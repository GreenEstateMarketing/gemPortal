///////////////agent list//////////////

$(window).on("load", function () {
  $("#city_id").select2();

  $("#city_area_id").select2();
  $("#location_input").select2();
  // Handler for .load() called.

  // $(".first_sub-category").trigger("click");
  /*if(window.location.pathname=="/Add-Property")
    {
        $("body").css("background","#26282b");
    }*/
  function agent_list() {
    var latitude = $("#latitude").val();
    var longitude = $("#longitude").val();
    $.ajax({
      url: "/api/v1/agent-list",
      type: "get",
      dataType: "json",
      data: {
        longitude: longitude,
        latitude: latitude,
      },
      success: function (response) {
        $("#dropdown-menu-member").empty();
        //$("#dropdown-menu-member").append(');
        $("#dropdown-menu-member").append(
          '<input type="text" placeholder="Search.." class="form-control" id="myInput" onKeyUp="filterFunction()">',
        );

        if (response.length > 0) {
          $.each(response, function (key, value) {
            console.log(value.img_src);
            if (value.rating) {
              $("#dropdown-menu-member").append(
                '<li class="dropdown-item" data-agent-id="' +
                  value.id +
                  '"><img src="' +
                  value.img_src.encoded +
                  '" width="50px" height="50px" class="br-100 v-mid mr1">  ' +
                  value.first_name +
                  " " +
                  value.last_name +
                  " (" +
                  value.rating +
                  '<i class="fa fa-star" style="color:#f3a54a" aria-hidden="true"></i>)</li>',
              );
            } else {
              $("#dropdown-menu-member").append(
                '<li class="dropdown-item"  data-agent-id="' +
                  value.id +
                  '"><img src="' +
                  value.img_src.encoded +
                  '" width="50" height="50"  class="br-100 v-mid mr1">  ' +
                  value.first_name +
                  "  " +
                  value.last_name +
                  " </li>",
              );
            }
            //       $("#agent_list").append('  <img src="/themes/real-scout/images/team01.jpeg" class="br-100 v-mid mr-1" style="width: 30px;"><option value='+value.id+'>'+value.first_name+' ('+value.rating+')</option>');
            // else
            //     $("#agent_list").append(' <img src="/themes/real-scout/images/team01.jpeg" class="br-100 v-mid mr-1" style="width: 30px;"><option value='+value.id+'>'+value.first_name+'</option>');
          });
        } else {
          $("#dropdown-menu-member").append(
            '<li class="dropdown-item disabled" disabled="disabled">No Agent Found in this Area</li>',
          );
        }
      },
    });
  }

  agent_list();

  $("#latitude").on("change", function () {
    agent_list();
  });
});
$(document).ready(function () {
  var member_status = $("input[name='member_status']:checked").val();
  $.ajax({
    type: "get",
    url: "/api/v1/get-term-conditions",
    /* processData: false,
         contentType: false,*/
    dataType: "json",
    async: false,
    success: function (data) {
      $("#term_condition_body").html(data.html);
    },
  });
  $(".dropdown-item").on("click", function () {});

  $(document).on("click", ".custom-select-agent", function (event) {
    $("#text_agent").html("Agent List");
    $(".custom-select-agent").addClass("d-none");
    $(".agent-detail").addClass("d-none");
    $("#agent_list").val("");
  });

  $(document).on("click", "#dropdown-menu-member li", function (event) {
    var btnObj = $(this).parent().siblings("button");
    $("#text_agent").html($(this).text());
    //$(btnObj+" <span class='close_btn'>").html($(this).text());
    $(".custom-select-agent").removeClass("d-none");
    $("#agent_list").val($(this).attr("data-agent-id"));
    $(".show_contact_detail").addClass("d-none");
    if ($(this).attr("data-agent-id") == "") {
      alert("no id found");
      $(".agent-detail").addClass("d-none");
    } else {
      $("input[name='author_id_hidden']").val($(this).attr("data-agent-id"));
      $.ajax({
        url: "/api/v1/agent-data",
        type: "get",
        dataType: "json",
        data: {
          id: $(this).attr("data-agent-id"),
        },
        async: false,
        success: function (response) {
          if (response.phone == null) {
            $(".showContact").css("display", "none");
          } else {
            $(".showContact").css("display", "block");
          }
          $(".agent-image").attr("src", response.avatar_url.encoded);
          $("#agent_name").text(response.first_name + " " + response.last_name);
          $("#agent_email").text(response.email);
          $("#agent_desc").html(response.description);
          $("#agent_no").html(response.phone);
          $(".agent-detail").removeClass("d-none");
        },
      });
    }
  });
  $("#modal_terms").on("click", function () {
    if ($("input[name='modal_terms']:checked")) {
      $("#terms").prop("checked", true);
      $("#exampleModal").modal("hide");
      $(document.body).removeClass("modal-open");
      $(".modal-backdrop").remove();
    } else {
    }
  });
  $.validator.addMethod(
    "notZero",
    function (value, element) {
      return value !== "0";
    },
    "Please select an option.",
  );
});

