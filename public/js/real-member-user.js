///////////////agent list//////////////

$(window).on("load", function () {
    $('#city_id').select2();

    $('#city_area_id').select2();
    $('#location_input').select2();
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
            dataType: 'json',
            data: {
                longitude: longitude,
                latitude: latitude

            },
            success: function (response) {
                $("#dropdown-menu-member").empty();
                //$("#dropdown-menu-member").append(');
                $("#dropdown-menu-member").append('<input type="text" placeholder="Search.." class="form-control" id="myInput" onKeyUp="filterFunction()">');

                if (response.length > 0) {
                    $.each(response, function (key, value) {
                        console.log(value.img_src);
                        if (value.rating) {
                            $("#dropdown-menu-member").append('<li class="dropdown-item" data-agent-id="' + value.id + '"><img src="' + value.img_src.encoded + '" width="50px" height="50px" class="br-100 v-mid mr1">  ' + value.first_name + ' ' + value.last_name + ' (' + value.rating + '<i class="fa fa-star" style="color:#f3a54a" aria-hidden="true"></i>)</li>');
                        } else {
                            $("#dropdown-menu-member").append('<li class="dropdown-item"  data-agent-id="' + value.id + '"><img src="' + value.img_src.encoded + '" width="50" height="50"  class="br-100 v-mid mr1">  ' + value.first_name + '  ' + value.last_name + ' </li>');

                        }
                        //       $("#agent_list").append('  <img src="/themes/real-scout/images/team01.jpeg" class="br-100 v-mid mr-1" style="width: 30px;"><option value='+value.id+'>'+value.first_name+' ('+value.rating+')</option>');
                        // else
                        //     $("#agent_list").append(' <img src="/themes/real-scout/images/team01.jpeg" class="br-100 v-mid mr-1" style="width: 30px;"><option value='+value.id+'>'+value.first_name+'</option>');
                    });
                } else {

                    $("#dropdown-menu-member").append('<li class="dropdown-item disabled" disabled="disabled">No Agent Found in this Area</li>');

                }

            },
        });
    }

    agent_list();

    $('#latitude').on('change', function () {
        agent_list();
    });

});
$(document).ready(function () {

    var member_status = $("input[name='member_status']:checked").val();
    $.ajax({
        type: 'get',
        url: "/api/v1/get-term-conditions",
        /* processData: false,
         contentType: false,*/
        dataType: 'json',
        async: false,
        success: function (data) {

            $("#term_condition_body").html(data.html);
        },

    });
    $('.dropdown-item').on('click', function () {

    });

    $(document).on("click", ".custom-select-agent", function (event) {

        $("#text_agent").html("Agent List");
        $(".custom-select-agent").addClass('d-none');
        $(".agent-detail").addClass('d-none');
        $("#agent_list").val("");
    });

    $(document).on("click", "#dropdown-menu-member li", function (event) {
        var btnObj = $(this).parent().siblings('button');
        $("#text_agent").html($(this).text());
        //$(btnObj+" <span class='close_btn'>").html($(this).text());
        $(".custom-select-agent").removeClass('d-none');
        $("#agent_list").val($(this).attr('data-agent-id'));
        $(".show_contact_detail").addClass("d-none");
        if ($(this).attr('data-agent-id') == "") {
            alert('no id found')
            $(".agent-detail").addClass('d-none');
        } else {
            $("input[name='author_id_hidden']").val($(this).attr('data-agent-id'))
            $.ajax({
                url: "/api/v1/agent-data",
                type: "get",
                dataType: 'json',
                data: {
                    id: $(this).attr('data-agent-id')
                },
                async: false,
                success: function (response) {
                    if (response.phone == null) {

                        $(".showContact").css('display', 'none');
                    } else {
                        $(".showContact").css('display', 'block');
                    }
                    $(".agent-image").attr('src', response.avatar_url.encoded);
                    $("#agent_name").text(response.first_name + " " + response.last_name);
                    $("#agent_email").text(response.email);
                    $("#agent_desc").html(response.description);
                    $("#agent_no").html(response.phone);
                    $(".agent-detail").removeClass('d-none');

                },
            });
        }
    });
    $('#modal_terms').on('click', function () {

        if ($("input[name='modal_terms']:checked")) {
            $("#terms").prop("checked", true);
            $("#exampleModal").modal("hide");
            $(document.body).removeClass("modal-open");
            $(".modal-backdrop").remove();
        } else {


        }
    });
    $("#form_member").validate({
        rules: {
            name: {
                required: true,

            },
            city_id: {
                required: true,

            },
            city_area_id: {
                required: true,

            },
            /*email: {
                   required: true,
                   email: true
               },*/
            password: {
                required: true,

            },
            email: {

                required: function () {
                    if ($("input[name='member_status']:checked").val() == "existing_user") {

                        return true;
                    } else {
                        return false;
                    }
                },
                email: true

            },
            full_name: {

                required: function () {
                    if ($("input[name='member_status']:checked").val() == "new_user") {
                        return true;
                    } else {
                        return false;
                    }
                }

            },
            new_email: {

                required: function () {
                    if ($("input[name='member_status']:checked").val() == "new_user") {
                        return true;
                    } else {
                        return false;
                    }
                },
                email: true

            },
            mobile_number: {

                required: function () {
                    if ($("input[name='member_status']:checked").val() == "new_user") {
                        return true;
                    } else {
                        return false;
                    }
                },
                number: true
            },
            new_password: {

                required: function () {
                    if ($("input[name='member_status']:checked").val() == "new_user") {
                        return true;
                    } else {
                        return false;
                    }
                },

            },
            terms: {
                required: true
            },
            images: {
                required: true
            }
        },
        messages: {
            name: 'Title is required',
            city_id: 'City is required',
            city_area_id: 'City Area is required',
            email: 'Email is required',
            password: 'Password is required',
            full_name: 'Full Name is required',
            new_email: 'Email is required',
            mobile_number: 'Mobile Number is required',
            new_password: 'New Password is required',
            terms: 'Accept GEM Terms & Conditions',
            images: 'Image Field is required'

        }
    });

});

