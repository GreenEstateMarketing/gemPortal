const chipArray = [];

$(document).ready(function () {

    let cityAreaArray = [];
    $('#city_id').select2()
        .on("select2:select", e => {
            const event = new Event("change", {bubbles: true, cancelable: true});
            e.params.data.element.parentElement.dispatchEvent(event);
        })
        .on("select2:unselect", e => {
            const event = new Event("change", {bubbles: true, cancelable: true});
            e.params.data.element.parentElement.dispatchEvent(event);
        });


    /**
     * Wait for the specified element to appear in the DOM. When the element appears,
     * provide it to the callback.
     *
     * @param selector a jQuery selector (eg, 'div.container img')
     * @param callback function that takes selected element (null if timeout)
     * @param maxtries number of times to try (return null after maxtries, false to disable, if 0 will still try once)
     * @param interval ms wait between each try
     */
    function waitForEl(selector, callback, maxtries = false, interval = 100) {
        const poller = setInterval(() => {
            const el = $(selector);
            const retry = maxtries === false || maxtries-- > 0
            if (retry && el.val().length < 1) return // will try again
            clearInterval(poller);
            callback(el || null);
        }, interval)
    }

    var url = window.location.href;
    if (url.indexOf("city_id") < 0) {
        waitForEl('#city-name-from-map', mapCityCallback, 10);
    } else {

        $.ajax({
            type: 'get',
            url: 'ajax/get-city-areas',
            dataType: 'json',
            async: false,
            data: {
                city_id: $('#city_id').val()
            },
            success: function (response) {
                /*  $('#autocomplete-ajax').val('');
                  $('#autocomplete-ajax').autocomplete('clear');
                  $('#chipContainer .chip').children('.chip-close').each(function () {
                      $(this).click();
                  });
                  showHideChips(true)*/
                cityAreaArray = response.data;
            }
        });
    }

    function mapCityCallback(el) {
        let val = $('#city_id').find("option:contains('" + $('#city-name-from-map').val() + "')").val();
        $('#city_id').val(val).trigger('change.select2');
        $('#city_id').trigger('change');
        $("#city_id")[0].dispatchEvent(new Event('change'));
        //$('#city_id').change();
    }

    $('#city_id').on('select2:opening', function (e) {
        $('#autocomplete-ajax').val('');
    });

    $('#city_id').on('change', function () {
        var city_id = $(this).val();
        if ($('#_city-id').length > 0) {
            $('#_city-id').val(city_id);
        }
        $.ajax({
            type: 'get',
            url: 'ajax/get-city-areas',
            dataType: 'json',
            async: false,
            data: {
                city_id: city_id
            },
            success: function (response) {
                $('#autocomplete-ajax').val('');
                $('#autocomplete-ajax').autocomplete('clear');
                $('#chipContainer .chip').children('.chip-close').each(function () {
                    $(this).click();
                });
                showHideChips(true)
                cityAreaArray = response.data;
            }
        });
    });


    $(document).click(function () {

        if (chipArray.length > 1) {
            showHideChips(false);
        } else if (chipArray.length == 1) {
            $('#autocomplete-ajax').hide();
            $('#autocomplete-ajax-x').hide();
        }
    });

    // Initialize ajax autocomplete:
    $('#autocomplete-ajax').autocomplete({
        lookup: function (query, done) {

            var result = {
                suggestions: $.map(cityAreaArray, function (dataItem) {
                    if (dataItem.city_area_name.toLowerCase().indexOf(query.toLowerCase()) !== -1) {
                        return {value: dataItem.city_area_name, data: dataItem.id};
                    }
                })
            };
            done(result);
        },
        showNoSuggestionNotice: true,
        noSuggestionNotice: 'Sorry, no matching results',
        onSearchStart: function (params) {
            // $(".spinner-border").show();
        },
        onSearchComplete: function (query, suggestions) {
            // $(".spinner-border").hide();
        },
        onSelect: function (suggestion) {
            addChip(suggestion);
            $('#autocomplete-ajax').val('');
        },
        onHint: function (hint) {
            $('#autocomplete-ajax-x').val(hint);
        },
        onInvalidateSelection: function () {
            // $(".spinner-border").hide();
        }/*,
        search: function (e, u) {
            $(".spinner-border").show();
        },
        response: function (e, u) {
            $(".spinner-border").hide();
        }*/
    });

    function addChip(chipContent) {
        var chipHtml = '<div class="chip">' +
            '<div class="chip-content" data-value=' + chipContent.data + '>' + chipContent.value + '</div>' +
            '<div class="chip-close">' +
            '<svg class="chip-svg" focusable="false" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"></path></svg>' +
            '</div>';

        var index = findNode(chipContent.data);
        if (index <= -1) {
            $('#chipContainer').prepend(chipHtml);

            if ($('#autocomplete-ajax').is(":visible")) {
                $('#autocomplete-ajax-x').hide();
                $('#autocomplete-ajax').hide();
            }
        }

    }


    const observer = new MutationObserver(function (mutations_list) {
        mutations_list.forEach(function (mutation) {
            mutation.addedNodes.forEach(function (added_node) {
                if (added_node.className == 'chip') {
                    var id = $(added_node).children(".chip-content").attr('data-value');
                    var value = $(added_node).children(".chip-content").html();
                    chipArray.push({
                        'id': id,
                        'value': value
                    });
                    //chipArray.push(decodeURIComponent($(added_node).children(".chip-content").attr('data-value')));
                    $(added_node).children(".chip-close").on("click", function (e) {
                        e.stopPropagation();
                        $(added_node).remove();

                    });
                    //observer.disconnect();
                }
            });
            mutation.removedNodes.forEach(function (removeed_node) {
                if (removeed_node.className == 'chip') {
                    //var index = chipArray.indexOf(decodeURIComponent($(removeed_node).children(".chip-content").attr('data-value')));
                    var index = findNode($(removeed_node).children(".chip-content").attr('data-value'));
                    if (index > -1) {
                        chipArray.splice(index, 1);
                    }
                    //observer.disconnect();
                }
            });
        });

        setLabel();

        if (chipArray.length > 0) {
            if (!$('#chipContainer').hasClass("chipContainer")) {
                $('#chipContainer').addClass("chipContainer");
            }
        } else if (chipArray.length <= 0) {
            if ($('#chipContainer').hasClass("chipContainer")) {
                $('#chipContainer').removeClass("chipContainer");
            }
        }

        if (chipArray.length > 1) {
            showHideChips(false)
        } else if (chipArray.length == 1) {

            showHideChips(true)
        }
    });

    observer.observe(document.querySelector("#chipContainer"), {subtree: false, childList: true});

    function setLabel() {
        $('#chipViewMore').children(".chip-content").html(chipArray.length - 1 + ' More+');
    }

    $("#parentChipContainer").on("click", function (e) {
        showHideViews();
        e.stopPropagation();
    });
    $("#autocomplete-ajax").on("click", function (e) {
        e.stopPropagation();
    });
    $.each(getUrlParameter('k[]'), function (index, value) {
        var jsonObject = JSON.parse(value)
        jsonObject['data'] = jsonObject['id'];
        delete jsonObject['id'];
        console.log(jsonObject);
        addChip(jsonObject);
    });

    function getUrlParameter(sParam) {
        let params = new URLSearchParams(window.location.search);
        let value = params.getAll(sParam);
        return value;
    }

    function findNode(id) {
        var nodeIndex = -1;
        chipArray.forEach(function (e, index) {
            if (id == e.id) {
                //chipArray.splice(index, 1);
                nodeIndex = index;
            }
        })
        return nodeIndex;
    }
});

function showHideViews() {

    if (chipArray.length > 1 && $('#chipViewMore').is(":visible")) {
        showHideChips(true);
    } else if (chipArray.length > 1 && $('#chipViewMore').is(":hidden")) {
        showHideChips(false);
    } else if (chipArray.length == 1) {
        if ($('#chipViewMore').is(":visible")) {
            $('#chipViewMore').hide();
        }
        if ($('#autocomplete-ajax').is(":visible")) {
            $('#autocomplete-ajax-x').hide();
            $('#autocomplete-ajax').hide();
        } else {
            $('#autocomplete-ajax-x').show();
            $('#autocomplete-ajax').show();
            $('#autocomplete-ajax').focus();
        }
    } else {
        $('#autocomplete-ajax-x').show();
        $('#autocomplete-ajax').show();
        $('#autocomplete-ajax').focus();
    }
}

function showHideChips(show) {
    if (show) {
        $("#chipContainer").children().show();
        $('#chipViewMore').hide();
        $('#autocomplete-ajax-x').show();
        $('#autocomplete-ajax').show();
        $('#autocomplete-ajax').focus();
    } else {
        $("#chipContainer").children().hide();
        $('#chipViewMore').show();
        $('#chipContainer div:first-child').show();
    }
}