///////on change to get agent data///
$(".dropdown-item").change(function (e) {
  $(".show_contact_detail").addClass("d-none");
  if ($(this).attr("data-agent-id") == "") {
    $(".agent-detail").addClass("d-none");
  } else {
    $.ajax({
      url: "/api/v1/agent-data",
      type: "get",
      dataType: "json",
      data: {
        id: $(this).attr("data-agent-id"),
      },
      async: false,
      success: function (response) {
        if (response.phone == null) {
          $(".showContact").css("display", "none");
        } else {
          $(".showContact").css("display", "block");
        }
        $("#agent_name").text(response.first_name + " " + response.last_name);
        $("#agent_email").text(response.email);
        $("#agent_desc").html(response.description);
        $("#agent_no").html(response.phone);
        $(".agent-detail").removeClass("d-none");
      },
    });
  }
});

//==============================================================
// Validation Helpers
//==============================================================

function scrollToElement(element) {
  $("html, body").animate(
    {
      scrollTop: element.offset().top - 120,
    },
    500,
  );
}

function removeErrors() {
  $(".validation").remove();
  $(".validation_login").remove();
  $(".validation_register").remove();
}

function showError(element, message, cls = "validation") {
  removeErrors();

  element.after('<ul class="' + cls + '"><li>' + message + "</li></ul>");

  scrollToElement(element);

  return false;
}

function validateRequiredInput(selector, message) {
  var el = $(selector);

  if (!el.length) return true;

  if ($.trim(el.val()) == "") {
    return showError(el, message);
  }

  return true;
}

function validateSelect2(selector, message) {
  var el = $(selector);

  if (!el.length) return true;

  if (el.val() == "" || el.val() == null || el.val() == "0") {
    $(".validation").remove();

    el.next(".select2").after(
      '<ul class="validation"><li>' + message + "</li></ul>",
    );

    scrollToElement(el.next(".select2"));

    return false;
  }

  return true;
}

function validateDropzone() {
  var value = $("input[name='images']").val();

  if (!value || value === "" || value === "[]") {
    $(".validation").remove();

    $(".dz-message.member").after(
      '<ul class="validation"><li>Please upload at least one property image.</li></ul>',
    );

    scrollToElement($("#multiple-upload"));

    return false;
  }

  return true;
}

function validateDocuments() {
  console.log("validateDocuments() called");

  $(".validation").remove();

  var valid = true;

  $("input[name='documents[]']").each(function (index) {
    console.log("Document", index + 1, "files:", this.files.length);

    if ($(this).data("required") == "required") {
      if (this.files.length === 0) {
        console.log("Missing document", index + 1);

        $(this).after(
          '<ul class="validation"><li>This document is required.</li></ul>',
        );

        scrollToElement($(this));

        valid = false;

        return false;
      }
    }
  });

  console.log("Returning:", valid);

  return valid;
}