///////on change to get agent data///
$(".dropdown-item").change(function (e) {
    $(".show_contact_detail").addClass("d-none");
    if ($(this).attr('data-agent-id') == "") {
        $(".agent-detail").addClass('d-none');
    } else {
        $.ajax({
            url: "/api/v1/agent-data",
            type: "get",
            dataType: 'json',
            data: {
                id: $(this).attr('data-agent-id')
            },
            async: false,
            success: function (response) {

                if (response.phone == null) {

                    $(".showContact").css('display', 'none');
                } else {
                    $(".showContact").css('display', 'block');
                }
                $("#agent_name").text(response.first_name + " " + response.last_name);
                $("#agent_email").text(response.email);
                $("#agent_desc").html(response.description);
                $("#agent_no").html(response.phone);
                $(".agent-detail").removeClass('d-none');

            },
        });
    }
});

$("#btnSave").click(function (e) {
    $(window).off('beforeunload');
    e.preventDefault();
    res = $("#form_member").validate();


    if (res) {
        $(".fa-spinner").removeClass('d-none');
        // var formdata = $("#form_member").serialize(); // here $(this) refere to the form its submitting
        var price = $("#price-number").val();
        var setprice = price.replace(",", "");
        var form = $('#form_member')[0];
        var formData = new FormData(form);
        formData.append("price", setprice);
        var email = $("#email").val();
        var password = $("#password").val();
        ////////register//////
        var name = $("#full_name").val();
        var new_email = $("#new_email").val();
        var new_password = $("#new_password").val();
        var mobile_number = $("#mobile_number").val();
        $('.validation').remove();
        var member_status = $("input[name='member_status']:checked").val();
        var submit = 1;

        if (member_status == "existing_user") {
            $('.validation_register').remove();
            $('.validation_login').remove();
            if (password == "") {
                submit = 0;
                $("input[name='password']").after('<ul class="validation_login"><li>password is required</li></ul>');
            } else {
                formData.append("password", password);
            }
            if (email == "") {
                submit = 0;
                $("input[name='email']").after('<ul class="validation_login"><li>email is required</li></ul>');
            } else {
                formData.append("email", email);
            }
            if (!$("input[name='terms']").is(':checked')) {
                submit = 0;
                // $("input[name='terms']").siblings().after('<ul class="validation_login"><li>Please accept GEM terms & condition</li></ul>')
            } else {
                formData.append("terms", true);
            }
        }

        if (member_status == "new_user") {
            formData.append("member_status", "new_user");
            $('.validation_register').remove();
            $('.validation_login').remove();
            if (name == "") {
                submit = 0;
                $("input[name='full_name']").after('<ul class="validation_register"><li>full name is required</li></ul>');
            } else {
                formData.append("full_name", name);
            }
            if (new_email == "") {
                submit = 0;
                $("input[name='new_email']").after('<ul class="validation_register"><li>email is required</li></ul>');
            } else {
                formData.append("new_email", new_email);
            }
            if (mobile_number == "") {
                submit = 0;
                $("input[name='mobile_number']").after('<ul class="validation_register"><li>mobile number is required</li></ul>');
            } else {
                formData.append("mobile_number", mobile_number);
            }
            if (new_password == "") {
                submit = 0;
                $("input[name='new_password']").after('<ul class="validation_register"><li>password is required</li></ul>');
            } else {
                formData.append("new_password", new_password);
            }
            if (!$("#terms").is(':checked')) {
                submit = 0;
                // $("input[name='terms']").after('<ul class="validation_register"><li>Please accept GEM terms & condition</li></ul>');
            } else {
                formData.append("terms", true);
            }
        }

        let document = 1;

        if (document == 1) {
            $.ajax({
                type: 'POST',
                url: "/member-property-save",
                processData: false,
                contentType: false,
                dataType: 'json',
                data: formData,
                success: function (data) {
                    $(".fa-spinner").addClass('d-none');
                    if ($.isEmptyObject(data.error)) {
                        setTimeout(function () {
                            $(".print-error-msg").fadeOut(1500);
                        }, 4000);
                    } else {
                        $(".fa-spinner").addClass('d-none');
                        printErrorMsg(data.error);
                    }

                    function printErrorMsg(msg) {
                        $(".print-error-msg").find("ul").html('');
                        $(".print-error-msg").css('display', 'block');
                        $.each(msg, function (key, value) {
                            $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
                        });
                    }

                    if (data.status) {
                        /*Swal.fire({
                            title: 'Property Added Success!',
                            text: '',
                            icon: 'success',
                            confirmButtonText: 'Go Dashboard'
                        }).then(function () {

                        });*/
                        window.location.href = "/member/properties";
                        $(window).off('beforeunload');
                    } else {
                        Swal.fire({
                            title: data.message,
                            text: '',
                            icon: 'error'
                        });
                    }
                },

            }).fail(function (data) {
                $(".fa-spinner").addClass('d-none');
                var response = JSON.parse(data.responseText);
                $('.validation').remove();
                $.each(response.errors, function (key, value) {
                    var errorString = '<ul  class="validation mt-2"><li>' + value + '</li></ul>';

                    $("input[name='" + key + "']").after(errorString);
                    if (key == 'city_id') {
                        $("label[for='city_id']").siblings().after(errorString);
                    }
                    if (key == 'city_area_id') {
                        $("label[for='city_area_id']").siblings().after(errorString);
                    }
                    //$("label[for='city_id']").siblings().after(errorString);
                    $("label[for='" + key + "']").focus();
                });
                $('input[name^="documents"]').each(function () {
                    if ($(this).attr("data-required") == "required") {

                        if ($(this).val() == "" && $(this).prev().attr('data-src') == "") {
                            document = 0;
                            $(this).css('border', '1px solid red');
                            $(this).focus();
                        } else {
                            document = 1;
                            $(this).css('border', '');
                        }
                    }
                });
            });
        } else {
            $(window).scrollTop(0);
            return false;
        }
        //  }
    }
});

