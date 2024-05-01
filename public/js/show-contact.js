$(".showContact").click(function () {


        a_id=$(this).attr('data-id');
    $(".fa-spinner").removeClass('d-none');

    $.ajax({
        type: 'get',
        url: "/api/v1/agent-data",

        dataType: 'json',
        data: {id:a_id},
        async: false,
        success: function (data) {

            setTimeout(function() {
                $(".fa-spinner").addClass('d-none');
                if(data.phone!=null) {
                    $(".mobile-p").removeClass('d-none');
                    $("#mobile_text").text(data.phone);
                }
                $("#email_text").text(data.email);
                $('.contactInfo').removeClass("d-none");
                setTimeout(function() {
                    $('.contactInfo').addClass("d-none");
                    $("#mobile_text").text("");
                    $("#email_text").text("");
                }, 6000);
            }, 1000);

          //  $('.contactInfo').delay(5000).fadeOut('slow');
           // $('.contactInfo').delay(10000).addClass("d-none");

        },

    });
    //
});
