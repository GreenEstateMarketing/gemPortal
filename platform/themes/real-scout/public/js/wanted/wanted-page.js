/*
    Path in theme: platform/themes/real-scout/public/js/wanted/wanted-page.js
    Served copy:   public/themes/real-scout/js/wanted/wanted-page.js (keep both in sync manually)

    Vanilla JS (no jQuery) behind the redesigned /wanted page - goal tile
    selection, category/sub-category chip cascade (same data-category-tree
    idiom as js/wizard/property-wizard.js's initCategoryAndTemplate), the
    searchable City / City Area comboboxes (same .wizard-combobox markup and
    createCombobox() behaviour as the Add Property wizard's Location step -
    platform/plugins/real-estate/resources/views/wizard/steps/location.blade.php
    - copied here rather than shared because that one lives inline in the
    wizard's own Blade view, not in a reusable JS file), the city -> city-area
    AJAX cascade (same /ajax/get-city-areas endpoint, now feeding the
    combobox's setOptions() instead of rebuilding <option> elements), and a
    plain fetch() submit that speaks the same JSON success/error contract
    Botble\Contact\Http\Controllers\PublicController::postSendWanted()
    already returns.
*/
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('wanted-form');
        if (!form) {
            return;
        }

        initGoalTiles(form);
        initCategoryCascade(form);
        var comboboxes = initComboboxes(form);
        initCityAreaCascade(comboboxes);
        initNewProjectToggle(form);
        initSubmit(form, comboboxes);
    });

    // =====================================================
    // Goal tiles (Buy / Rent / Invest / Build)
    // =====================================================
    function initGoalTiles(form) {
        var tiles = form.querySelectorAll('.wanted-goal-tile');
        var typeInput = document.getElementById('wanted-type');
        var projectBlock = form.querySelector('[data-project-block]');
        var projectSelect = document.getElementById('wanted-project-select');
        var newProjectValue = document.getElementById('wanted-new-project-value');

        function applyType(type) {
            tiles.forEach(function (tile) {
                tile.classList.toggle('wanted-goal-tile--active', tile.getAttribute('data-type') === type);
            });
            typeInput.value = type;

            var needsProject = type === 'invest' || type === 'build';
            if (projectBlock) {
                projectBlock.hidden = !needsProject;
                if (!needsProject) {
                    if (projectSelect) {
                        projectSelect.removeAttribute('required');
                    }
                    if (newProjectValue) {
                        newProjectValue.removeAttribute('required');
                    }
                }
            }
        }

        tiles.forEach(function (tile) {
            tile.addEventListener('click', function () {
                applyType(tile.getAttribute('data-type'));
            });
        });

        applyType(typeInput.value || 'buy');
    }

    // =====================================================
    // Category -> Property type chip cascade
    // =====================================================
    function initCategoryCascade(form) {
        var categoryRow = form.querySelector('[data-category-row]');
        var typeRow = form.querySelector('[data-subcategory-row]');
        var categoryIdInput = document.getElementById('wanted-category-id');

        if (!categoryRow || !typeRow || !categoryIdInput) {
            return;
        }

        var tree = [];
        try {
            tree = JSON.parse(form.getAttribute('data-category-tree') || '[]');
        } catch (e) {
            tree = [];
        }

        function childrenOf(parentId) {
            return tree.filter(function (c) {
                return String(c.parent_id) === String(parentId);
            });
        }

        function markActive(row, value) {
            row.querySelectorAll('.wizard-chip-btn').forEach(function (btn) {
                btn.classList.toggle('wizard-chip-btn--active', btn.getAttribute('data-id') === String(value));
            });
        }

        function renderTypes(parentId, selectId) {
            var children = childrenOf(parentId);
            typeRow.innerHTML = '';

            if (!children.length) {
                categoryIdInput.value = parentId;
                return;
            }

            children.forEach(function (child) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'wizard-chip-btn';
                btn.setAttribute('data-id', child.id);
                btn.textContent = child.name;
                btn.addEventListener('click', function () {
                    markActive(typeRow, child.id);
                    categoryIdInput.value = child.id;
                });
                typeRow.appendChild(btn);
            });

            var idToSelect = selectId && children.some(function (c) { return String(c.id) === String(selectId); })
                ? selectId
                : children[0].id;
            markActive(typeRow, idToSelect);
            categoryIdInput.value = idToSelect;
        }

        categoryRow.querySelectorAll('.wizard-chip-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                markActive(categoryRow, btn.getAttribute('data-id'));
                renderTypes(btn.getAttribute('data-id'));
            });
        });

        var firstCategoryBtn = categoryRow.querySelector('.wizard-chip-btn');
        if (firstCategoryBtn) {
            markActive(categoryRow, firstCategoryBtn.getAttribute('data-id'));
            renderTypes(firstCategoryBtn.getAttribute('data-id'));
        }
    }

    // =====================================================
    // Searchable combobox (City / City Area) - same behaviour as the wizard's
    // Location step createCombobox(), copied rather than imported since that
    // one is defined inline in a Blade view, not a shared JS file. A type-
    // ahead text input + dropdown menu, backed by a hidden `name=`d input
    // that's what actually gets submitted. Supports swapping its option list
    // after init via setOptions() - the hook the city->area cascade needs.
    // =====================================================
    function createCombobox(container) {
        var input = container.querySelector('[data-combobox-input]');
        var hidden = container.querySelector('[data-combobox-value]');
        var menu = container.querySelector('[data-combobox-menu]');
        var options = [];
        var selectedLabel = input.value || '';

        try {
            var preset = container.getAttribute('data-combobox-options');
            if (preset) {
                options = JSON.parse(preset);
            }
        } catch (e) {
            options = [];
        }

        function close() {
            menu.classList.remove('wizard-combobox__menu--open');
            menu.innerHTML = '';
        }

        function render(filterText) {
            var term = (filterText || '').trim().toLowerCase();
            var matches = term
                ? options.filter(function (o) { return String(o.name).toLowerCase().indexOf(term) !== -1; })
                : options;

            menu.innerHTML = '';

            if (matches.length === 0) {
                var empty = document.createElement('div');
                empty.className = 'wizard-combobox__empty';
                empty.textContent = options.length === 0 ? (container.getAttribute('data-combobox-empty') || 'No options available') : 'No matches';
                menu.appendChild(empty);
            } else {
                matches.slice(0, 100).forEach(function (option) {
                    var item = document.createElement('div');
                    item.className = 'wizard-combobox__option';
                    item.textContent = option.name;
                    item.setAttribute('data-combobox-option-id', option.id);
                    menu.appendChild(item);
                });
            }

            menu.classList.add('wizard-combobox__menu--open');
        }

        function selectOption(id, name) {
            hidden.value = id;
            input.value = name;
            selectedLabel = name;
            close();
            hidden.dispatchEvent(new Event('change'));
        }

        menu.addEventListener('mousedown', function (event) {
            var item = event.target.closest('[data-combobox-option-id]');
            if (!item) {
                return;
            }
            var id = item.getAttribute('data-combobox-option-id');
            var option = options.filter(function (o) { return String(o.id) === String(id); })[0];
            selectOption(id, option ? option.name : item.textContent);
        });

        input.addEventListener('focus', function () {
            render(input.value === selectedLabel ? '' : input.value);
        });
        input.addEventListener('input', function () {
            render(input.value);
        });
        input.addEventListener('blur', function () {
            setTimeout(function () {
                if (input.value !== selectedLabel) {
                    input.value = selectedLabel;
                }
                close();
            }, 150);
        });

        return {
            setOptions: function (newOptions) {
                options = newOptions;
            },
            clear: function () {
                options = [];
                selectedLabel = '';
                hidden.value = '';
                input.value = '';
                hidden.dispatchEvent(new Event('change'));
            },
            // Like clear(), but leaves the preset `options` list alone - for
            // a static list (City) a form.reset() should drop the current
            // selection without also wiping what's searchable afterward,
            // unlike city_area's fetched-per-city list which should go away.
            resetLabel: function () {
                selectedLabel = '';
                hidden.value = '';
                input.value = '';
            },
            isEmpty: function () {
                return !hidden.value;
            },
        };
    }

    function initComboboxes(form) {
        var comboboxes = {};
        form.querySelectorAll('[data-combobox]').forEach(function (el) {
            comboboxes[el.getAttribute('data-combobox')] = createCombobox(el);
        });
        return comboboxes;
    }

    // =====================================================
    // City -> City area
    // =====================================================
    function initCityAreaCascade(comboboxes) {
        var city = comboboxes.city;
        var cityArea = comboboxes.city_area;
        if (!city || !cityArea) {
            return;
        }

        var cityHidden = document.querySelector('[data-combobox="city"] [data-combobox-value]');
        cityHidden.addEventListener('change', function () {
            cityArea.clear();

            var cityId = this.value;
            if (!cityId) {
                return;
            }

            fetch('/ajax/get-city-areas?city_id=' + encodeURIComponent(cityId), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(function (res) { return res.json(); })
                .then(function (response) {
                    var options = (response.data || []).map(function (item) {
                        return { id: item.id, name: item.city_area_name };
                    });
                    cityArea.setOptions(options);
                })
                .catch(function () {
                    /* Leave the "no options" empty-state in place on failure. */
                });
        });
    }

    // =====================================================
    // "New project" checkbox
    // =====================================================
    function initNewProjectToggle(form) {
        var checkbox = document.getElementById('wanted-new-project');
        var valueInput = document.getElementById('wanted-new-project-value');
        if (!checkbox || !valueInput) {
            return;
        }

        checkbox.addEventListener('change', function () {
            valueInput.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                valueInput.value = '';
            }
        });
    }

    // =====================================================
    // Submit
    // =====================================================
    function initSubmit(form, comboboxes) {
        var submitBtn = document.getElementById('wanted-submit-btn');
        var successBox = form.querySelector('[data-alert-success]');
        var errorBox = form.querySelector('[data-alert-error]');

        function validateComboboxes() {
            // type=hidden inputs are exempt from the "required" constraint by
            // spec (they're barred from constraint validation entirely), so
            // form.reportValidity() below would silently accept an empty
            // city/city-area - check them by hand first instead.
            var valid = true;
            ['city', 'city_area'].forEach(function (key) {
                var box = comboboxes[key];
                var errorEl = form.querySelector('[data-error-for="' + (key === 'city' ? 'city_id' : 'city_area_id') + '"]');
                var isEmpty = !box || box.isEmpty();
                if (errorEl) {
                    errorEl.textContent = isEmpty ? (key === 'city' ? 'City field is required' : 'City area field is required') : '';
                }
                if (isEmpty) {
                    valid = false;
                }
            });
            return valid;
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            if (successBox) {
                successBox.hidden = true;
            }
            if (errorBox) {
                errorBox.hidden = true;
                errorBox.querySelector('ul').innerHTML = '';
            }

            var comboboxesValid = validateComboboxes();

            if (!form.reportValidity() || !comboboxesValid) {
                return;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('is-loading');
            }

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            })
                .then(function (res) { return res.json(); })
                .then(function (response) {
                    if (response.error) {
                        if (errorBox) {
                            var list = errorBox.querySelector('ul');
                            var errors = Array.isArray(response.error) ? response.error : [response.error];
                            errors.forEach(function (message) {
                                var li = document.createElement('li');
                                li.textContent = message;
                                list.appendChild(li);
                            });
                            errorBox.hidden = false;
                        }
                        return;
                    }

                    form.reset();
                    // form.reset() only clears the comboboxes' DOM values, not
                    // their closed-over selectedLabel - without this, blurring
                    // either field after a reset would silently restore the
                    // old selected text (see createCombobox's blur handler).
                    // city_area's option list is also stale (scoped to
                    // whichever city was picked) and should go away too;
                    // city's own list is static, so only its label resets.
                    if (comboboxes.city) {
                        comboboxes.city.resetLabel();
                    }
                    if (comboboxes.city_area) {
                        comboboxes.city_area.clear();
                    }
                    if (successBox) {
                        successBox.querySelector('span').textContent = response.success || 'Your request has been submitted.';
                        successBox.hidden = false;
                    }
                })
                .catch(function () {
                    if (errorBox) {
                        var list = errorBox.querySelector('ul');
                        var li = document.createElement('li');
                        li.textContent = 'Something went wrong. Please try again.';
                        list.appendChild(li);
                        errorBox.hidden = false;
                    }
                })
                .finally(function () {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('is-loading');
                    }
                });
        });
    }
})();