$('.preview-image-wrapper img').each(function () {

    var img = $(this).attr("src").substring($(this).attr("src").lastIndexOf('/') + 1);
    $(this).attr("src", "/storage/documents/" + img); // Set herf value
});

function open_div(ob) {
    if (ob.value == "new_user") {
        $(".new_user").css('display', 'block');
        $(".existing_user").css('display', 'none');
        $(".existing_user :input").attr('disabled', true);
        $(".new_user :input").attr('disabled', false);
        $('#validation_login').empty();
        $('#validation').empty();
    }
    if (ob.value == "existing_user") {
        $(".existing_user").css('display', 'block');
        $(".new_user").css('display', 'none');
        $(".new_user :input").attr('disabled', true);
        $(".existing_user :input").attr('disabled', false);
        $(".new_user :input").attr('disabled', true);
        $('#validation_register').empty();
        $('#validation').empty();
    }
}

$(".showContact").click(function () {
    $(".show_contact_detail").removeClass("d-none");
});
$('#plugins-real-estate-properties').on('click', '.rate_modal', function () {
    property_id = $(this).attr('data-property-id');
    author_id = $(this).attr('data-author_id');
    $.ajax({
        type: 'get',
        url: "/member/agent?id=" + author_id,

        /*data: {
            id: author_id
        },
*/
        dataType: 'json',
        success: function (data) {
            console.log(data)
            $('#agent_name').text(data.data.fname + ' ' + data.data.lname);
            $('#agent_img').attr('src', data.data.url.encoded);
        },
        error: function (xhr, status, error) {
            console.error('Error:', error); // Log the error for debugging
            console.error('Status:', status); // Log the status for debugging
            console.error('Response:', xhr.responseText); // Log the server response
        }
    });
    $('#property_id').val(property_id);
    $('#agent_id').val(author_id);
    $('#ratingModal').modal('show');
});

$("#rate_send").click(function (e) {

    var rating = $("input[name='rating']").val();

    if (rating != "") {
        $("input[name='rating']").parent().next(".validation").remove();
        var form = $('#rateform')[0];
        var formData = new FormData(form);
        $.ajax({
            type: 'POST',
            url: "/member/rate",
            processData: false,
            contentType: false,
            dataType: 'json',
            data: formData,
            async: false,
            success: function (data) {

                if (data.status) {
                    $.alert('Rating Added Successfully!', {
                        title: 'Success',
                        type: 'success',
                    });
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                } else {
                    $.alert(data.message, {
                        title: 'Error',
                        type: 'danger'
                    });
                    $('#ratingModal').modal('hide');
                }

            },

        });
    } else {
        $("input[name='rating']").parent().next(".validation").remove();
        $("input[name='rating']").parent().after("<div class='validation' style='color:red;margin-bottom: 20px;'>Please rate</div>");


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
