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

    function initFacilityRepeater(root) {
        var container = root.querySelector('[data-facility-rows]');
        var addButton = root.querySelector('[data-facility-add]');
        var template = root.querySelector('[data-facility-template]');

        if (!container || !addButton || !template) {
            return;
        }

        addButton.addEventListener('click', function () {
            var clone = template.content.cloneNode(true);
            container.appendChild(clone);
        });

        container.addEventListener('click', function (event) {
            if (event.target.matches('[data-facility-remove]')) {
                event.target.closest('[data-facility-row]').remove();
            }
        });
    }

    function initUploader(root, options) {
        var widget = root.querySelector('[data-uploader="' + options.name + '"]');
        if (!widget) {
            return;
        }

        var dropzone = widget.querySelector('.wizard-upload');
        var input = widget.querySelector('input[type="file"]');
        var thumbs = widget.querySelector('[data-uploader-thumbs]');
        var hidden = widget.querySelector('[data-uploader-value]');
        var items = [];

        try {
            items = JSON.parse(hidden.value || '[]');
        } catch (e) {
            items = [];
        }

        function persist() {
            hidden.value = JSON.stringify(items);
        }

        function renderThumb(item) {
            if (options.kind === 'image') {
                var wrap = document.createElement('div');
                wrap.className = 'wizard-thumb';
                wrap.innerHTML = '<img src="' + item.url + '" alt=""><button type="button" class="wizard-thumb__remove" data-remove-url="' + item.url + '">&times;</button>';
                thumbs.appendChild(wrap);
            } else {
                var row = document.createElement('div');
                row.className = 'wizard-doc-item';
                row.innerHTML = '<span><i class="fas fa-file"></i> ' + item.name + '</span><button type="button" class="wizard-btn wizard-btn--danger" data-remove-url="' + item.url + '">Remove</button>';
                thumbs.appendChild(row);
            }
        }

        items.forEach(renderThumb);

        thumbs.addEventListener('click', function (event) {
            var btn = event.target.closest('[data-remove-url]');
            if (!btn) {
                return;
            }
            var url = btn.getAttribute('data-remove-url');
            items = items.filter(function (item) {
                return item.url !== url;
            });
            persist();
            btn.closest(options.kind === 'image' ? '.wizard-thumb' : '.wizard-doc-item').remove();
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
            Array.prototype.forEach.call(fileList, function (file) {
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
                    return response.json();
                }).then(function (json) {
                    if (json.error) {
                        window.alert(json.message || 'Upload failed');
                        return;
                    }
                    var url = (json.data && (json.data.url || json.data.src)) || '';
                    var item = { url: url, name: file.name };
                    items.push(item);
                    persist();
                    renderThumb(item);
                }).catch(function () {
                    window.alert('Upload failed, please try again.');
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

            ['images', 'documents'].forEach(function (key) {
                var hidden = form.querySelector('[data-uploader-value="' + key + '"]');
                if (hidden) {
                    try {
                        payload[key] = JSON.parse(hidden.value || '[]');
                    } catch (e) {
                        payload[key] = [];
                    }
                }
            });

            setLoading(submitBtn, true);

            postJson(form.getAttribute('action'), payload).then(function (result) {
                setLoading(submitBtn, false);

                if (!result.ok) {
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
                setLoading(button, false);

                if (result.json && result.json.require_auth && guestPanel) {
                    guestPanel.classList.add('wizard-guest-auth--visible');
                    guestPanel.style.display = 'block';
                    button.style.display = 'none';
                    guestPanel.scrollIntoView({ behavior: 'smooth' });
                    return;
                }

                if (!result.ok || !result.json.success) {
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

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                var isNew = radio.value === 'new_user' && radio.checked;
                if (radio.checked) {
                    existingFields.style.display = radio.value === 'existing_user' ? 'block' : 'none';
                    newFields.style.display = isNew ? 'block' : 'none';
                }
            });
        });

        var submitBtn = panel.querySelector('[data-guest-auth-submit]');
        var authUrl = panel.getAttribute('data-authenticate-url');

        submitBtn.addEventListener('click', function () {
            clearErrors(panel);
            var payload = collectFormData(panel);

            setLoading(submitBtn, true);

            postJson(authUrl, payload).then(function (result) {
                setLoading(submitBtn, false);

                if (!result.ok || !result.json.success) {
                    if (result.json && result.json.errors) {
                        applyErrors(panel, result.json.errors);
                    } else {
                        window.alert((result.json && result.json.message) || 'Unable to continue.');
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
        sub-category of that parent). The form only ever submits one
        `category_id` (the sub-category, or the parent itself when it has no
        children) - the two <select> elements here are purely a browsing aid.

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

        var categorySelect = document.getElementById('wizard-category');
        var subcategorySelect = document.getElementById('wizard-subcategory');
        var categoryIdInput = document.getElementById('wizard-category-id');
        var categoryNameInput = document.getElementById('wizard-category-name');
        var templateInput = document.getElementById('wizard-template-description');
        var descriptionField = document.getElementById('wizard-description');

        if (!categorySelect || !subcategorySelect || !categoryIdInput) {
            return;
        }

        function childrenOf(parentId) {
            return tree.filter(function (c) {
                return c.parent_id === parseInt(parentId, 10);
            });
        }

        function findById(id) {
            return tree.filter(function (c) {
                return String(c.id) === String(id);
            })[0];
        }

        function setActiveCategory(id, name, triggerFetch) {
            categoryIdInput.value = id || '';
            categoryNameInput.value = name || '';
            if (triggerFetch) {
                fetchTemplate(id);
            }
        }

        // triggerFetch is false only for the initial page-load pre-fill
        // (resuming a draft) - it must not re-fetch the template and
        // overwrite a description the user already saved.
        function populateSubcategories(parentId, selectSubId, triggerFetch) {
            var children = childrenOf(parentId);
            subcategorySelect.innerHTML = '<option value="">' + (subcategorySelect.dataset.placeholder || 'Select a sub-category') + '</option>';

            children.forEach(function (child) {
                var opt = document.createElement('option');
                opt.value = child.id;
                opt.textContent = child.name;
                if (selectSubId && String(selectSubId) === String(child.id)) {
                    opt.selected = true;
                }
                subcategorySelect.appendChild(opt);
            });

            if (children.length === 0) {
                subcategorySelect.setAttribute('disabled', 'disabled');
                var parent = findById(parentId);
                setActiveCategory(parentId, parent ? parent.name : '', triggerFetch);
            } else {
                subcategorySelect.removeAttribute('disabled');
                if (selectSubId) {
                    var selected = findById(selectSubId);
                    setActiveCategory(selectSubId, selected ? selected.name : '', triggerFetch);
                } else {
                    setActiveCategory('', '', false);
                }
            }
        }

        categorySelect.addEventListener('change', function () {
            if (!this.value) {
                subcategorySelect.innerHTML = '<option value="">Select a sub-category</option>';
                subcategorySelect.setAttribute('disabled', 'disabled');
                setActiveCategory('', '', false);
                return;
            }
            populateSubcategories(this.value, null, true);
        });

        subcategorySelect.addEventListener('change', function () {
            var selected = findById(this.value);
            setActiveCategory(this.value, selected ? selected.name : '', true);
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
                    applyTemplatePlaceholders();
                } else if (descriptionField) {
                    templateInput.value = '';
                    descriptionField.value = '';
                }
            }).catch(function () {
                // Leave whatever description text is already there.
            });
        }

        function applyTemplatePlaceholders() {
            if (!templateInput || !templateInput.value || !descriptionField) {
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
        }

        ['wizard-number-bedroom', 'wizard-number-bathroom', 'wizard-square', 'wizard-type', 'wizard-area-units', 'wizard-number-floor'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', applyTemplatePlaceholders);
            }
        });

        // Pre-fill the sub-category list on first render (resuming a draft)
        // without re-fetching the template over the network.
        if (categorySelect.value) {
            populateSubcategories(categorySelect.value, categoryIdInput.value, false);
        }

        // The description field is always read-only - it's entirely
        // generated from the template + the fields above, so re-apply the
        // substitution on load too (values may have changed since it was
        // last saved).
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
            var isRent = typeSelect.value === 'rent';
            rentFields.forEach(function (field) {
                field.style.display = isRent ? '' : 'none';
                if (!isRent) {
                    var input = field.querySelector('[data-field]');
                    if (input) {
                        input.value = '';
                    }
                }
            });
        }

        typeSelect.addEventListener('change', sync);
        sync();
    }

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('.property-wizard');
        if (!root) {
            return;
        }

        initFacilityRepeater(root);

        var uploadUrl = root.getAttribute('data-upload-url');
        initUploader(root, { name: 'images', kind: 'image', uploadUrl: uploadUrl });
        initUploader(root, { name: 'documents', kind: 'document', uploadUrl: uploadUrl });

        initStepForm(root);
        initFinalize(root);
        initGuestAuth(root);
        initCategoryAndTemplate(root);
        initRentOnlyFields(root);
    });
})();