//==============================================================
// Submit
//==============================================================

$("#btnSave").click(function (e) {
  console.log("REAL MEMBER USER JS CLICK HANDLER");
  $(window).off("beforeunload");

  e.preventDefault();

  removeErrors();

  //----------------------------------------------------------
  // Property Information
  //----------------------------------------------------------

  // Title
  if (!validateRequiredInput("#name", "Title is required")) return;
  // Country
  if (!validateSelect2("#country_id", "Please select Country")) return;

  // State
  if (!validateSelect2("#state_id", "Please select State")) return;
  // City
  if (!validateSelect2("#city_id", "Please select City")) return;

  // City Area
  if (!validateSelect2("#city_area_id", "Please select City Area")) return;
  // Property Images (Dropzone)
  if (!validateDropzone()) return;
  // Property Location
  if (
    $.trim($("#location").val()) == "" ||
    $("#latitude").val() == "" ||
    $("#longitude").val() == ""
  ) {
    $(".validation").remove();

    $("#location").after(
      '<ul class="validation"><li>Please select Property Location.</li></ul>',
    );

    scrollToElement($("#location"));

    return;
  }
  // Required Documents
  if (!validateDocuments()) return;
  // Area
  if ($("#square").val().replace(/,/g, "").trim() == "") {
    showError($("#square"), "Area is required");
    return;
  }

  // Price
  if ($("#price-number").val().replace(/,/g, "").trim() == "") {
    showError($("#price-number"), "Price is required");
    return;
  }

  //----------------------------------------------------------
  // Member Validation
  //----------------------------------------------------------

  var member_status = $("input[name='member_status']:checked").val();

  if (member_status == "existing_user") {
    if (!validateRequiredInput("#email", "Email is required")) return;

    // Email format
    var email = $("#email").val().trim();
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailPattern.test(email)) {
      showError($("#email"), "Please enter a valid email address.");
      return;
    }

    if (!validateRequiredInput("#password", "Password is required")) return;
  }

  if (member_status == "new_user") {
    if (!validateRequiredInput("#full_name", "Full Name is required")) return;

    if (!validateRequiredInput("#new_email", "Email is required")) return;

    // Email format
    var newEmail = $("#new_email").val().trim();
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailPattern.test(newEmail)) {
      showError($("#new_email"), "Please enter a valid email address.");
      return;
    }

    if (!validateRequiredInput("#mobile_number", "Mobile Number is required"))
      return;

    // Mobile number should contain only digits
    var mobile = $("#mobile_number").val().trim();

    if (!/^[0-9]+$/.test(mobile)) {
      showError($("#mobile_number"), "Please enter a valid mobile number.");
      return;
    }

    if (!validateRequiredInput("#new_password", "Password is required")) return;
  }
  //----------------------------------------------------------
  // Terms
  //----------------------------------------------------------

  if (!$("#terms").is(":checked")) {
    $(".validation").remove();

    $("#terms")
      .parent()
      .append(
        '<ul class="validation"><li>Please accept GEM Terms & Conditions.</li></ul>',
      );

    scrollToElement($("#terms"));

    return;
  }

  //----------------------------------------------------------
  // Passed Validation
  //----------------------------------------------------------

  $(".fa-spinner").removeClass("d-none");

  var price = $("#price-number").val();
  var setprice = price.replace(",", "");

  var form = $("#form_member")[0];

  var formData = new FormData(form);

  formData.append("price", setprice);

  var email = $("#email").val();
  var password = $("#password").val();

  var name = $("#full_name").val();
  var new_email = $("#new_email").val();
  var new_password = $("#new_password").val();
  var mobile_number = $("#mobile_number").val();

  if (member_status == "existing_user") {
    formData.append("email", email);
    formData.append("password", password);
  }

  if (member_status == "new_user") {
    formData.append("member_status", "new_user");
    formData.append("full_name", name);
    formData.append("new_email", new_email);
    formData.append("mobile_number", mobile_number);
    formData.append("new_password", new_password);
  }

  formData.append("terms", true);

  let document = 1;

  $.ajax({
    type: "POST",
    url: "/member-property-save",
    processData: false,
    contentType: false,
    dataType: "json",
    data: formData,

    success: function (data) {
      $(".fa-spinner").addClass("d-none");

      if ($.isEmptyObject(data.error)) {
        setTimeout(function () {
          $(".print-error-msg").fadeOut(1500);
        }, 4000);
      } else {
        printErrorMsg(data.error);
      }

      function printErrorMsg(msg) {
        $(".print-error-msg").find("ul").html("");

        $(".print-error-msg").show();

        $.each(msg, function (key, value) {
          $(".print-error-msg")
            .find("ul")
            .append("<li>" + value + "</li>");
        });
      }

      if (data.status) {
        $(window).off("beforeunload");

        window.location.href = "/member/properties";
      } else {
        Swal.fire({
          title: data.message,
          text: "",
          icon: "error",
        });
      }
    },

    error: function (xhr) {
      $(".fa-spinner").addClass("d-none");

      removeErrors();

      if (!xhr.responseJSON || !xhr.responseJSON.errors) return;

      var errors = xhr.responseJSON.errors;

      //------------------------------------------------------
      // Server Validation
      //------------------------------------------------------

      if (errors.name) {
        showError($("#name"), errors.name[0]);

        return;
      }

      if (errors.city_id) {
        $("#city_id")
          .next(".select2")
          .after(
            '<ul class="validation"><li>' + errors.city_id[0] + "</li></ul>",
          );

        scrollToElement($("#city_id").next(".select2"));

        return;
      }

      if (errors.city_area_id) {
        $("#city_area_id")
          .next(".select2")
          .after(
            '<ul class="validation"><li>' +
              errors.city_area_id[0] +
              "</li></ul>",
          );

        scrollToElement($("#city_area_id").next(".select2"));

        return;
      }

      if (errors.images) {
        $(".dz-message.member").after(
          '<ul class="validation"><li>' + errors.images[0] + "</li></ul>",
        );

        scrollToElement($(".dz-message.member"));

        return;
      }

      if (errors.location) {
        showError($("#location"), errors.location[0]);

        return;
      }

      if (errors.square) {
        showError($("#square"), errors.square[0]);

        return;
      }

      if (errors.price) {
        showError($("#price-number"), errors.price[0]);

        return;
      }

      if (errors.email) {
        showError($("#email"), errors.email[0]);

        return;
      }

      if (errors.password) {
        showError($("#password"), errors.password[0]);

        return;
      }

      if (errors.full_name) {
        showError($("#full_name"), errors.full_name[0]);

        return;
      }

      if (errors.new_email) {
        showError($("#new_email"), errors.new_email[0]);

        return;
      }

      if (errors.mobile_number) {
        showError($("#mobile_number"), errors.mobile_number[0]);

        return;
      }

      if (errors.new_password) {
        showError($("#new_password"), errors.new_password[0]);

        return;
      }

      //------------------------------------------------------
      // Documents
      //------------------------------------------------------

      $("input[name='documents[]']").each(function () {
        if ($(this).data("required") == "required") {
          if ($(this).val() == "" && $(this).prev().attr("data-src") == "") {
            $(this).after(
              '<ul class="validation"><li>This document is required.</li></ul>',
            );

            scrollToElement($(this));

            return false;
          }
        }
      });
    },
  });
});

