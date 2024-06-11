$(document).ready(function() {
    $(document).on("change", "#type", function(e) {
        if ($(e.currentTarget).val() === "rent") {
            $("#period").closest(".period-form-group").removeClass("hidden").fadeIn();
        } else {
            $("#period").closest(".period-form-group").addClass("hidden").fadeOut();
        }
    });

    $(document).on("change", "#never_expired", function(e) {
        if ($(e.currentTarget).is(":checked") === true) {
            $("#auto_renew").closest(".auto-renew-form-group").addClass("hidden").fadeOut();
        } else {
            $("#auto_renew").closest(".auto-renew-form-group").removeClass("hidden").fadeIn();
        }
    });
});
