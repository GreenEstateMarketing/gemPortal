


$(window).ready(function () {
    $("#discount_form").validate({
        rules: {
            voucher : {
                required: true,

            },
        },
        messages:{
            voucher:'Voucher is required',


        },
        submitHandler: function(form){
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                dataType:'json',
                success: function(response) {

                    if(response.status)
                    {

                        $(".discount-detial").removeClass("d-none");
                        $("#discount_per").html(response.data.discount_percent);
                        $("#discount_price").html(response.data.discount);
                        $(".error-message").addClass("d-none");
                        $(".success-message").removeClass("d-none");
                        $(".alert-success").removeClass("d-none");
                        $("#alert-success").html(response.message);
                        $(".alert-danger").addClass("d-none");
                        total=$("#total_price").text();
                        total_dic=total-response.data.discount;
                        $("input[name='amount']").val(total_dic);
                        $("#total_price").text(total_dic);

                    }
                    else
                    {
                        $(".success-message").addClass("d-none");
                        $(".alert-success").addClass("d-none");
                        $(".error-message").removeClass("d-none");
                        $(".alert-danger").removeClass("d-none");
                        if(response.message!='The voucher was already redeemed.')
                            $(".discount-detial").addClass("d-none");
                            $("#alert-danger").html(response.message);


                    }
                }
            });
        }
    });




});
