/*
    Path in theme: platform/themes/YOUR_THEME/public/js/new-home-page/property-categories.js
    Enqueue near the end of body (after the property-categories section's markup):

    <script src="{{ Theme::asset()->url('js/new-home-page/property-categories.js') }}"></script>
*/
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('propertyCategorySearchInput');
    var hiddenId = document.getElementById('propertyCategorySearchId');
    var suggestionsBox = document.getElementById('propertyCategorySuggestions');
    var form = document.getElementById('propertyCategorySearchForm');
    if (!input || !hiddenId || !suggestionsBox || !form) return;

    var debounceTimer = null;
    var currentRequest = null;

    function hideSuggestions() {
        suggestionsBox.style.display = 'none';
        suggestionsBox.innerHTML = '';
    }

    function renderSuggestions(items) {
        suggestionsBox.innerHTML = '';

        if (!items.length) {
            var empty = document.createElement('div');
            empty.className = 'property-categories__suggestion-empty';
            empty.textContent = 'No matching categories';
            suggestionsBox.appendChild(empty);
            suggestionsBox.style.display = 'block';
            return;
        }

        items.forEach(function (item) {
            var row = document.createElement('div');
            row.className = 'property-categories__suggestion';
            row.setAttribute('data-id', item.id);

            var name = document.createElement('span');
            name.textContent = item.name;
            row.appendChild(name);

            if (item.parent_name) {
                var parent = document.createElement('span');
                parent.className = 'property-categories__suggestion-parent';
                parent.textContent = '(' + item.parent_name + ')';
                row.appendChild(parent);
            }

            row.addEventListener('click', function () {
                input.value = item.name;
                hiddenId.value = item.id;
                hideSuggestions();
            });

            suggestionsBox.appendChild(row);
        });

        suggestionsBox.style.display = 'block';
    }

    input.addEventListener('input', function () {
        // Any manual edit invalidates a previously selected suggestion -
        // otherwise "Search" would still filter by the old category_id
        // even after the visible text no longer matches it.
        hiddenId.value = '';

        var query = input.value.trim();
        clearTimeout(debounceTimer);

        if (!query) {
            hideSuggestions();
            return;
        }

        debounceTimer = setTimeout(function () {
            if (currentRequest) {
                currentRequest.abort();
            }

            currentRequest = new XMLHttpRequest();
            currentRequest.open('GET', 'ajax/search-categories?q=' + encodeURIComponent(query), true);
            currentRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            currentRequest.onload = function () {
                if (currentRequest.status !== 200) return;
                try {
                    var response = JSON.parse(currentRequest.responseText);
                    renderSuggestions(response.data || []);
                } catch (e) {
                    hideSuggestions();
                }
            };
            currentRequest.send();
        }, 250);
    });

    document.addEventListener('click', function (e) {
        if (!form.contains(e.target)) {
            hideSuggestions();
        }
    });
});
