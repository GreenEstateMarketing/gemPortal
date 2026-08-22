class Location {
  static changeProvince($element) {
    let $city = $(document).find("select[data-type=city]");
    if ($element.data("related-city")) {
      $city = $(document).find("#" + $element.data("related-city"));
    }
    let url = $element.data("change-state-url");
    if (url !== null && url !== "" && $element.val() !== "") {
      $.ajax({
        url: url,
        type: "GET",
        data: { state_id: $element.val() },
        beforeSend: () => {
          $element
            .closest("form")
            .find("button[type=submit], input[type=submit]")
            .prop("disabled", true);
        },
        success: (data) => {
          let option =
            '<option value="">' + $city.data("placeholder") + "</option>";
          $.each(data.data, (index, item) => {
            if (item.id === $city.data("origin-value")) {
              option +=
                '<option value="' +
                item.id +
                '" selected="selected">' +
                item.name +
                "</option>";
            } else {
              option +=
                '<option value="' + item.id + '">' + item.name + "</option>";
            }
          });
          $city.html(option);
          $element
            .closest("form")
            .find("button[type=submit], input[type=submit]")
            .prop("disabled", false);
        },
      });
    }
  }
}

$(document).ready(() => {
  let $state_fields = $(document).find("select[data-type=state]");
  if ($state_fields.length > 0) {
    $.each($state_fields, (index, el) => {
      Location.changeProvince($(el));
    });
    $(document).on("change", "select[data-type=state]", (event) => {
      Location.changeProvince($(event.currentTarget));
    });
  }
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