$(".preview-image-wrapper img").each(function () {
  var img = $(this)
    .attr("src")
    .substring($(this).attr("src").lastIndexOf("/") + 1);
  $(this).attr("src", "/storage/documents/" + img); // Set herf value
});

function open_div(ob) {
  if (ob.value == "new_user") {
    $(".new_user").css("display", "block");
    $(".existing_user").css("display", "none");
    $(".existing_user :input").attr("disabled", true);
    $(".new_user :input").attr("disabled", false);
    $("#validation_login").empty();
    $("#validation").empty();
  }
  if (ob.value == "existing_user") {
    $(".existing_user").css("display", "block");
    $(".new_user").css("display", "none");
    $(".new_user :input").attr("disabled", true);
    $(".existing_user :input").attr("disabled", false);
    $(".new_user :input").attr("disabled", true);
    $("#validation_register").empty();
    $("#validation").empty();
  }
}

$(".showContact").click(function () {
  $(".show_contact_detail").removeClass("d-none");
});
$("#plugins-real-estate-properties").on("click", ".rate_modal", function () {
  property_id = $(this).attr("data-property-id");
  author_id = $(this).attr("data-author_id");
  $.ajax({
    type: "get",
    url: "/member/agent?id=" + author_id,

    /*data: {
            id: author_id
        },
*/
    dataType: "json",
    success: function (data) {
      console.log(data);
      $("#agent_name").text(data.data.fname + " " + data.data.lname);
      $("#agent_img").attr("src", data.data.url.encoded);
    },
    error: function (xhr, status, error) {
      console.error("Error:", error); // Log the error for debugging
      console.error("Status:", status); // Log the status for debugging
      console.error("Response:", xhr.responseText); // Log the server response
    },
  });
  $("#property_id").val(property_id);
  $("#agent_id").val(author_id);
  $("#ratingModal").modal("show");
});

