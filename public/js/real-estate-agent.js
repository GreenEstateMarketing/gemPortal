
$(document).ready(function() {
    $("#add_comment").click(function(e) {

        var comment = $("input[name='comment']").val();
        if(comment!="") {
            $("input[name='comment']").parent().next(".validation").remove();
            var form = $('#agent_comment')[0];
            var formData = new FormData(form);
            $.ajax({
                type: 'POST',
                url: "/comment/store",
                 processData: false,
                 contentType: false,
                dataType: 'json',
                data: formData,
                async: false,
                success: function (data) {
                    $("input[name='comment']").val('');

                       if ($(".d-comment").find(".display-comment").length)
                    {

                        //   alert(data.admin_url.encoded);
                        $(".display-comment:last").append('<img src="' + data.agent_url.encoded + '" class="br-100 v-mid mr-2" style="width: 30px;"><strong>' + data.user.first_name + '</strong><p class="comment-desp">' + data.comment + '</p>');
                    }
                    else{

                           //   alert(data.admin_url.encoded);
                           $(".comment-box").find(".d-comment").append('<div class="display-comment"><img src="' + data.agent_url.encoded + '" class="br-100 v-mid mr-2" style="width: 30px;"><strong>' + data.user.first_name + '</strong><p class="comment-desp">' + data.comment + '</p></div>');


                       }

                },

            });
        }
        else{
            $("input[name='comment']").parent().next(".validation").remove();
            $("input[name='comment']").parent().after("<div class='validation' style='color:red;margin-bottom: 20px;'>Please enter comment</div>");


        }
    });
    $(".delete-crud-entry").click(function(e) {
       //ajax to call custom php function
        $.ajax({
            url: "/account/getConsultCount",
            type: "get",
            dataType: 'json',
            //async:false,
           // data:{category_id:$("#category_id").val()},

            success: function (response) {
                if(response.status)
                {
                    count=response.count;
                    latest=count-1;
                    if(latest <= 0)
                        latest='';
                    $("#consult_count").html(latest);
                }

            },
        });

    });
});
