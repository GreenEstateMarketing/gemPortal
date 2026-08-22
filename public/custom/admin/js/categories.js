console.log("categories.js loaded");
$(document).ready(function () {
  function setParent($default) {
    p_id = $("#parent_id").val();
    if (p_id == "") {
      $("#parent_id_check").prop("checked", true);
      $("#parent_id_check").trigger("change");
    } else {
      $("#parent_id_check").prop("checked", false);
      $("#parent_id_check").trigger("change");
    }
  }
  $("#parent_id_check").change(function () {
    if ($(this).is(":checked")) {
      switchStatus = $(this).is(":checked");
      $("#parent_id").parent().hide();
      $("#parent_id").attr("disabled", true);
      $(".parentCategory").hide();
    } else {
      switchStatus = $(this).is(":checked");

      $("#parent_id").parent().show();
      $("#parent_id").attr("disabled", false);
      $(".parentCategory").show();
    }
  });
  setParent(($default = true));
});

// ==============================
// Country -> State
// ==============================

$(document).on("change", "#country_id", function () {
  let country = $(this).val();

  let url = $(this).data("change-country-url");

  if (!country || !url) {
    return;
  }

  $.ajax({
    url: url,

    type: "GET",

    data: {
      country_id: country,
    },

    success: function (response) {
      let html = '<option value="">Select State</option>';

      $.each(response.data, function (i, state) {
        html += '<option value="' + state.id + '">' + state.name + "</option>";
      });

      $("#state_id").html(html);

      // Reset cities
      $("#city_id").html('<option value="">Select City</option>');
    },
  });
});

// ==============================
// State -> City
// ==============================

$(document).on("change", "#state_id", function () {
  let state = $(this).val();

  let url = $(this).data("change-state-url");

  if (!state || !url) {
    return;
  }

  $.ajax({
    url: url,

    type: "GET",

    data: {
      state_id: state,
    },

    success: function (response) {
      let html = '<option value="">Select City</option>';

      $.each(response.data, function (i, city) {
        html += '<option value="' + city.id + '">' + city.name + "</option>";
      });

      $("#city_id").html(html);

      $("#city_id").trigger("change");
    },
  });
});