$("#rate_send").click(function (e) {
  var rating = $("input[name='rating']").val();

  if (rating != "") {
    $("input[name='rating']").parent().next(".validation").remove();
    var form = $("#rateform")[0];
    var formData = new FormData(form);
    $.ajax({
      type: "POST",
      url: "/member/rate",
      processData: false,
      contentType: false,
      dataType: "json",
      data: formData,
      async: false,
      success: function (data) {
        if (data.status) {
          $.alert("Rating Added Successfully!", {
            title: "Success",
            type: "success",
          });
          setTimeout(function () {
            window.location.reload();
          }, 2000);
        } else {
          $.alert(data.message, {
            title: "Error",
            type: "danger",
          });
          $("#ratingModal").modal("hide");
        }
      },
    });
  } else {
    $("input[name='rating']").parent().next(".validation").remove();
    $("input[name='rating']")
      .parent()
      .after(
        "<div class='validation' style='color:red;margin-bottom: 20px;'>Please rate</div>",
      );
  }
});

// $('#mobile_number').keydown(function () {

//     //allow  backspace, tab, ctrl+A, escape, carriage return
//     if (event.keyCode == 8 || event.keyCode == 9 ||
//         event.keyCode == 27 || event.keyCode == 13 ||
//         (event.keyCode == 65 && event.ctrlKey === true))
//         return;
//     if ((event.keyCode < 48 || event.keyCode > 57))
//         event.preventDefault();

// });

//==============================================================
// Remove validation automatically when user fixes a field
//==============================================================

// Normal text inputs
$(document).on(
  "input",
  "input[type='text'], input[type='email'], input[type='password'], textarea",
  function () {
    $(this).next(".validation").remove();
    $(this).next(".validation_login").remove();
    $(this).next(".validation_register").remove();
  },
);

// Select2 fields
$("#city_id, #city_area_id").on("change", function () {
  $(this).next(".select2").next(".validation").remove();
});

// Location
$("#location").on("input", function () {
  $(this).next(".validation").remove();
});

// Area
$("#square").on("input", function () {
  $(this).next(".validation").remove();
});

// Price
$("#price-number").on("input", function () {
  $(this).next(".validation").remove();
});

