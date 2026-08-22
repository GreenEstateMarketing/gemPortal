$(document).ready(function () {
    $(document).on("change", "#type", function (e) {
        if ($(e.currentTarget).val() === "rent") {
            $("#period").closest(".period-form-group").removeClass("hidden").fadeIn();
        } else {
            $("#period").closest(".period-form-group").addClass("hidden").fadeOut();
        }
    });

    $(document).on("change", "#never_expired", function (e) {
        if ($(e.currentTarget).is(":checked") === true) {
            $("#auto_renew").closest(".auto-renew-form-group").addClass("hidden").fadeOut();
        } else {
            $("#auto_renew").closest(".auto-renew-form-group").removeClass("hidden").fadeIn();
        }
    });
});

$(document).ready(function () {
    let areaUnitBefore = $('#area_units').val();
    $('#area_units').change(function () {
            let areaUnit = $(this).val();

            let unitValue = $('#square').val();

            if (!unitValue) {
                unitValue = 0;
            }

            if (areaUnitBefore === 'ft²') {
                if (areaUnit === 'm²') {
                    $('#square').val(unitValue * 0.092903);
                } else if (areaUnit === 'yards') {
                    $('#square').val(unitValue / 9);
                } else if (areaUnit === 'marla') {
                    $('#square').val(unitValue / 272.25);
                } else if (areaUnit === 'kanal') {
                    $('#square').val(unitValue / 5445);
                }
            }
            if (areaUnitBefore === 'm²') {
                if (areaUnit === 'ft²') {
                    $('#square').val(unitValue * 10.7639);
                } else if (areaUnit === 'yards') {
                    $('#square').val(unitValue * 1.19599);
                } else if (areaUnit === 'marla') {
                    $('#square').val(unitValue * 10.7639 / 272.25);
                } else if (areaUnit === 'kanal') {
                    $('#square').val(unitValue * 10.7639 / 5445);
                }
            }
            if (areaUnitBefore === 'yards') {
                if (areaUnit === 'ft²') {
                    $('#square').val(unitValue * 9);
                } else if (areaUnit === 'm²') {
                    $('#square').val(unitValue * 0.836127);
                } else if (areaUnit === 'marla') {
                    $('#square').val(unitValue / 30.25);
                } else if (areaUnit === 'kanal') {
                    $('#square').val(unitValue / 605);
                }
            }
            if (areaUnitBefore === 'marla') {
                if (areaUnit === 'ft²') {
                    $('#square').val(unitValue * 272.25);
                } else if (areaUnit === 'm²') {
                    $('#square').val(unitValue * 272.25 * 0.092903);
                } else if (areaUnit === 'yards') {
                    $('#square').val(unitValue * 30.25);
                } else if (areaUnit === 'kanal') {
                    $('#square').val(unitValue / 20);
                }
            }
            if (areaUnitBefore === 'kanal') {
                if (areaUnit === 'ft²') {
                    $('#square').val(unitValue * 5445);
                } else if (areaUnit === 'm²') {
                    $('#square').val(unitValue * 5445 * 0.092903);
                } else if (areaUnit === 'yards') {
                    $('#square').val(unitValue * 5445 / 9);
                } else if (areaUnit === 'marla') {
                    $('#square').val(unitValue * 20);
                }
            }
            areaUnitBefore = areaUnit;
        }
    )
})

$(document).ready(function () {
    function checkImageCount() {
        var images = $('#images').val();
        var imageArray = images ? images.split(',') : [];

        console.log('imageArray.length', imageArray.length);

        if (imageArray.length > 20) {
            $('.btn-set button').prop('disabled', true);
            $('#image-warning').css('color', '#f00');
        } else {
            $('.btn-set button').prop('disabled', false);
            $('#image-warning').css('color', '#856404');
        }
    }

    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                checkImageCount();
            }
        });
    });

    observer.observe(document.getElementById('images'), {
        attributes: true
    });

    checkImageCount();
});