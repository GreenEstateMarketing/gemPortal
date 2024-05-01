


$(document).ready(function () {
    $('#city_id').select2();
    $('#city_area_id').select2();
    $('#mobile_no').keydown(function() {

        //allow  backspace, tab, ctrl+A, escape, carriage return
        if (event.keyCode == 8 || event.keyCode == 9 ||
            event.keyCode == 27 || event.keyCode == 13 ||
            (event.keyCode == 65 && event.ctrlKey === true))
            return;
        if ((event.keyCode < 48 || event.keyCode > 57))
            event.preventDefault();

    });
    $("#contact").validate({
        rules: {
            city_id : {
                required: true,
            },
            city_area_id : {
                required: true,
            },

            mobile_no:{
                required: true,
            },
            email : {
                required: true,
                email:true
            },
            name : {
                required: true,
            },
            comments:{
                required:true
            }
        },
        messages:{
            name:'Name is required',
            email:'Email is required',
            mobile_no:'Mobile No. is required',
            city_id:'Choose city from list',
            city_area_id:'Choose city area from list',
            comments:'Message is required',


        },
        invalidHandler: function(form, validator) {
            var errors = validator.numberOfInvalids();
            $(this).find(":input.error:first").focus();

        },
        errorPlacement: function (error, element) {
            console.log('dd', element.attr("name"))
            if (element.attr("name") == "city_id") {
                error.appendTo("#error_city");
            } else if(element.attr("name") == "city_area_id") {
                error.appendTo("#error_city_area");
            }else{
                error.insertAfter(element)
            }
        },
        submitHandler: function(form){
            $(".fa-spinner").removeClass('d-none');
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                dataType:'json',
                success: function(response) {
                    $(".fa-spinner").addClass('d-none');

                    if($.isEmptyObject(response.error)){
                        console.log(response.success);
                    }else{
                        printErrorMsg(response.error);
                    }

                    function printErrorMsg (msg) {
                        $(".print-error-msg").find("ul").html('');
                        $(".print-error-msg").css('display','block');
                        $.each( msg, function( key, value ) {
                            $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
                        });
                    }

                    if(response.error)
                    {
                        $(".alert-success").css('display','none');
                        // $(".validation-error").css('display','block');
                        // $(".validation-error span").text(response.message);

                    }
                    else
                    {
                        $('#contact').trigger("reset");
                        $(".close").trigger("click");
                        $(".alert-success").css('display','block');
                        $(".validation-error").css('display','none');
                        $(".alert-success span").text(response.success);


                    }
                }
            });
        }
    });
$("#submit_Btn").click(function () {
    if($("#contact").valid())
    {
        $("#contact").submit();
    }
    else
    {
        return false;
    }

    setTimeout(function() {
        $(".alert-success").hide();
    }, 4000);
    setTimeout(function() {
        $(".alert-danger").hide();
    }, 4000);


});




});