// Property Images (Dropzone)
if (typeof Dropzone !== "undefined") {
  Dropzone.forElement(".dropzone").on("success", function () {
    $(".dz-message.member").next(".validation").remove();
  });
}

// Required Documents
$("input[name='documents[]']").on("change", function () {
  $(this).next(".validation").remove();
});

// Terms
$("#terms").on("change", function () {
  $("#terms").parent().find(".validation").remove();
});

// Existing User
$("#email,#password").on("input", function () {
  $(this).next(".validation").remove();
});

// New User
$("#full_name,#new_email,#mobile_number,#new_password").on(
  "input",
  function () {
    $(this).next(".validation").remove();
  },
);
function filterFunction() {
  var input, filter, ul, li, a, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  div = document.getElementById("dropdown-menu-member");
  a = div.getElementsByTagName("li");

  for (i = 0; i < a.length; i++) {
    txtValue = a[i].textContent || a[i].innerText;

    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      a[i].style.display = "";
    } else {
      a[i].style.display = "none";
    }
  }
}
$(window).on("load", function () {
  var validator = $(".custom_form").data("validator");

  if (validator) {
    // Validate the hidden images field
    validator.settings.ignore = ":hidden:not([name='images'])";

    // Remove the remote validation that causes the "Whoops..." message
    if (validator.settings.rules.images) {
      delete validator.settings.rules.images.laravelValidationRemote;
    }
  }
  if (!validator) {
    console.log("Validator not ready");
    return;
  }

  validator.focusInvalid = function () {
    if (!this.errorList.length) return;

    var first = $(this.errorList[0].element);

    if (first.attr("name") === "images") {
      first = $("#multiple-upload");
    }

    $("html, body").animate(
      {
        scrollTop: first.offset().top - 120,
      },
      500,
    );
  };

  console.log("focusInvalid overridden");
});
$(document).on("submit", ".custom_form", function (e) {
  // Only intercept the member property form
  if ($(this).attr("action").indexOf("/member/properties/create") === -1) {
    return true;
  }

  console.log("Member form intercepted");

  removeErrors();

  if (!validateDropzone()) {
    e.preventDefault();
    return false;
  }

  if (!validateDocuments()) {
    e.preventDefault();
    return false;
  }
});
$(document).ready(function () {
  // Load States
  $("#country_id").on("change", function () {
    let countryId = $(this).val();

    $("#state_id").html('<option value="">Loading...</option>');
    $("#city_id").html('<option value="">Select State First</option>');
    $("#city_area_id").html('<option value="">Select City Area</option>');

    if (!countryId) {
      $("#state_id").html('<option value="">Select Country First</option>');
      return;
    }

    $.get(
      "/ajax/states",
      {
        country_id: countryId,
      },
      function (data) {
        let options = '<option value="">Select State</option>';

        $.each(data, function (index, state) {
          options +=
            '<option value="' + state.id + '">' + state.name + "</option>";
        });

        $("#state_id").html(options);
      },
    ).fail(function (xhr) {
      console.log(xhr.responseText);
      alert("Unable to load states.");
    });
  });

  // Load Cities
  $("#state_id").on("change", function () {
    let stateId = $(this).val();

    $("#city_id").html('<option value="">Loading...</option>');
    $("#city_area_id").html('<option value="">Select City Area</option>');

    if (!stateId) {
      $("#city_id").html('<option value="">Select State First</option>');
      return;
    }

    // CHANGED ROUTE HERE
    $.get(
      "/ajax/property-cities",
      {
        state_id: stateId,
      },
      function (data) {
        let options = '<option value="">Select City</option>';

        $.each(data, function (index, city) {
          options +=
            '<option value="' + city.id + '">' + city.name + "</option>";
        });

        $("#city_id").html(options);
      },
    ).fail(function (xhr) {
      console.log(xhr.responseText);
      alert("Unable to load cities.");
    });
  });
});
