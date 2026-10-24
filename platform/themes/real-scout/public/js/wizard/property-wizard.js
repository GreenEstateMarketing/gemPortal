/*
    Path in theme: platform/themes/real-scout/public/js/wizard/property-wizard.js
    Served copy:   public/themes/real-scout/js/wizard/property-wizard.js (keep both in sync manually)

    Vanilla JS, no framework - same style as new-home-page/how-it-works.js.
    Handles: per-step AJAX save + full-page navigation to the next step,
    the image/document upload widgets (AJAX-upload-then-store-URL, same
    pattern the old form's image gallery already used), the facility rows
    repeater, and the guest login/signup gate at Review time.
*/
(function () {
    'use strict';

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) {
            return meta.getAttribute('content');
        }
        var root = document.querySelector('.property-wizard');
        return root ? root.getAttribute('data-csrf-token') : '';
    }

    // Client-side pre-checks for the property-photo uploader only - a UX
    // convenience that mirrors PropertyImageValidationService on the server,
    // which remains authoritative (catches anything that bypasses JS, e.g.
    // no-JS submissions or direct API calls).
    var ALLOWED_IMAGE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    var MAX_IMAGE_SIZE_BYTES = 5 * 1024 * 1024;
    var MIN_IMAGE_WIDTH = 1024;
    var MIN_IMAGE_HEIGHT = 768;
    var MAX_IMAGE_ASPECT_RATIO = 3;

    function validateImageFile(file) {
        if (ALLOWED_IMAGE_MIME_TYPES.indexOf(file.type) === -1) {
            return 'Please upload a photo in JPG, PNG, or WEBP format.';
        }
        if (file.size > MAX_IMAGE_SIZE_BYTES) {
            return 'This image is too large. Please upload a photo smaller than 5MB.';
        }
        return null;
    }

    // Probes actual pixel dimensions by loading the file into an off-DOM
    // Image() - the only reliable way to read this client-side before upload.
    function checkImageDimensions(file, callback) {
        var url = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
            var width = img.naturalWidth;
            var height = img.naturalHeight;
            URL.revokeObjectURL(url);

            if (width < MIN_IMAGE_WIDTH || height < MIN_IMAGE_HEIGHT) {
                callback('This image resolution is too low. Please upload an image with a minimum resolution of ' + MIN_IMAGE_WIDTH + ' × ' + MIN_IMAGE_HEIGHT + ' pixels.');
                return;
            }

            var ratio = width / height;
            if (ratio > MAX_IMAGE_ASPECT_RATIO || ratio < (1 / MAX_IMAGE_ASPECT_RATIO)) {
                callback('This image has an unusual shape and can\'t be used. Please upload a normal photo of the property.');
                return;
            }

            callback(null);
        };
        img.onerror = function () {
            URL.revokeObjectURL(url);
            callback('This image appears to be corrupted or unsupported. Please upload a different photo.');
        };
        img.src = url;
    }

    function showError(form, field, message) {
        var el = form.querySelector('[data-error-for="' + field + '"]');
        if (el) {
            el.textContent = message || '';
        }
    }

    function clearErrors(form) {
        var errors = form.querySelectorAll('[data-error-for]');
        errors.forEach(function (el) {
            el.textContent = '';
        });
    }

    function applyErrors(form, errors) {
        clearErrors(form);
        Object.keys(errors || {}).forEach(function (field) {
            var messages = errors[field];
            showError(form, field, Array.isArray(messages) ? messages[0] : messages);
        });
    }

    function setLoading(button, loading) {
        if (!button) {
            return;
        }
        button.disabled = loading;
        if (loading) {
            button.dataset.originalText = button.textContent;
            button.textContent = button.dataset.loadingText || 'Saving...';
        } else if (button.dataset.originalText) {
            button.textContent = button.dataset.originalText;
        }
    }

    function postJson(url, payload) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        }).then(function (response) {
            return response.json().then(function (json) {
                return { ok: response.ok, status: response.status, json: json };
            });
        });
    }

    function collectFormData(form) {
        var data = {};
        var elements = form.querySelectorAll('[data-field]');

        elements.forEach(function (el) {
            var name = el.getAttribute('data-field');
            var type = el.getAttribute('type');

            if (type === 'checkbox') {
                if (el.dataset.multiple === 'true') {
                    if (!data[name]) {
                        data[name] = [];
                    }
                    if (el.checked) {
                        data[name].push(el.value);
                    }
                } else {
                    data[name] = el.checked;
                }
                return;
            }

            if (type === 'radio') {
                // Every radio in the group shares the same data-field name,
                // so only the checked one should ever set it - otherwise
                // whichever radio happens to appear last in the DOM always
                // wins, regardless of what the user actually picked.
                if (el.checked) {
                    data[name] = el.value;
                }
                return;
            }

            data[name] = el.value;
        });

        return data;
    }

    function collectFacilities(form) {
        var rows = form.querySelectorAll('[data-facility-row]');
        var facilities = [];

        rows.forEach(function (row) {
            var id = row.querySelector('[data-facility-id]').value;
            var distance = row.querySelector('[data-facility-distance]').value;
            if (id) {
                facilities.push({ id: id, distance: distance });
            }
        });

        return facilities;
    }

    // The site-wide area unit (used to display every property's square
    // footage, e.g. Property::square_text) lives in the 'real_estate_square_unit'
    // setting and is normally changed via the homepage's "Change Area Unit"
    // modal (GET ajax/area_unit_update). This just fires that same endpoint
    // when the wizard's own unit dropdown changes, so the two stay in sync -
    // no page reload needed here since nothing on this page displays
    // square_text live.
    var AREA_UNIT_ASCII_TO_DISPLAY = {
        ft2: 'ft²',
        m2: 'm²',
        marla: 'marla',
        yards: 'yards',
        kanal: 'kanal'
    };

    function initAreaUnitSync(root) {
        var select = root.querySelector('[data-field="area_units"]');
        var squareInput = root.querySelector('[data-field="square"]');
        if (!select) {
            return;
        }

        var factors = {};
        try {
            factors = JSON.parse(select.getAttribute('data-area-factors') || '{}');
        } catch (e) {
            factors = {};
        }

        select.addEventListener('change', function () {
            // Convert the entered area value right away so it keeps
            // representing the same real-world size instead of being
            // silently relabeled - e.g. 20 marla shouldn't turn into
            // "20 yards" just because the unit dropdown changed.
            var previousUnit = select.getAttribute('data-previous-unit');
            var oldFactor = factors[previousUnit];
            var newFactor = factors[select.value];
            var currentValue = squareInput ? parseFloat(squareInput.value) : NaN;

            if (squareInput && oldFactor && newFactor && !isNaN(currentValue)) {
                var sqFt = currentValue * oldFactor;
                squareInput.value = Math.round((sqFt / newFactor) * 100) / 100;
            }

            select.setAttribute('data-previous-unit', select.value);

            var unit = AREA_UNIT_ASCII_TO_DISPLAY[select.value];
            if (!unit) {
                return;
            }

            // Absolute path, not relative - this page can be served from
            // several different prefixes (/member/..., /account/...,
            // /admin/...), and the route itself lives at the site root.
            fetch('/ajax/area_unit_update?area_unit=' + encodeURIComponent(unit), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).catch(function () {
                // Non-critical - the property's own square value still
                // saves correctly either way, this only syncs the
                // site-wide display preference.
            });
        });
    }

    function initFacilityRepeater(root) {
        var container = root.querySelector('[data-facility-rows]');
        var addButton = root.querySelector('[data-facility-add]');

        if (!container || !addButton) {
            return;
        }

        var facilityOptions = [];
        try {
            facilityOptions = JSON.parse(container.getAttribute('data-facility-options') || '[]');
        } catch (e) {
            facilityOptions = [];
        }

        // Built via plain DOM calls rather than a <template> element - this
        // page's Vue-based header/search components compile the whole page
        // as a template on mount, which silently strips out raw <template>
        // tags (and the <script> that used to clone one here), leaving the
        // "Add Nearby Facility" button with nothing to clone.
        function buildFacilityRow() {
            var row = document.createElement('div');
            row.className = 'wizard-facility-row';
            row.setAttribute('data-facility-row', '');

            var select = document.createElement('select');
            select.className = 'wizard-select';
            select.setAttribute('data-facility-id', '');

            var placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Select facility';
            select.appendChild(placeholder);

            facilityOptions.forEach(function (option) {
                var opt = document.createElement('option');
                opt.value = option.id;
                opt.textContent = option.name;
                select.appendChild(opt);
            });

            var distanceInput = document.createElement('input');
            distanceInput.type = 'text';
            distanceInput.className = 'wizard-input';
            distanceInput.setAttribute('data-facility-distance', '');
            distanceInput.placeholder = 'e.g. 2km';

            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'wizard-btn wizard-btn--danger';
            removeBtn.setAttribute('data-facility-remove', '');
            removeBtn.textContent = 'Remove';

            row.appendChild(select);
            row.appendChild(distanceInput);
            row.appendChild(removeBtn);

            return row;
        }

        // Once a facility is picked in one row, it shouldn't be pickable
        // again in another row - disable (not remove) its <option> in every
        // other row's select so its label/position stays visible if the
        // user later frees it up again.
        function syncFacilityOptions() {
            var selects = Array.prototype.slice.call(container.querySelectorAll('[data-facility-id]'));
            var selectedElsewhere = selects.map(function (select) {
                return select.value;
            });

            selects.forEach(function (select) {
                Array.prototype.forEach.call(select.options, function (option) {
                    if (!option.value) {
                        return;
                    }
                    var pickedInAnotherRow = selectedElsewhere.indexOf(option.value) !== -1 && option.value !== select.value;
                    option.disabled = pickedInAnotherRow;
                });
            });
        }

        addButton.addEventListener('click', function () {
            container.appendChild(buildFacilityRow());
            syncFacilityOptions();
        });

        container.addEventListener('click', function (event) {
            if (event.target.matches('[data-facility-remove]')) {
                event.target.closest('[data-facility-row]').remove();
                syncFacilityOptions();
            }
        });

        container.addEventListener('change', function (event) {
            // Genuine user interaction with a row's select/distance marks it
            // "manual" so the map script's auto-fill (see location.blade.php)
            // never silently overwrites it later - programmatic updates from
            // auto-fill set .value directly and never dispatch 'change', so
            // this only ever fires for something the user actually did.
            if (event.target.matches('[data-facility-id], [data-facility-distance]')) {
                event.target.closest('[data-facility-row]').setAttribute('data-facility-manual', 'true');
            }
            if (event.target.matches('[data-facility-id]')) {
                syncFacilityOptions();
            }
        });

        // Rows already in the DOM at load time are a resumed draft's
        // previously-saved facilities (or server-rendered on a validation
        // error re-render) - treat them as manual from the start so the
        // very first auto-fill run can add new facilities but never
        // silently touches one that already existed before this feature.
        Array.prototype.slice.call(container.querySelectorAll('[data-facility-row]')).forEach(function (row) {
            row.setAttribute('data-facility-manual', 'true');
        });

        function findFacilityRowById(idStr) {
            var rows = Array.prototype.slice.call(container.querySelectorAll('[data-facility-row]'));
            return rows.filter(function (row) {
                return row.querySelector('[data-facility-id]').value === idStr;
            })[0];
        }

        // Handoff API for location.blade.php's map script: it runs later
        // (on window 'load', after this DOMContentLoaded setup has already
        // finished), and calls this with [{id, distance}, ...] once it has
        // looked up the nearest Google Place for each mapped facility.
        window.PropertyWizardFacilities = {
            applyAutoFill: function (matches) {
                (matches || []).forEach(function (match) {
                    var idStr = String(match.id);
                    var row = findFacilityRowById(idStr);

                    if (row) {
                        if (row.getAttribute('data-facility-manual') !== 'true') {
                            row.querySelector('[data-facility-distance]').value = match.distance;
                            row.setAttribute('data-facility-auto', 'true');
                        }
                        return;
                    }

                    row = buildFacilityRow();
                    row.setAttribute('data-facility-auto', 'true');
                    row.querySelector('[data-facility-id]').value = idStr;
                    row.querySelector('[data-facility-distance]').value = match.distance;
                    container.appendChild(row);
                });

                syncFacilityOptions();
            }
        };

        syncFacilityOptions();
    }

    function fileIconFor(name) {
        var ext = (name || '').split('.').pop().toLowerCase();
        if (ext === 'pdf') {
            return 'fa-file-pdf';
        }
        if (ext === 'doc' || ext === 'docx') {
            return 'fa-file-word';
        }
        if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].indexOf(ext) !== -1) {
            return 'fa-file-image';
        }
        return 'fa-file-alt';
    }

    function initUploader(root, options) {
        var widget = root.querySelector('[data-uploader="' + options.name + '"]');
        if (!widget) {
            return;
        }

        var dropzone = widget.querySelector('.wizard-upload');
        var input = widget.querySelector('input[type="file"]');
        var thumbs = widget.querySelector('[data-uploader-thumbs]');
        // .wizard-thumbs defaults to a square-tile photo grid - document
        // rows need a plain stacked list instead so their filename/Remove
        // button aren't squeezed into a ~110px grid cell.
        if (options.kind !== 'image') {
            thumbs.classList.add('wizard-thumbs--documents');
        }
        var hidden = widget.querySelector('[data-uploader-value]');
        var counter = widget.parentNode.querySelector('[data-uploader-count="' + options.name + '"]');
        var items = [];
        // Files currently mid-upload - counted toward options.max alongside
        // items.length so a burst of drops can't sneak past the limit
        // before any of them have actually landed in `items`.
        var reserved = 0;
        // Photos currently shown with a validation problem (client- or
        // server-side) - Save & Continue stays disabled the whole time any
        // of these exist, until the user removes or replaces them. Images
        // only; the document uploader doesn't use this.
        var invalidCount = 0;
        var submitBtn = null;
        if (options.kind === 'image') {
            var stepForm = widget.closest('form');
            submitBtn = stepForm ? stepForm.querySelector('[data-step-submit]') : null;
        }

        try {
            items = JSON.parse(hidden.value || '[]');
        } catch (e) {
            items = [];
        }

        function persist() {
            hidden.value = JSON.stringify(items);
        }

        function updateCount() {
            if (counter && options.max) {
                counter.textContent = '(' + items.length + ' of ' + options.max + ' added)';
            }
        }

        // Keeps the submit button disabled and the shared error message in
        // sync with how many photos currently have an unresolved problem.
        function onInvalidCountChanged() {
            if (submitBtn) {
                submitBtn.disabled = invalidCount > 0;
            }
            if (invalidCount > 0) {
                showError(root, options.name, invalidCount === 1
                    ? 'One photo has a problem - hover over it for details, then remove or replace it to continue.'
                    : invalidCount + ' photos have a problem - hover over each for details, then remove or replace them to continue.');
            } else {
                showError(root, options.name, '');
            }
        }

        function revokePreview(wrap) {
            var url = wrap.getAttribute('data-object-url');
            if (url) {
                URL.revokeObjectURL(url);
                wrap.removeAttribute('data-object-url');
            }
        }

        // beforeNode lets a finished upload drop its real thumb exactly where
        // its "uploading" placeholder was (see renderPlaceholder below),
        // rather than at the end of the grid - without that, two files
        // dropped together could land out of order if the second one's
        // upload happens to finish first.
        function renderThumb(item, beforeNode) {
            if (options.kind === 'image') {
                var wrap = document.createElement('div');
                wrap.className = 'wizard-thumb';
                // item.url is the raw relative storage path (what actually
                // gets saved) - it isn't browsable on its own, so display
                // uses item.full_url when we have it.
                wrap.innerHTML = '<img src="' + (item.full_url || item.url) + '" alt=""><button type="button" class="wizard-thumb__remove" data-remove-url="' + item.url + '" title="Remove">&times;</button>';
                thumbs.insertBefore(wrap, beforeNode || null);
            } else {
                var row = document.createElement('div');
                row.className = 'wizard-doc-item';

                var icon = document.createElement('div');
                icon.className = 'wizard-doc-item__icon';
                icon.innerHTML = '<i class="fas ' + fileIconFor(item.name) + '"></i>';

                var body = document.createElement('div');
                body.className = 'wizard-doc-item__body';
                var name = document.createElement('span');
                name.className = 'wizard-doc-item__name';
                name.textContent = item.name || '';
                var meta = document.createElement('span');
                meta.className = 'wizard-doc-item__meta';
                meta.textContent = 'Uploaded';
                body.appendChild(name);
                body.appendChild(meta);

                var removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'wizard-doc-item__remove';
                removeBtn.setAttribute('data-remove-url', item.url);
                removeBtn.setAttribute('title', 'Remove');
                removeBtn.innerHTML = '&times;';

                row.appendChild(icon);
                row.appendChild(body);
                row.appendChild(removeBtn);
                thumbs.insertBefore(row, beforeNode || null);
            }
        }

        // Shown the instant a document is picked/dropped, before its upload
        // request even starts. Images use renderImagePreview() below instead -
        // they preview the actual photo rather than a bare spinner, since a
        // photo that turns out to be invalid still needs to be recognizable.
        function renderPlaceholder(file) {
            var row = document.createElement('div');
            row.className = 'wizard-doc-item wizard-doc-item--uploading';

            var icon = document.createElement('div');
            icon.className = 'wizard-doc-item__icon';
            icon.innerHTML = '<div class="wizard-spinner"></div>';

            var body = document.createElement('div');
            body.className = 'wizard-doc-item__body';
            var name = document.createElement('span');
            name.className = 'wizard-doc-item__name';
            name.textContent = file.name || '';
            var meta = document.createElement('span');
            meta.className = 'wizard-doc-item__meta';
            meta.textContent = 'Uploading...';
            body.appendChild(name);
            body.appendChild(meta);

            row.appendChild(icon);
            row.appendChild(body);
            thumbs.appendChild(row);
            return row;
        }

        // Previews the exact file the user picked, immediately - before any
        // validation or upload has happened - so a photo that turns out to
        // be too small/corrupted/etc. is still clearly the one they chose,
        // not a mystery blank tile. Stays in this "pending" state (dimmed,
        // spinner on top) until checkImageDimensions()/the upload resolves
        // it one way or the other.
        function renderImagePreview(file) {
            var wrap = document.createElement('div');
            wrap.className = 'wizard-thumb wizard-thumb--pending';
            var objectUrl = URL.createObjectURL(file);
            wrap.setAttribute('data-object-url', objectUrl);
            wrap.innerHTML = '<img src="' + objectUrl + '" alt="">' +
                '<div class="wizard-thumb__overlay"><div class="wizard-spinner"></div></div>' +
                '<button type="button" class="wizard-thumb__remove" title="Remove">&times;</button>';
            thumbs.appendChild(wrap);
            return wrap;
        }

        // Marks a previewed photo as unusable in place - red border + a
        // badge carrying the specific reason (shown on hover) - and keeps
        // Save & Continue disabled until it's removed or replaced.
        function markThumbInvalid(wrap, message) {
            if (!wrap.isConnected) {
                // Already removed by the user while validation/upload was
                // still in flight - nothing to mark, and no slot to free.
                return;
            }
            wrap.classList.remove('wizard-thumb--pending');
            wrap.classList.add('wizard-thumb--invalid');
            var overlay = wrap.querySelector('.wizard-thumb__overlay');
            if (overlay) {
                overlay.remove();
            }
            wrap.title = message;
            var badge = document.createElement('div');
            badge.className = 'wizard-thumb__badge';
            badge.title = message;
            badge.innerHTML = '<i class="fas fa-exclamation"></i>';
            wrap.appendChild(badge);
            invalidCount += 1;
            onInvalidCountChanged();
        }

        // Converts a pending preview into a normal, persisted thumb once the
        // upload has actually succeeded server-side.
        function resolveThumbAsUploaded(wrap, file, json) {
            var url = (json.data && (json.data.url || json.data.src)) || '';
            var fullUrl = (json.data && (json.data.full_url || json.data.url)) || url;
            var item = { url: url, full_url: fullUrl, name: file.name };
            items.push(item);
            persist();

            if (!wrap.isConnected) {
                // User removed the preview before the upload finished - the
                // file is already saved server-side (same as removing an
                // already-uploaded thumb doesn't delete it there either),
                // but there's no tile left to update.
                updateCount();
                return;
            }

            revokePreview(wrap);
            wrap.classList.remove('wizard-thumb--pending');
            var overlay = wrap.querySelector('.wizard-thumb__overlay');
            if (overlay) {
                overlay.remove();
            }
            var img = wrap.querySelector('img');
            if (img) {
                img.src = fullUrl || url;
            }
            var removeBtn = wrap.querySelector('.wizard-thumb__remove');
            if (removeBtn) {
                removeBtn.setAttribute('data-remove-url', url);
            }
            updateCount();
        }

        items.forEach(renderThumb);
        updateCount();
        onInvalidCountChanged();

        thumbs.addEventListener('click', function (event) {
            var btn = event.target.closest('.wizard-thumb__remove, .wizard-doc-item__remove');
            if (!btn) {
                return;
            }
            var wrap = btn.closest(options.kind === 'image' ? '.wizard-thumb' : '.wizard-doc-item');
            var url = btn.getAttribute('data-remove-url');
            if (url) {
                items = items.filter(function (item) {
                    return item.url !== url;
                });
                persist();
                updateCount();
            } else if (wrap && wrap.classList.contains('wizard-thumb--invalid')) {
                // A still-invalid preview being dismissed - frees up its slot
                // and may re-enable Save & Continue.
                invalidCount = Math.max(0, invalidCount - 1);
                onInvalidCountChanged();
            }
            if (wrap) {
                revokePreview(wrap);
                wrap.remove();
            }
        });

        dropzone.addEventListener('click', function () {
            input.click();
        });

        ['dragover', 'dragleave', 'drop'].forEach(function (evt) {
            dropzone.addEventListener(evt, function (e) {
                e.preventDefault();
                dropzone.classList.toggle('wizard-upload--dragover', evt === 'dragover');
            });
        });

        dropzone.addEventListener('drop', function (e) {
            uploadFiles(e.dataTransfer.files);
        });

        input.addEventListener('change', function () {
            uploadFiles(input.files);
            input.value = '';
        });

        function uploadFiles(fileList) {
            var files = Array.prototype.slice.call(fileList);
            var itemLabel = options.kind === 'image' ? 'photo' : 'document';

            if (options.max) {
                // Invalid previews still occupy a grid slot until the user
                // deals with them, so they count against the limit too -
                // otherwise a user could keep adding past max by leaving
                // rejected photos sitting there.
                var remaining = options.max - (items.length + reserved + invalidCount);
                if (remaining <= 0) {
                    window.alert(options.max === 1
                        ? 'Only one ' + itemLabel + ' can be added here - remove the existing one first.'
                        : 'You can add up to ' + options.max + ' ' + itemLabel + 's.');
                    return;
                }
                if (files.length > remaining) {
                    window.alert('Only ' + remaining + ' more ' + itemLabel + '(s) can be added (max ' + options.max + ' total) - the rest were skipped.');
                    files = files.slice(0, remaining);
                }
            }

            function uploadDocument(file) {
                reserved += 1;
                var placeholder = renderPlaceholder(file);
                var formData = new FormData();
                formData.append('file[]', file);
                // Tells the upload endpoint not to apply its images-only
                // validation - documents can legitimately be PDFs, Word
                // files, etc. per whatever the category's document type
                // allows (see the accept="" attribute on this input).
                formData.append('type', 'document');

                fetch(options.uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                }).then(function (response) {
                    var ok = response.ok;
                    return response.json().catch(function () {
                        return null;
                    }).then(function (json) {
                        return { ok: ok, json: json };
                    });
                }).then(function (result) {
                    reserved -= 1;
                    var json = result.json;
                    var url = json && json.data && (json.data.url || json.data.src);
                    if (!result.ok || !json || json.error || !url) {
                        placeholder.remove();
                        window.alert((json && json.message) || 'Upload failed');
                        return;
                    }
                    var fullUrl = (json.data && json.data.full_url) || url;
                    var item = { url: url, full_url: fullUrl, name: file.name };
                    items.push(item);
                    persist();
                    renderThumb(item, placeholder);
                    placeholder.remove();
                    updateCount();
                }).catch(function () {
                    reserved -= 1;
                    placeholder.remove();
                    window.alert('Upload failed, please try again.');
                });
            }

            // reserved is incremented once, right before the dimension check,
            // by the caller below - this function owns the matching
            // decrement, on whichever path it resolves through.
            function uploadImage(file, wrap) {
                var formData = new FormData();
                formData.append('file[]', file);

                fetch(options.uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                }).then(function (response) {
                    // response.ok has to be captured here - it's not available
                    // any more once we're inside the body-parsing promise below.
                    var ok = response.ok;
                    return response.json().catch(function () {
                        // Not JSON at all (e.g. a raw PHP error page) - treat
                        // the same as a malformed app response, below.
                        return null;
                    }).then(function (json) {
                        return { ok: ok, json: json };
                    });
                }).then(function (result) {
                    reserved -= 1;
                    var json = result.json;
                    var url = json && json.data && (json.data.url || json.data.src);
                    // A non-2xx status, a response that isn't our app's
                    // {error, data, message} shape, or one missing a usable
                    // URL, is never treated as success - without this, an
                    // unexpected server error (a crash, a proxy error page,
                    // anything that doesn't match the expected shape) would
                    // silently fall through as "success" with a blank URL,
                    // leaving a broken image counted as uploaded with no
                    // visible explanation.
                    if (!result.ok || !json || json.error || !url) {
                        markThumbInvalid(wrap, (json && json.message) || 'Upload failed, please try again.');
                        return;
                    }
                    resolveThumbAsUploaded(wrap, file, json);
                }).catch(function () {
                    reserved -= 1;
                    markThumbInvalid(wrap, 'Upload failed, please try again.');
                });
            }

            files.forEach(function (file) {
                if (options.kind !== 'image') {
                    uploadDocument(file);
                    return;
                }

                var wrap = renderImagePreview(file);

                var syncError = validateImageFile(file);
                if (syncError) {
                    markThumbInvalid(wrap, syncError);
                    return;
                }

                reserved += 1;
                checkImageDimensions(file, function (dimensionError) {
                    if (dimensionError) {
                        reserved -= 1;
                        markThumbInvalid(wrap, dimensionError);
                        return;
                    }
                    uploadImage(file, wrap);
                });
            });
        }
    }

    function initStepForm(root) {
        var form = root.querySelector('[data-step-form]');
        if (!form) {
            return;
        }

        var submitBtn = form.querySelector('[data-step-submit]');

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearErrors(form);

            var payload = collectFormData(form);

            if (form.hasAttribute('data-collect-facilities')) {
                payload.facilities = collectFacilities(form);
            }

            var imagesHidden = form.querySelector('[data-uploader-value="images"]');
            if (imagesHidden) {
                var parsedImages = [];
                try {
                    parsedImages = JSON.parse(imagesHidden.value || '[]');
                } catch (e) {
                    parsedImages = [];
                }
                // Property::images is a flat array of relative storage paths
                // everywhere else in the app (property galleries, listing
                // cards, etc.) - submit plain URL strings here, not the
                // {url, full_url, name} objects the uploader tracks
                // internally for its own thumbnail rendering.
                payload.images = parsedImages.map(function (item) { return item && item.url ? item.url : item; });
            }

            // Documents come from one or more upload slots (one per
            // admin-configured document type, or a single generic one when
            // the category has none configured - see media.blade.php).
            // Merge them into one flat array, tagging each item with the
            // document_id its slot belongs to so the server can check which
            // required types were actually provided.
            var documentsPayload = [];
            var missingRequiredDocument = false;
            form.querySelectorAll('[data-uploader-value^="documents"]').forEach(function (hidden) {
                var documentId = hidden.getAttribute('data-document-id');
                var parsed = [];
                try {
                    parsed = JSON.parse(hidden.value || '[]');
                } catch (e) {
                    parsed = [];
                }

                if (documentId && hidden.getAttribute('data-document-required') === '1' && !parsed.length) {
                    showError(form, 'document_' + documentId, 'This document is required.');
                    missingRequiredDocument = true;
                }

                parsed.forEach(function (item) {
                    documentsPayload.push(documentId ? Object.assign({ document_id: documentId }, item) : item);
                });
            });
            payload.documents = documentsPayload;

            // Belt-and-suspenders alongside the disabled submit button in
            // onInvalidCountChanged() (property-wizard.js's initUploader) -
            // checks the live DOM rather than that closure's own counter, so
            // it still blocks submission even if the button's disabled state
            // somehow got out of sync.
            var invalidImageCount = form.querySelectorAll('[data-uploader="images"] .wizard-thumb--invalid').length;
            if (invalidImageCount > 0) {
                showError(form, 'images', invalidImageCount === 1
                    ? 'One photo has a problem - remove or replace it before continuing.'
                    : invalidImageCount + ' photos have a problem - remove or replace them before continuing.');
                return;
            }

            // Catch the empty-photos / missing-required-document cases
            // immediately, without a round trip - the server enforces the
            // same rules regardless.
            if (form.querySelector('[data-uploader="images"]') && !(payload.images && payload.images.length)) {
                showError(form, 'images', 'Please add at least 1 photo.');
                return;
            }

            if (missingRequiredDocument) {
                return;
            }

            var moderationSelect = form.querySelector('[data-field="moderation_status"]');
            if (moderationSelect && moderationSelect.value === 'rejected') {
                var reasonInput = form.querySelector('[data-field="reject_reason"]');
                if (reasonInput && !reasonInput.value.trim()) {
                    showError(form, 'reject_reason', 'Please provide a reason for rejecting this listing.');
                    return;
                }
            }

            setLoading(submitBtn, true);

            // Stays "Saving..." through to the actual page navigation on
            // success (not just until the response lands) - only reset it
            // on paths that keep the user on this page.
            postJson(form.getAttribute('action'), payload).then(function (result) {
                if (!result.ok) {
                    setLoading(submitBtn, false);
                    if (result.json && result.json.errors) {
                        applyErrors(form, result.json.errors);
                    } else {
                        window.alert((result.json && result.json.message) || 'Something went wrong.');
                    }
                    return;
                }

                window.location.href = result.json.next_url;
            }).catch(function () {
                setLoading(submitBtn, false);
                window.alert('Network error, please try again.');
            });
        });
    }

    function initFinalize(root) {
        var button = root.querySelector('[data-finalize-submit]');
        var finalizeUrl = root.getAttribute('data-finalize-url');
        var guestPanel = root.querySelector('[data-guest-auth]');

        if (!button) {
            return;
        }

        function doFinalize() {
            setLoading(button, true);

            postJson(finalizeUrl, {}).then(function (result) {
                if (result.json && result.json.require_auth && guestPanel) {
                    setLoading(button, false);
                    guestPanel.classList.add('wizard-guest-auth--visible');
                    guestPanel.style.display = 'block';
                    button.style.display = 'none';
                    guestPanel.scrollIntoView({ behavior: 'smooth' });
                    return;
                }

                if (!result.ok || !result.json.success) {
                    setLoading(button, false);
                    window.alert((result.json && result.json.message) || 'Unable to submit right now.');
                    return;
                }

                window.location.href = result.json.redirect_url;
            }).catch(function () {
                setLoading(button, false);
                window.alert('Network error, please try again.');
            });
        }

        button.addEventListener('click', doFinalize);
    }

    function initGuestAuth(root) {
        var panel = root.querySelector('[data-guest-auth]');
        if (!panel) {
            return;
        }

        var radios = panel.querySelectorAll('input[name="member_status"]');
        var existingFields = panel.querySelector('[data-existing-fields]');
        var newFields = panel.querySelector('[data-new-fields]');
        var submitLabel = panel.querySelector('[data-guest-auth-submit-label]');

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                var isNew = radio.value === 'new_user' && radio.checked;
                if (radio.checked) {
                    existingFields.style.display = radio.value === 'existing_user' ? 'block' : 'none';
                    newFields.style.display = isNew ? 'block' : 'none';
                    if (submitLabel) {
                        submitLabel.textContent = isNew ? 'Create Account' : 'Log In & Publish';
                    }
                }
            });
        });

        var submitBtn = panel.querySelector('[data-guest-auth-submit]');
        var authUrl = panel.getAttribute('data-authenticate-url');
        var authForm = panel.querySelector('[data-guest-auth-form]');
        var authSuccess = panel.querySelector('[data-guest-auth-success]');

        submitBtn.addEventListener('click', function () {
            clearErrors(panel);
            var payload = collectFormData(panel);

            setLoading(submitBtn, true);

            postJson(authUrl, payload).then(function (result) {
                if (!result.ok || !result.json.success) {
                    setLoading(submitBtn, false);
                    if (result.json && result.json.errors) {
                        applyErrors(panel, result.json.errors);
                    } else {
                        window.alert((result.json && result.json.message) || 'Unable to continue.');
                    }
                    return;
                }

                if (result.json.require_email_verification) {
                    if (authForm) {
                        authForm.style.display = 'none';
                    }
                    if (authSuccess) {
                        authSuccess.style.display = 'flex';
                    }
                    return;
                }

                window.location.href = result.json.redirect_url;
            }).catch(function () {
                setLoading(submitBtn, false);
                window.alert('Network error, please try again.');
            });
        });
    }

    /*
        Category / sub-category cascade + description-template auto-fill.

        Categories are a two-level tree (parent_id 0 = top category, else a
        sub-category of that parent), presented as two rows of buttons rather
        than <select> dropdowns. The form only ever submits one
        `category_id` (the sub-category, or the parent itself when it has no
        children) via the hidden #wizard-category-id input; the buttons are
        purely a browsing aid over that one value.

        The server pre-selects the first category/sub-category (and 'sale')
        for a brand new draft, so the buttons and hidden fields already agree
        on load - this module only needs to react to clicks from there.

        Once a (sub-)category is chosen, /api/v1/get_template looks up a
        description_template row for that category id. Its `detail` text
        contains $a-$g placeholders (see setTemplateVariables' `map`) that get
        substituted from the current form values, mirroring the substitution
        the old admin form already did - a placeholder is left untouched
        (not blanked) when its source field is still empty.
    */
    function initCategoryAndTemplate(root) {
        var form = root.querySelector('[data-step-form][data-category-tree]');
        if (!form) {
            return;
        }

        var tree = [];
        try {
            tree = JSON.parse(form.getAttribute('data-category-tree') || '[]');
        } catch (e) {
            tree = [];
        }

        var categoryRow = form.querySelector('[data-category-row]');
        var subcategoryRow = form.querySelector('[data-subcategory-row]');
        var categoryIdInput = document.getElementById('wizard-category-id');
        var categoryNameInput = document.getElementById('wizard-category-name');
        var templateInput = document.getElementById('wizard-template-description');
        var descriptionField = document.getElementById('wizard-description');
        var typeInput = document.getElementById('wizard-type');

        if (!categoryRow || !subcategoryRow || !categoryIdInput) {
            return;
        }

        // Gated by the "wizard_description_editable" feature flag (see
        // FeatureFlag::WIZARD_DESCRIPTION_EDITABLE). Off by default, in which
        // case the field stays read-only and always gets overwritten below -
        // identical to the original behaviour. When on, the field is a real
        // textarea and descriptionUserEdited tracks whether its text should
        // be left alone, so incidental field changes (beds/baths/etc.) - and
        // the on-load re-apply below - stop clobbering it. Picking a
        // category is a deliberate "regenerate" action and always wins.
        var isDescriptionEditable = !!descriptionField && descriptionField.dataset.descriptionEditable === '1';
        // Any text already in the field on load (a previously saved
        // description, edited or not) counts as already "finalised" - only
        // an explicit category pick should regenerate it, never just
        // reopening the step. A brand new draft's empty field is still fair
        // game so it gets its initial auto-filled template.
        var descriptionUserEdited = isDescriptionEditable && !!descriptionField && descriptionField.value.trim() !== '';
        if (descriptionField && isDescriptionEditable) {
            descriptionField.addEventListener('input', function () {
                descriptionUserEdited = true;
            });
        }

        // Which of the two type-tie flags a category needs to have set to
        // be eligible under the currently active Listing Type.
        function currentType() {
            return typeInput && typeInput.value === 'rent' ? 'rent' : 'sale';
        }

        function flagFor(type) {
            return type === 'rent' ? 'for_rent' : 'for_sale';
        }

        function childrenOf(parentId) {
            var flag = flagFor(currentType());
            return tree.filter(function (c) {
                return c.parent_id === parseInt(parentId, 10) && c[flag];
            });
        }

        function topCategories() {
            var flag = flagFor(currentType());
            return tree.filter(function (c) {
                return c.parent_id === 0 && c[flag];
            });
        }

        function findById(id) {
            return tree.filter(function (c) {
                return String(c.id) === String(id);
            })[0];
        }

        function markActive(row, value) {
            row.querySelectorAll('button').forEach(function (btn) {
                var btnValue = btn.getAttribute('data-category-option') || btn.getAttribute('data-subcategory-option');
                btn.classList.toggle('wizard-chip-btn--active', String(btnValue) === String(value));
            });
        }

        function setActiveCategory(id, name, triggerFetch) {
            categoryIdInput.value = id || '';
            categoryNameInput.value = name || '';
            if (triggerFetch) {
                fetchTemplate(id);
            }
        }

        // Rebuilds the sub-category button row for the given parent and, by
        // default, auto-picks its first child (matching the "first category
        // and sub-category selected by default" behaviour when the user
        // actively switches category).
        function populateSubcategories(parentId, selectSubId, triggerFetch) {
            var children = childrenOf(parentId);
            subcategoryRow.innerHTML = '';

            children.forEach(function (child) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'wizard-chip-btn';
                btn.setAttribute('data-subcategory-option', child.id);
                btn.textContent = child.name;
                subcategoryRow.appendChild(btn);
            });

            if (children.length === 0) {
                var parent = findById(parentId);
                setActiveCategory(parentId, parent ? parent.name : '', triggerFetch);
                return;
            }

            // A selectSubId carried over from before a Listing Type switch
            // may no longer be one of this (now type-filtered) parent's
            // children - fall back to the first valid one instead of
            // writing a sub-category id that's invalid for the active type.
            var stillValid = children.some(function (c) {
                return String(c.id) === String(selectSubId);
            });
            var subIdToSelect = stillValid ? selectSubId : children[0].id;
            markActive(subcategoryRow, subIdToSelect);
            var selected = findById(subIdToSelect);
            setActiveCategory(subIdToSelect, selected ? selected.name : '', triggerFetch);
        }

        // Rebuilds the top-level category chip row for the currently active
        // Listing Type and returns whichever category id ends up active -
        // either the requested one (if still valid for this type) or the
        // first available category otherwise.
        function populateTopCategories(selectParentId) {
            var tops = topCategories();
            categoryRow.innerHTML = '';

            tops.forEach(function (cat) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'wizard-chip-btn';
                btn.setAttribute('data-category-option', cat.id);
                btn.textContent = cat.name;
                categoryRow.appendChild(btn);
            });

            if (tops.length === 0) {
                return null;
            }

            var stillValid = tops.some(function (cat) {
                return String(cat.id) === String(selectParentId);
            });
            var parentIdToSelect = stillValid ? selectParentId : tops[0].id;
            markActive(categoryRow, parentIdToSelect);
            return parentIdToSelect;
        }

        categoryRow.addEventListener('click', function (event) {
            var btn = event.target.closest('[data-category-option]');
            if (!btn) {
                return;
            }
            markActive(categoryRow, btn.getAttribute('data-category-option'));
            populateSubcategories(btn.getAttribute('data-category-option'), null, true);
        });

        // Switching Listing Type (Sale <-> Rent) changes which categories
        // and sub-categories are eligible at all - re-render both rows for
        // the newly active type, keeping the current selection if it's
        // still valid and otherwise falling back to the first valid one
        // (same convention used for a brand-new draft with nothing chosen
        // yet). This only ever runs on an explicit user click on the type
        // toggle (see initTypeToggle's 'change' dispatch), never on initial
        // page load, so an existing property's saved category still
        // displays correctly on open even if it predates a later flag
        // change - it only gets re-evaluated if the user actively flips
        // the toggle during this editing session.
        if (typeInput) {
            typeInput.addEventListener('change', function () {
                var activeParentBtn = categoryRow.querySelector('.wizard-chip-btn--active');
                var activeSubBtn = subcategoryRow.querySelector('.wizard-chip-btn--active');
                var currentParentId = activeParentBtn ? activeParentBtn.getAttribute('data-category-option') : null;
                var currentSubId = activeSubBtn ? activeSubBtn.getAttribute('data-subcategory-option') : null;

                var newParentId = populateTopCategories(currentParentId);
                if (newParentId === null) {
                    subcategoryRow.innerHTML = '';
                    setActiveCategory('', '', true);
                    return;
                }

                var parentChanged = String(newParentId) !== String(currentParentId);
                populateSubcategories(newParentId, parentChanged ? null : currentSubId, true);
            });
        }

        subcategoryRow.addEventListener('click', function (event) {
            var btn = event.target.closest('[data-subcategory-option]');
            if (!btn) {
                return;
            }
            markActive(subcategoryRow, btn.getAttribute('data-subcategory-option'));
            var selected = findById(btn.getAttribute('data-subcategory-option'));
            setActiveCategory(btn.getAttribute('data-subcategory-option'), selected ? selected.name : '', true);
        });

        function fetchTemplate(categoryId) {
            if (!categoryId || !templateInput) {
                return;
            }

            fetch('/api/v1/get_template?category_id=' + encodeURIComponent(categoryId), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (r) {
                return r.json();
            }).then(function (json) {
                if (json.status && json.html && json.html.detail) {
                    templateInput.value = json.html.detail;
                    applyTemplatePlaceholders({ force: true });
                } else if (descriptionField) {
                    templateInput.value = '';
                    descriptionField.value = '';
                }
            }).catch(function () {
                // Leave whatever description text is already there.
            });
        }

        function applyTemplatePlaceholders(opts) {
            opts = opts || {};
            if (!templateInput || !templateInput.value || !descriptionField) {
                return;
            }

            // Once the user has typed a manual edit, only an explicit
            // category/sub-category selection (force: true) is allowed to
            // regenerate and overwrite it - incidental field changes below
            // leave their edit alone.
            if (isDescriptionEditable && descriptionUserEdited && !opts.force) {
                return;
            }

            var map = {
                '$a': document.getElementById('wizard-number-bedroom'),
                '$b': document.getElementById('wizard-number-bathroom'),
                '$c': document.getElementById('wizard-square'),
                '$d': document.getElementById('wizard-type'),
                '$e': document.getElementById('wizard-area-units'),
                '$f': categoryNameInput,
                '$g': document.getElementById('wizard-number-floor')
            };

            var text = templateInput.value;
            Object.keys(map).forEach(function (placeholder) {
                var el = map[placeholder];
                var value = el ? el.value : '';
                if (value) {
                    text = text.split(placeholder).join(value);
                }
            });

            descriptionField.value = text;
            if (opts.force) {
                descriptionUserEdited = false;
            }
        }

        ['wizard-number-bedroom', 'wizard-number-bathroom', 'wizard-square', 'wizard-type', 'wizard-area-units', 'wizard-number-floor'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', applyTemplatePlaceholders);
            }
        });

        // The sub-category row and hidden category fields are already
        // correctly rendered server-side on load (including the "first
        // category/sub-category" default for a brand new draft), so there's
        // nothing to pre-fill here beyond re-running the description
        // substitution below.

        // When not editable (the default), the description field is
        // entirely generated from the template + the fields above, so
        // re-apply the substitution on load too (values may have changed
        // since it was last saved). When editable, this still re-applies on
        // a fresh page load since descriptionUserEdited starts false.
        if (templateInput && templateInput.value) {
            applyTemplatePlaceholders();
        }
    }

    // "Price Unit" (e.g. /month) only makes sense for rentals.
    function initRentOnlyFields(root) {
        var typeSelect = document.getElementById('wizard-type');
        var rentFields = root.querySelectorAll('[data-rent-only-field]');

        if (!typeSelect || !rentFields.length) {
            return;
        }

        function sync() {
            // Only ever toggle visibility here - clearing the input's value
            // used to run alongside it, which meant switching the Listing
            // Type toggle away from "For Rent" (even just to explore it,
            // with no intent to actually change the listing) silently and
            // permanently erased whatever had been typed into Price Unit
            // before the user ever got to "Save & Continue". The field
            // stays merely hidden, not destroyed, while the type is
            // something other than "rent".
            var isRent = typeSelect.value === 'rent';
            rentFields.forEach(function (field) {
                field.style.display = isRent ? '' : 'none';
            });
        }

        typeSelect.addEventListener('change', sync);
        sync();
    }

    // The rejection reason box only makes sense (and is only required)
    // while the moderation status is actually "rejected".
    function initModerationStatus(root) {
        var statusSelect = document.getElementById('wizard-moderation-status');
        var reasonField = root.querySelector('[data-reject-reason-field]');

        if (!statusSelect || !reasonField) {
            return;
        }

        function sync() {
            var isRejected = statusSelect.value === 'rejected';
            reasonField.style.display = isRejected ? '' : 'none';
            if (!isRejected) {
                var textarea = reasonField.querySelector('[data-field]');
                if (textarea) {
                    textarea.value = '';
                }
            }
        }

        statusSelect.addEventListener('change', sync);
        sync();
    }

    // Wraps the hidden <select id="wizard-agent-select"> with a search-
    // filtered dropdown (button + panel), so long agent lists - e.g. every
    // agent in the system for an admin, who isn't limited to this
    // property's location coverage - stay easy to navigate. The native
    // select remains the single source of truth for data-field="agent_id";
    // this only drives its value and dispatches 'change' so the existing
    // agent-card render (and the generic collectFormData reader) keep
    // working unmodified. Not rendered at all when the field is disabled
    // (agent's own listing, or a locked/approved property) - see
    // choose-agent-content.blade.php.
    function initSearchableSelect(select, agents) {
        var wrap = select.closest('[data-searchable-select]');
        if (!wrap) {
            return;
        }

        var control = wrap.querySelector('[data-ss-control]');
        var controlLabel = wrap.querySelector('[data-ss-control-label]');
        var panel = wrap.querySelector('[data-ss-panel]');
        var search = wrap.querySelector('[data-ss-search]');
        var optionsList = wrap.querySelector('[data-ss-options]');
        var empty = wrap.querySelector('[data-ss-empty]');

        if (!control || !panel || !search || !optionsList || !empty) {
            return;
        }

        var items = agents.map(function (agent) {
            var el = document.createElement('li');
            el.className = 'wizard-searchable-select__option';
            el.setAttribute('data-ss-option', '');
            el.setAttribute('data-value', String(agent.id));
            el.textContent = agent.name || '';
            optionsList.appendChild(el);
            return el;
        });

        function syncSelected() {
            items.forEach(function (el) {
                el.classList.toggle('wizard-searchable-select__option--selected', el.getAttribute('data-value') === select.value);
            });
        }

        function filter(term) {
            term = term.trim().toLowerCase();
            var visibleCount = 0;

            items.forEach(function (el) {
                var match = !term || el.textContent.toLowerCase().indexOf(term) !== -1;
                el.style.display = match ? '' : 'none';
                if (match) {
                    visibleCount++;
                }
            });

            empty.hidden = visibleCount !== 0;
        }

        function open() {
            panel.hidden = false;
            wrap.classList.add('wizard-searchable-select--open');
            search.value = '';
            filter('');
            search.focus();
        }

        function close() {
            panel.hidden = true;
            wrap.classList.remove('wizard-searchable-select--open');
        }

        function selectAgent(id, name) {
            select.value = id;
            controlLabel.textContent = name;
            syncSelected();
            close();
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }

        control.addEventListener('click', function () {
            if (panel.hidden) {
                open();
            } else {
                close();
            }
        });

        search.addEventListener('input', function () {
            filter(search.value);
        });

        search.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                close();
                control.focus();
            } else if (event.key === 'Enter') {
                event.preventDefault();
                var firstVisible = items.filter(function (el) { return el.style.display !== 'none'; })[0];
                if (firstVisible) {
                    selectAgent(firstVisible.getAttribute('data-value'), firstVisible.textContent);
                }
            }
        });

        optionsList.addEventListener('click', function (event) {
            var option = event.target.closest('[data-ss-option]');
            if (option) {
                selectAgent(option.getAttribute('data-value'), option.textContent);
            }
        });

        document.addEventListener('click', function (event) {
            if (!wrap.contains(event.target)) {
                close();
            }
        });

        syncSelected();
    }

    // Renders the picked agent's info card on the Choose Agent step.
    // Agent data is embedded once as JSON on the form (data-agents) rather
    // than fetched, since the eligible list is already fixed server-side
    // for this property.
    function initAgentPicker(root) {
        var form = root.querySelector('[data-agents]');
        var select = document.getElementById('wizard-agent-select');
        var card = root.querySelector('[data-agent-card]');

        if (!form || !select || !card) {
            return;
        }

        var agents = [];
        try {
            agents = JSON.parse(form.getAttribute('data-agents') || '[]');
        } catch (e) {
            agents = [];
        }

        initSearchableSelect(select, agents);

        function renderMetaRow(icon, text) {
            var row = document.createElement('p');
            row.className = 'wizard-agent-card__meta';
            var iconEl = document.createElement('i');
            iconEl.className = 'fas ' + icon;
            var textEl = document.createElement('span');
            textEl.textContent = text;
            row.appendChild(iconEl);
            row.appendChild(textEl);
            return row;
        }

        function render() {
            var agent = agents.filter(function (a) { return String(a.id) === select.value; })[0];

            card.innerHTML = '';

            if (!agent) {
                card.classList.remove('wizard-agent-card--visible');
                return;
            }

            var avatar;
            if (agent.avatar) {
                avatar = document.createElement('img');
                avatar.className = 'wizard-agent-card__avatar';
                avatar.src = agent.avatar;
                avatar.alt = agent.name || '';
            } else {
                avatar = document.createElement('div');
                avatar.className = 'wizard-agent-card__avatar wizard-agent-card__avatar--initials';
                avatar.textContent = agent.initials || '';
            }

            var body = document.createElement('div');
            body.className = 'wizard-agent-card__body';

            var name = document.createElement('h3');
            name.className = 'wizard-agent-card__name';
            name.textContent = agent.name || '';
            body.appendChild(name);

            if (agent.phone) {
                body.appendChild(renderMetaRow('fa-phone', agent.phone));
            }
            if (agent.email) {
                body.appendChild(renderMetaRow('fa-envelope', agent.email));
            }

            var listingCount = agent.listings || 0;
            body.appendChild(renderMetaRow('fa-building', listingCount + ' active listing' + (listingCount === 1 ? '' : 's')));

            if (agent.description) {
                var bio = document.createElement('p');
                bio.className = 'wizard-agent-card__bio';
                bio.textContent = agent.description;
                body.appendChild(bio);
            }

            card.appendChild(avatar);
            card.appendChild(body);
            card.classList.add('wizard-agent-card--visible');
        }

        select.addEventListener('change', render);
        render();
    }

    // Listing Type (For Sale / For Rent) button toggle - drives the hidden
    // #wizard-type input other modules (rent-only fields, description
    // template) already listen to via its 'change' event.
    function initTypeToggle(root) {
        var toggle = root.querySelector('[data-type-toggle]');
        var typeInput = document.getElementById('wizard-type');

        if (!toggle || !typeInput) {
            return;
        }

        toggle.addEventListener('click', function (event) {
            var btn = event.target.closest('[data-type-value]');
            if (!btn) {
                return;
            }

            toggle.querySelectorAll('[data-type-value]').forEach(function (b) {
                b.classList.toggle('wizard-toggle-btn--active', b === btn);
            });

            typeInput.value = btn.getAttribute('data-type-value');
            typeInput.dispatchEvent(new Event('change'));
        });
    }

    // Plain (non-AJAX) wizard forms, e.g. the agent/admin "verify" buttons -
    // the server redirects back to this same page on success, so disabling
    // the submit button on 'submit' and leaving it disabled is enough to
    // block double-submits; no re-enable is needed since the page reloads.
    function initDisableOnSubmit(root) {
        root.querySelectorAll('form[data-disable-on-submit]').forEach(function (form) {
            form.addEventListener('submit', function () {
                var button = form.querySelector('button[type="submit"]');
                if (button) {
                    button.disabled = true;
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('.property-wizard');
        if (!root) {
            return;
        }

        initFacilityRepeater(root);
        initAreaUnitSync(root);

        var uploadUrl = root.getAttribute('data-upload-url');
        initUploader(root, { name: 'images', kind: 'image', uploadUrl: uploadUrl, min: 1, max: 20 });

        // One uploader per admin-configured document slot ("documents_5",
        // "documents_9", ...), or the single generic "documents" fallback
        // when the property's category has none configured - see
        // media.blade.php.
        root.querySelectorAll('[data-uploader^="documents"]').forEach(function (widget) {
            initUploader(root, { name: widget.getAttribute('data-uploader'), kind: 'document', uploadUrl: uploadUrl, max: 1 });
        });

        initStepForm(root);
        initFinalize(root);
        initGuestAuth(root);
        initCategoryAndTemplate(root);
        initRentOnlyFields(root);
        initTypeToggle(root);
        initModerationStatus(root);
        initAgentPicker(root);
        initDisableOnSubmit(root);
    });
})();
