/*const jquery-validation = require('jquery-validation');*/
///////////////agent list//////////////

$(window).on("load", function () {
    // Handler for .load() called.
    $('.city_id').on('change', function () {
        var city_id = $(this).val();
        $.ajax({
            type: 'get',
            url: '/ajax/get-city-areas',
            dataType: 'json',
            async: false,
            data: {
                city_id: city_id
            },
            success: function (response) {

                $('#city_area_id').empty();
                $('#city_area_id').html('<option selected value> Select city area... </option>');
                $.each(response.data, function (i, item) {
                    $('#city_area_id').append('<option value="'
                        + item.id
                        + '">'
                        + item.city_area_name
                        + '</option>'
                    );

                });
            }
        });
    });
    $(".info-area-icon").after('<div class="infoTitle d-none"></div> <i class="showInfoArea ml-2 active fas fa-info-circle" style="cursor: pointer" title=""></i> ');
    //ajax to get area units data

    $(".showInfoArea").click(function () {


        $.ajax({
            url: "/api/v1/area-units",
            type: "get",
            dataType: 'json',
            async: false,
            data: { area: $("#square").val(), unit: $("#area_units").val() },

            success: function (response) {

                $(".infoTitle").html(response.html);
                $(".infoTitle").removeClass('d-none');
            },
        });

    });

    $(".showInfoArea").mouseleave(function () {
        $(".infoTitle").addClass('d-none');
    });

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
            async: false,
            success: function (response) {

                $("#dropdown-menu-admin").empty();
                $("#dropdown-menu-checklist").empty();

                //$("#dropdown-menu-admin").append(');
                $("#dropdown-menu-admin").append('<input type="text" placeholder="Search.." class="form-control" id="myInputadmin" onKeyUp="filterFunction()">');
                $("#dropdown-menu-checklist").append('<input type="text" placeholder="Search.." class="form-control" id="myInputchecklist" onKeyUp="filterFunctionCheck()">');
                if (response.length > 0) {
                    $.each(response, function (key, value) {
                        if (value.rating) {
                            $("#dropdown-menu-admin").append('<li class="dropdown-item" data-agent-id="' + value.id + '"><img src="' + value.img_src.encoded + '" width="50px" height="50px" class="br-100 v-mid mr-2">  ' + value.first_name + '' + value.last_name + ' (' + value.rating + '<i class="fa fa-star" style="color:#f3a54a" aria-hidden="true"></i>)</li>');
                            $("#dropdown-menu-checklist").append('<li class="dropdown-item" data-agent-id="' + value.id + '"><img src="' + value.img_src.encoded + '" width="50px" height="50px" class="br-100 v-mid mr-2">  ' + value.first_name + '' + value.last_name + ' (' + value.rating + '<i class="fa fa-star" style="color:#f3a54a" aria-hidden="true"></i>)</li>');

                        } else {
                            $("#dropdown-menu-admin").append('<li class="dropdown-item"  data-agent-id="' + value.id + '"><img src="' + value.img_src.encoded + '" width="50" height="50"  class="br-100 v-mid mr-2">  ' + value.first_name + ' ' + value.last_name + '</li>');
                            $("#dropdown-menu-checklist").append('<li class="dropdown-item" data-agent-id="' + value.id + '"><img src="' + value.img_src.encoded + '" width="50px" height="50px" class="br-100 v-mid mr-2">  ' + value.first_name + '' + value.last_name + '</li>');

                        }
                    });
                }
                else {

                    $("#dropdown-menu-admin").append('<li class="dropdown-item disabled" disabled="disabled">No Agent Found in this Area</li>');
                    $("#dropdown-menu-checklist").append('<li class="dropdown-item disabled" disabled="disabled">No Agent Found in this Area</li>');

                }
            },
        });
    }
    agent_list();

    $('#latitude').on('change', function () {
        agent_list();
    });
    property_id = $("input[name='property_id']").val();
    category_id = $("input[name='category_id']").val();
    if (property_id == "") {
        $(".p-category").first().trigger("click")

        var myObject = { '$a': 'number_bedroom', '$b': 'number_bathroom', '$c': 'square', '$d': 'type', '$e': 'area_units', '$f': 'category_name', '$g': 'number_floor' };
        //   setTemplateVariables(myObject);
    } else {
        $(".p-subcategory").each(function () {
            $(this).removeClass('label-secondary').addClass('label-sub-category');
            if ($(this).attr('data-id') == category_id) {
                $(this).removeClass('label-sub-category').addClass('label-primary');
                $(this).html('<span class="category-tick-selected"><i class="fas fa-check"></i></span> ' + $(this).attr('data-category_name'));
                // $(this).parent().parent().css('display','block');
                var p_id = $(this).attr("data-id");
                $(".pcateory_data").html($(this).parent().parent().html());
                return false;
            }
        });

        agent_id = $("input[name='author_id_hidden']").val();
        liObj = $("#dropdown-menu-admin").find('li[data-agent-id=' + agent_id + ']');
        var btnObj = liObj.parent().siblings('button');
        $(btnObj).text(liObj.text());
        ////popup agent
        liObj = $("#dropdown-menu-checklist").find('li[data-agent-id=' + agent_id + ']');
        var btnObj = liObj.parent().siblings('button');
        $(btnObj).text(liObj.text());
        $("#author_id").val(agent_id);

    }
    /*$(window).on('click', '.p-subcategory', function() {


        $(this).siblings().removeClass("label-primary").addClass('label-sub-category');
        $(this).removeClass("label-sub-category").addClass('label-primary');
        $(".category-tick-selected").html('');
        $(this).html('<span class="category-tick-selected"><i class="fas fa-check"></i></span> '+$(this).attr('data-category_name'));
        var p_id= $(this).attr("data-id");
        $("input[name='category_id']").val(p_id);
        $("input[name='category_name']").val($(this).attr('data-category_name'));
        getDocumentsList();
        ///template setting////
        $default=$(this).attr('data-category_name');
        temp_list();
        var myObject = {'$a' : 'number_bedroom', '$b' : 'number_bathroom','$c':'square','$d':'type','$e':'area_units','$f':'category_name','$g':'number_floor'  };
        setTemplateVariables(myObject);

    });*/



    //$(".pcateory_data .sub-category .p-subcategory:nth-child(1)").click();

});
$(window).ready(function () {
    $(document).on("click", "#dropdown-menu-admin li", function (event) {

        var btnObj = $(this).parent().siblings('button');
        $(btnObj).text($(this).text());
        //$(btnObj).val($(this).text());
        $("#author_id").val($(this).attr('data-agent-id'));
        $(".show_contact_detail").addClass("d-none");
        if ($(this).attr('data-agent-id') == "") {
            $(".agent-detail").addClass('d-none');
        }
        else {
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
                    }
                    else {
                        $(".showContact").css('display', 'block');
                    }
                    $(".agent-image").attr('src', response.avatar_url.encoded);
                    /*$("#agent_name").text(response.first_name+" "+response.last_name);
                    $("#agent_email").text(response.email);
                    $("#agent_desc").html(response.description);
                    $("#agent_no").html(response.phone);
                    $(".agent-detail").removeClass('d-none');*/
                    //
                    $(".agent-detail-admin").removeClass('d-none');
                    $("#agent_name_admin").text(response.first_name + " " + response.last_name);
                    $("#agent_email_admin").text(response.email);
                    $("#agent_desc_admin").html(response.description);
                    if (response.phone == null) {
                        $(".phone-icon").css('display', 'none');
                        $("#agent_no_admin").html('');
                    }
                    else {
                        $(".phone-icon").css('display', 'block');
                        $("#agent_no_admin").html(response.phone);
                    }

                },
            });
        }
    });
    $(document).on("click", "#dropdown-menu-checklist li", function (event) {

        var btnObj = $(this).parent().siblings('button');
        $(btnObj).text($(this).text());
        //$(btnObj).val($(this).text());
        $("#author_id").val($(this).attr('data-agent-id'));
        $(".show_contact_detail").addClass("d-none");
        if ($(this).attr('data-agent-id') == "") {
            $(".agent-detail").addClass('d-none');
        }
        else {
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
                    }
                    else {
                        $(".showContact").css('display', 'block');
                    }
                    $(".agent-image").attr('src', response.avatar_url.encoded);
                    /*$("#agent_name").text(response.first_name+" "+response.last_name);
                    $("#agent_email").text(response.email);
                    $("#agent_desc").html(response.description);
                    $("#agent_no").html(response.phone);
                    $(".agent-detail").removeClass('d-none');*/
                    //
                    $(".agent-detail-admin").removeClass('d-none');
                    $("#agent_name_admin").text(response.first_name + " " + response.last_name);
                    $("#agent_email_admin").text(response.email);
                    $("#agent_desc_admin").html(response.description);
                    if (response.phone == null) {
                        $(".phone-icon").css('display', 'none');
                        $("#agent_no_admin").html('');
                    }
                    else {
                        $(".phone-icon").css('display', 'block');
                        $("#agent_no_admin").html(response.phone);
                    }

                },
            });
        }
    });

    moderation_status = $("input[name='moderation_status_hidden']").val();

    if (moderation_status == "rejected" /*&& property_id > 0*/) {

        $("#reject_reason").parent().removeClass('d-none');
    }
    else {
        $("input[name='reject_reason']").val('');
        $("#reject_reason").parent().addClass('d-none');
    }
    $("#moderation_status").change(function () {
        if ($(this).val() == "rejected" /*&& property_id > 0*/) {

            $("#reject_reason").parent().removeClass('d-none');
        }
        else {
            $("input[name='reject_reason']").val('');
            $("#reject_reason").parent().addClass('d-none');
        }
    });


});
$(document).ready(function () {
    $(document).on('click', '[data-id="sale"]', function () {
        $('#proj-1').css('display', 'none');
        $('#proj-2').css('display', 'none');
        $('[data-id="project"]').removeClass('label-primary');
        $('[data-id="project"]').addClass('label-secondary');
        $('button[data-id="project"]').find('.tick-selected').empty();
        $("li.p-subcategory.label-primary").each(function () {
            if ($(this).find("span i").length > 0) {
                $(this).click();
            }
        });
    });
    $(document).on('click', '[data-id="rent"]', function () {
        $('#proj-1').css('display', 'none');
        $('#proj-2').css('display', 'none');
        $('[data-id="project"]').removeClass('label-primary');
        $('[data-id="project"]').addClass('label-secondary');
        $('button[data-id="project"]').find('.tick-selected').empty();
        $("li.p-subcategory.label-primary").each(function () {
            if ($(this).find("span i").length > 0) {
                $(this).click();
            }
        });
    });
    $(document).on('click', '[data-id="project"]', function () {
        $('[data-id="project"]').removeClass('label-secondary');
        $('[data-id="project"]').addClass('label-primary');
        $('[data-id="sale"]').removeClass('label-primary');
        $('[data-id="rent"]').removeClass('label-primary');
        $('[data-id="sale"]').addClass('label-secondary');
        $('[data-id="rent"]').addClass('label-secondary');

        $('button[data-id="sale"]').find('.tick-selected').empty();
        $('button[data-id="rent"]').find('.tick-selected').empty();

        $('[data-id="project"]').html('<span class="tick-selected"><i class="fas fa-check"></i></span> INVEST');

        $('#proj-1').css('display', '');
        $('#proj-2').css('display', '');

        $('#type').val('project');
    });
    function temp_list() {
        /*   var latitude=  $("#latitude").val();
           var longitude=  $("#longitude").val();
           $.ajax({
               url: "/api/v1/agent-list",
               type: "get",
               dataType: 'json',
               data: {
                   longitude:longitude,
                   latitude:latitude

               },
               success: function (response) {
                   $("#author_id").empty();
                   $("#author_id").append('<option value="">Select</option>');


                   $.each(response, function (key, value) {
                       if(value.rating)
                           $("#author_id").append('<option value='+value.id+'>'+value.first_name+' ('+value.rating+')</option>');
                       else
                           $("#author_id").append('<option value='+value.id+'>'+value.first_name+'</option>');
                   });

               },
           });*/

        $.ajax({
            url: "/api/v1/get_template",
            type: "get",
            dataType: 'json',
            async: false,
            data: { category_id: $("#category_id").val() },

            success: function (response) {
                // alert(response.detail);
                if (response.status) {
                    $("#description").val(response.html.detail);
                    $("#template_desp").val(response.html.detail);
                    $("#description").prop('readonly', true);
                }

            },
        });
    }
    temp_list();
    $(".category-parent-active").click(function () {

        $(".p-category").removeClass("category-parent-active").addClass('category-parent-inactive');
        $(this).addClass('category-parent-active');
        $(this).html('<span class="category-tick-selected"><i class="fas fa-check"></i></span> ' + $(this).attr('data-category_name'));
        $(".category-tick-selected").html('');
        var p_id = $(this).attr("data-id");
        $(".pcateory_data").html($(".p" + p_id).html());

        var c_name = $(this).attr('data-category_name');
        if (c_name == "PLOTS" || c_name == "plots" || c_name == "plot") {
            $(".category_attr").addClass('d-none');
        }
        else if (c_name == "COMMERCIALS" || c_name == "Commercials" || c_name == "COMMERCIAL" || c_name == "Commercials") {

            $(".category_attr").removeClass('d-none');
            $(".number_bedroom").addClass('d-none');
            $(".number_bathroom").addClass('d-none');

        }
        else {
            $(".category_attr").removeClass('d-none');
            $(".number_bedroom").removeClass('d-none');
            $(".number_bathroom").removeClass('d-none');


        }
        $('.pcateory_data ').find('li.p-subcategory:nth-child(1)').trigger("click");
    });
    $(".p-category").click(function () {

        $(".category-tick-selected").html('');
        $(".p-category").removeClass("label-primary").addClass('label-secondary');
        $(this).removeClass("label-secondary").addClass('label-primary');
        $(this).html('<span class="category-tick-selected"><i class="fas fa-check"></i></span> ' + $(this).attr('data-category_name'));
        var p_id = $(this).attr("data-id");
        $(".pcateory_data").html($(".p" + p_id).html());
        $("input[name='category_id']").val(p_id);
        $("input[name='category_name']").val($(this).attr('data-category_name'));
        // temp_list();
        var myObject = { '$a': 'number_bedroom', '$b': 'number_bathroom', '$c': 'square', '$d': 'type', '$e': 'area_units', '$f': 'category_name', '$g': 'number_floor' };
        //  setTemplateVariables(myObject);
        var c_name = $(this).attr('data-category_name');
        if (c_name == "PLOTS" || c_name == "plots" || c_name == "plot") {
            $(".category_attr").addClass('d-none');
        }
        else if (c_name == "COMMERCIALS" || c_name == "Commercials" || c_name == "COMMERCIAL" || c_name == "Commercials") {

            $(".category_attr").removeClass('d-none');
            $(".number_bedroom").addClass('d-none');
            $(".number_bathroom").addClass('d-none');

        }
        else {
            $(".category_attr").removeClass('d-none');
            $(".number_bedroom").removeClass('d-none');
            $(".number_bathroom").removeClass('d-none');


        }
        $('.pcateory_data ').find('li.p-subcategory:nth-child(1)').trigger("click");

    });
    $('body').on('click', '.p-subcategory', function () {

        var c_name = $(this).attr('data-parent-name');
        if (c_name == "PLOTS" || c_name == "plots" || c_name == "plot") {
            $(".category_attr").addClass('d-none');
        }
        else if (c_name == "COMMERCIALS" || c_name == "Commercials" || c_name == "COMMERCIAL" || c_name == "Commercials") {

            $(".category_attr").removeClass('d-none');
            $(".number_bedroom").addClass('d-none');
            $(".number_bathroom").addClass('d-none');

        }
        else {
            $(".category_attr").removeClass('d-none');
            $(".number_bedroom").removeClass('d-none');
            $(".number_bathroom").removeClass('d-none');


        }
        $(this).siblings().removeClass("label-primary").addClass('label-sub-category');
        $(this).removeClass("label-sub-category").addClass('label-primary');
        $(".category-tick-selected").html('');
        $(this).html('<span class="category-tick-selected"><i class="fas fa-check"></i></span> ' + $(this).attr('data-category_name'));
        var p_id = $(this).attr("data-id");
        $("input[name='category_id']").val(p_id);
        $("input[name='category_name']").val($(this).attr('data-category_name'));
        getDocumentsList();
        ///template setting////
        $default = $(this).attr('data-category_name');
        temp_list();

        var myObject = { '$a': 'number_bedroom', '$b': 'number_bathroom', '$c': 'square', '$d': 'type', '$e': 'area_units', '$f': 'category_name', '$g': 'number_floor' };
        setTemplateVariables(myObject);

    });
    property_id = $("input[name='property_id']").val();
    ///////////seting current category on edit/////////
    category_id = $("input[name='category_id']").val();
    if (property_id == "") {
        $(".p-category").first().trigger("click");
        $('.pcateory_data ').find('li.p-subcategory:nth-child(1)').trigger("click");

    }
    else {

        $(".p-subcategory").each(function () {

            if ($(this).attr('data-id') == category_id) {
                var c_name = $(this).attr('data-parent-name');
                if (c_name == "PLOTS" || c_name == "plots" || c_name == "plot") {
                    $(".category_attr").addClass('d-none');
                }
                else if (c_name == "COMMERCIALS" || c_name == "Commercials" || c_name == "COMMERCIAL" || c_name == "Commercials") {

                    $(".category_attr").removeClass('d-none');
                    $(".number_bedroom").addClass('d-none');
                    $(".number_bathroom").addClass('d-none');

                }
                else {
                    $(".category_attr").removeClass('d-none');
                    $(".number_bedroom").removeClass('d-none');
                    $(".number_bathroom").removeClass('d-none');


                }
                $(this).removeClass('label-sub-category').addClass('label-primary');
                $(this).html('<span class="category-tick-selected"><i class="fas fa-check"></i></span> ' + $(this).attr('data-category_name'));
                // $(this).parent().parent().css('display','block');
                var p_id = $(this).attr("data-id");
                $(".pcateory_data").html($(this).parent().parent().html());
                return false;
            }
        });


    }

    /*$(document).on('click', '.pcateory_data.sub-category.first_sub-category', function() {


       $(".category-tick-selected").html('');
        $(this).siblings().removeClass("label-primary").addClass('label-sub-category');
        $(this).removeClass("label-sub-category").addClass('label-primary');
        $(this).html('<span class="category-tick-selected"><i class="fas fa-check"></i></span> '+$(this).attr('data-category_name'));
        var p_id= $(this).attr("data-id");
        $("input[name='category_id']").val(p_id);
        $("input[name='category_name']").val($(this).attr('data-category_name'));
        getDocumentsList();
    });*/
    /* function setTemplateVariables(ob) {
 
       /!*  temp_update = temp.replace('$d', p_id);
         ber=$("#number_bedroom").val();
         temp_update = temp_update.replace('$a',ber);
         ///bathroom
         br=$("#number_bathroom").val();
         temp_update = temp_update.replace('$b', br);
         ///square
         sq=$("#square").val();
         temp_update = temp_update.replace('$c', sq);
         $("#description").val(temp_update);*!/
     }*/

    /* $(document).on('click', '.p-subcategory', function() {
 
 
         $(this).siblings().removeClass("label-primary").addClass('label-sub-category');
         $(this).removeClass("label-sub-category").addClass('label-primary');
         $(".category-tick-selected").html('');
         $(this).html('<span class="category-tick-selected"><i class="fas fa-check"></i></span> '+$(this).attr('data-category_name'));
         var p_id= $(this).attr("data-id");
         $("input[name='category_id']").val(p_id);
         $("input[name='category_name']").val($(this).attr('data-category_name'));
         getDocumentsList();
         ///template setting////
         $default=$(this).attr('data-category_name');
         temp_list();
         var myObject = {'$a' : 'number_bedroom', '$b' : 'number_bathroom','$c':'square','$d':'type','$e':'area_units','$f':'category_name','$g':'number_floor'  };
         setTemplateVariables(myObject);
 
     });*/
    // $(".type_sale").first().trigger("click");
    $(".type_sale").click(function () {

        $(".type_rent").removeClass("label-primary").addClass('label-secondary');
        $(".tick-selected").html('');
        $(this).removeClass("label-secondary").addClass('label-primary');
        $(this).html('<span class="tick-selected"><i class="fas fa-check"></i></span> ' + $(this).attr('data-type-name'));

        //$(this).html();
        var p_id = $(this).attr("data-id");
        $("input[name='type']").val(p_id);
        var myObject = { '$a': 'number_bedroom', '$b': 'number_bathroom', '$c': 'square', '$d': 'type', '$e': 'area_units', '$f': 'category_name', '$g': 'number_floor' };

        setTemplateVariables(myObject);
    });
    $(".type_rent").click(function () {

        $(".type_sale").removeClass("label-primary").addClass('label-secondary');
        $(".tick-selected").html('');
        $(this).removeClass("label-secondary").addClass('label-primary');
        $(this).html('<span class="tick-selected"><i class="fas fa-check"></i></span> ' + $(this).attr('data-type-name'));

        //$(this).html();
        var p_id = $(this).attr("data-id");
        $("input[name='type']").val(p_id);
        var myObject = { '$a': 'number_bedroom', '$b': 'number_bathroom', '$c': 'square', '$d': 'type', '$e': 'area_units', '$f': 'category_name', '$g': 'number_floor' };

        setTemplateVariables(myObject);
    });
    property_id = $("input[name='property_id']").val();
    moderation_status = $("input[name='moderation_status']").val();
    if ($("input[name='type']").val() == '') {
        $(".type_sale").first().trigger("click");
    }


    /* ********  wizard *****/////////////////// */


    /*///agent list//
    $.ajax({
        url: "/api/v1/agent-list",
        type: "get",
        dataType: 'json',
        data: {

        },
        success: function (response) {
            $("#agent_list_admin").empty();
            $("#agent_list_admin").append('<option value="">Select</option>');


            $.each(response, function (key, value) {
                $("#agent_list_admin").append('<option value='+value.id+'>'+value.first_name+'</option>');
            });

        },
    });*/
    /*  ///////on change to get agent data///
      $("#agent_list_admin").change(function(e){
          if($(this).val()==""){
              $(".agent-detail-admin").addClass('d-none');
          }
          else {
  
              $.ajax({
                  url: "/api/v1/agent-data",
                  type: "get",
                  dataType: 'json',
                  data: {
                      id: $(this).val()
                  },
                  success: function (response) {
                      $(".agent-detail-admin").removeClass('d-none');
                      $("#agent_name_admin").text(response.first_name+" "+response.last_name);
                      $("#agent_email_admin").text(response.email);
                      $("#agent_desc_admin").html(response.description);
                      if(response.phone==null ) {
                              $(".phone-icon").css('display','none');
                              $("#agent_no_admin").html('');
                      }
                      else{
                          $(".phone-icon").css('display','block');
                          $("#agent_no_admin").html(response.phone);
                      }
                  },
              });
          }
      });*/

    function getDocumentsList() {
        //////////get documents lists///

        property_id = $("input[name='property_id']").val();
        $(".document-row").empty();
        $(".verify-documents").empty();
        var requiredcheck = "";
        var warningcheck = "";
        var span = "";

        /* if(property_id=="") {*/
        $.ajax({
            url: "/api/v1/get-checklist-documents",
            type: "get",
            dataType: 'json',
            async: false,
            data: {
                category_id: $("input[name='category_id']").val(),
                property_id: $("input[name='property_id']").val() != "" ? $("input[name='property_id']").val() : 0
            },
            success: function (response) {
                if (response.status) {
                    $.each(response.data.documents, function (key, value) {
                        //  alert(response.data.document_images[key].id);
                        var doc_image = '<input type="hidden" data-src="">';
                        var doc_image_status = 0;
                        if (response.data.document_images) {
                        }

                        //check if it is rent as for rent the checked is not required
                        var isRent = $('.btn.type_rent.label-primary').first().val();
                        if (value.required) {
                            if (isRent) {
                                requiredcheck = "";
                            } else {
                                span = '<span class="red"> *</span>';
                                requiredcheck = "required";
                            }

                        } else {
                            requiredcheck = "";
                        }

                        ///soft delete check
                        if (property_id == "" && value.documents.is_delete == 0) {
                            if (response.data.document_images) {
                                doc_image_status = 1;
                                var path = response.data.document_images[key].path;
                                doc_image = '<br><a target="_blank" class="thisisthetest" href="/storage/' + path + '"><img src="/storage/' + path + '"  class="image-box-wrapper mb-2" width="50%" style="height:50%"></a><input type="hidden" data-src="/storage/' + path + '">';

                            }

                            $(".document-row").append('<div class="col-md-4 testsets"><label class="control-label ' + requiredcheck + '">' + value.documents.name + '</label><input type="hidden" name="document_ids[]" value=' + value.document_id + '>' + doc_image + '<input type="file"  name="documents[]" class="form-control" data-document-id="' + value.document_id + '" data-required="' + requiredcheck + '"   ' + requiredcheck + ' accept="' + value.documents.type + '"></div>');

                            //checklists add

                            $(".verify-documents").append('<div class="row">' +
                                '<div class="col">' +
                                '<input type="checkbox"  class="checklist"  data-document-id="' + value.document_id + '" name="verify_document[]" value="' + value.document_id + '"/><span> I have verified the ' + value.documents.name + ' </span>' +
                                '</div>' +
                                '</div>');
                        } else {
                            if (response.data.document_images) {
                                doc_image_status = 1;
                                var path = response.data.document_images[key].path;
                                var id = response.data.document_images[key].id;
                                /*if(id==value.id)*/
                                doc_image = '<br><a target="_blank" href="/storage/' + path + '">' + path + '</a><input type="hidden" data-src="/storage/' + path + '">';

                            }
                            /*if(response.data.document_images)
                            {
                                doc_image_status = 1;
                                var path = response.data.document_images[key].path;
                                var id = response.data.document_images[key].id;
                               if(id==value.id)
                                doc_image = '<br><a target="_blank" href="/storage/' + path + '"><img src="/storage/' + path + '"  class="image-box-wrapper mb-2" width="50%" style="height:50%"></a><input type="hidden" data-src="/storage/' + path + '">';

                            }
                            if (value.documents.is_delete==1)
                            {
                                warningcheck = ' <span class="red"><i class="fas fa-exclamation-triangle"></i>  This Document is deleted</span>';

                            }
                            else
                            {
                                warningcheck = "";
                            }*/
                            if (property_id != "") {
                                $(".document-row").append('<div class="col-md-4"><label class="control-label ' + requiredcheck + '">' + value.documents.name + '</label>' + warningcheck + '<input type="hidden" name="document_ids[]" value=' + value.document_id + '>' + doc_image + '<input type="file"  name="documents[]" class="form-control" data-document-id="' + value.document_id + '" data-required="' + requiredcheck + '"   ' + requiredcheck + '></div>');

                                //checklists add

                                $(".verify-documents").append('<div class="row">' +
                                    '<div class="col">' +
                                    '<input type="checkbox"  class="checklist"  data-document-id="' + value.document_id + '" name="verify_document[]" value="' + value.document_id + '"/><span> I have verified the ' + value.documents.name + ' </span>' +
                                    '</div>' +
                                    '</div>');
                            }
                        }
                    });
                }
            },
        });
        /* }
         else
         //show documents using jsonArray()
         {*/
        /*     json_arr= $("#json_documents").val();
             var obj = jQuery.parseJSON(json_arr);
             var requiredcheck="";
             $.each(obj, function(key,value)
             {
                 $.ajax({
                     url: "/api/v1/get-document-details",
                     type: "get",
                     dataType: 'json',
                     async:false,
                     data: {
                         document_id:value.id
                     },
                     success: function (response) {
                         if (response.data[0].category_document.required) {
                             span = '<span class="red"> *</span>';
                             requiredcheck = "required";
                         }

                         $(".document-row").append('<div class="col-md-3"><label class="control-label ' + requiredcheck + '">' + response.data[0].name + '</label><br><a  target="_blank" href="/storage/'+value.path+'"><img src="/storage/'+value.path+'" class="image-box-wrapper mb-2" width="50%" style="height:50%"></a><input type="hidden" name="document_ids[]" value=' + response.data[0].id + '><input type="file" data-document-id='+response.data[0].id+' name="documents['+response.data[0].id+']" class="form-control" ' + requiredcheck + '></div>');
                         //checklists add
                         $(".verify-documents").append('<div class="row">'+
                             '<div class="col">' +
                             '<input type="checkbox"  class="checklist"  data-document-id="'+response.data[0].id+'" name="verify_document[]" value="'+response.data[0].id+'"/><span> I have verified the '+response.data[0].name+' letter</span>' +
                             '</div>'+
                             '</div>');


                     },
                 });

             });*/

        //   }
    }
    getDocumentsList();
    /////////
    ///////get checklist data//////

    user = $("input[name='super_user']").val();
    property_id = $("input[name='property_id']").val();
    author_id = $("input[name='author_id_hidden']").val();

    // var user = "<?php echo Auth()->user() ?>";
    $(".btn-agent").click(function () {


        $(".agents").css('display', 'block');
        $(".documents").css('display', 'none');
        $(".btn-document").removeClass('btn-primary').addClass('btn-gray');
        $(this).removeClass('btn-gray').addClass('btn-primary');

    });
    $(".btn-document").click(function () {


        $(".agents").css('display', 'none');
        $(".documents").css('display', 'block');
        $(".btn-agent").removeClass('btn-primary').addClass('btn-gray');
        $(this).removeClass('btn-gray').addClass('btn-primary');
    });
    if (author_id == "") {
        $(".btn-agent").css('display', 'block');
        $(".agent-name").css('display', 'block');
    }
    if (user && property_id != "") {
        $.ajax({
            url: "/api/v1/get-checklist",
            type: "get",
            dataType: 'json',
            data:
            {
                property_id: $("input[name='property_id']").val() ? $("input[name='property_id']").val() : 0,
            },
            success: function (response) {
                let category = $('ul.parent-category li.label-primary').text().trim();
                let subcategory = $('ul.sub-category li.label-primary').attr('data-category_name').trim();

                if (response.status) {
                    checklist = response.data[0];

                    if (checklist.is_verify == 1 && author_id != "") {
                        $("#btn_verify").text('Verified');
                        $(".moderation_status").removeClass('d-none');
                        $('#mode-status-select').removeAttr('disabled')
                        $('#mode-status-select').css('background', '#f3a54a')
                        $('#mode-status-select').focus();
                    } else {
                        if (checklist.document_checklist != "") {
                            parsedarr = JSON.parse(checklist.document_checklist);
                            $("input[name='verify_document[]']").each(function () {
                                var val = $(this).attr('data-document-id');
                                var index = parsedarr.indexOf(val);
                                if (index > -1) {
                                    $(this).prop("checked", true);
                                }
                                else {
                                    $(this).prop("checked", false);
                                }
                            });
                        }

                        $("#myModal").modal("show");
                        $("#btn_verify").css('display', 'block');
                        $(".moderation_status").addClass('d-none');
                        $('#mode-status-select').attr('disabled', 'disabled')
                        $('#mode-status-select').css('background', 'grey')
                    }
                } else {
                    var verifyDocuments = $('input[name="verify_documents"]').val();
                    if (verifyDocuments) {
                        $("#myModal").modal("show");
                        $("#btn_verify").css('display', 'block');
                        $(".moderation_status").addClass('d-none');
                        $('#mode-status-select').attr('disabled', 'disabled')
                        $('#mode-status-select').css('background', 'grey')
                    } else {
                        $("#myModal").modal("hide");
                        $("#btn_verify").css('display', 'none');
                        $(".moderation_status").removeClass('d-none');
                        $('#mode-status-select').removeAttr('disabled')
                        $('#mode-status-select').css('background', '#f3a54a')
                        $('#mode-status-select').focus();
                    }
                }
            },
        });

        property_id = $("input[name='property_id']").val();

        $("#verify_checklist").click(function () {
            //ajax to update subscription
            var data = [];
            $('.checklist:checked').each(function () {
                data.push($(this).val());
            });

            if (data != "") {
                $("#checklist_dp").addClass('d-none');
                $.ajax({
                    url: "/api/v1/update-checklist",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        verify_document: data,
                        property_id: $("input[name='property_id']").val(),
                        category_id: $("input[name='category_id']").val()
                    },
                    success: function (response) {
                        if (response.status) {
                            if (response.approved && author_id != "") {
                                toastr.success('Document Checklist Updated successfully! You can approve the property.', 'success', { iconClass: "toast-custom", timeOut: 10000, extendedTimeOut: 5000 });
                                $(".moderation_status").removeClass('d-none');
                                $('#mode-status-select').removeAttr('disabled')
                                $('#mode-status-select').css('background', '#f3a54a')
                                $('#mode-status-select').focus();
                            } else {
                                toastr.success('Document Checklist Updated successfully! You can approve the property after verifying all the documents.', 'success', { iconClass: "toast-custom", timeOut: 10000, extendedTimeOut: 5000 });
                            }
                        }
                        $("#myModal").modal("hide");
                    },
                });
            }
            else {
                $("#checklist_dp").removeClass('d-none');
                $("#checklist_dp").text('please select checklist first');

            }

        });
        $("#assign_checklist").click(function () {

            //ajax to update subscription
            var author_idd = $("#author_id").val();
            if (author_idd == "") {
                //checklist_agent
                //alert("please Select Agent from list First");
                $("#checklist_agent").removeClass('d-none');
                $("#checklist_agent").text('please Select Agent from list First');
            }
            else {
                $("#checklist_agent").addClass('d-none');
                $.ajax({
                    url: "/api/v1/assign-checklist",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        author_id: $("#author_id").val(),
                        property_id: $("input[name='property_id']").val()
                    },
                    success: function (response) {
                        if (response.status) {
                            //alert("Agent Assigned successfully!");
                            toastr.success('Agent Assigned successfully!', 'success', { iconClass: "toast-custom" });
                            agent_id = $("#author_id").val();
                            liObj = $("#dropdown-menu-admin").find('li[data-agent-id=' + agent_id + ']');
                            var btnObj = liObj.parent().siblings('button');
                            $(btnObj).text(liObj.text());
                            if (response.approved && author_idd != "")
                                $("#btn_verify").text('Verified');
                        }
                        $("#myModal").modal("hide");
                    },
                });
            }

        });
        $('.attachment-details a').each(function () {

            var oldUrl = $(this).attr("href"); // Get current url
            $(this).attr("href", "/storage/documents/" + oldUrl); // Set herf value
        });
    }
    else {
        $("#btn_verify").css('display', 'none');
    }
});

function setTemplateVariables(ob) {

    temp_update = $("#template_desp").val();

    for (key in ob) {
        var id = ob[key];
        ber = $("#" + id).val();

        temp_update = temp_update.replaceAll(key, ber);
    }
    $("#description").val(temp_update);

}

$(document).ready(function () {

    $(this).scrollTop(0);
    $("button[type=submit]").click(function () {
        var submit = 1;
        $('input[name^="documents"]').each(function () {
            if ($(this).attr("data-required") == "required") {

                if ($(this).val() == "" && $(this).prev().attr('data-src') == "") {
                    submit = 0;
                    $(this).css('border', '1px solid red');
                    $(this).focus();
                }
                else {
                    submit = 1;
                    $(this).css('border', '');
                }
            }
        });
        if (submit) {
            return true;
        }
        else {
            return false;
        }

    });
    /* $("button[type=submit]").validate({
             rules: {
                 example_file: {
                     fileType: {
                         types: ["text", "gzip", "zip"]
                     },
                     maxFileSize: {
                         "unit": "KB",
                         "size": 100
                     },
                     minFileSize: {
                         "unit": "KB",
                         "size": "10"
                     }
                 }
             }
             });*/
    $("#admin_comment").click(function (e) {

        var comment = $("input[name='comment']").val();
        if (comment != "") {
            $("input[name='comment']").parent().next(".validation").remove();
            var form = $('#admin_form')[0];
            var formData = new FormData(form);
            $.ajax({
                type: 'POST',
                url: "/comment/admin/store",
                processData: false,
                contentType: false,
                dataType: 'json',
                data: formData,
                async: false,
                success: function (data) {
                    $("input[name='comment']").val('');
                    if ($(".d-comment").find(".display-comment").length) {

                        //   alert(data.admin_url.encoded);
                        $(".display-comment:last").append('<img src="' + data.admin_url.encoded + '" class="br-100 v-mid mr-2" style="width: 30px;"><strong>' + data.admin.first_name + '</strong><p class="comment-desp">' + data.comment + '</p>');
                    }
                    else {

                        //   alert(data.admin_url.encoded);
                        $(".comment-box").find(".d-comment").append('<div class="display-comment"><img src="' + data.admin_url.encoded + '" class="br-100 v-mid mr-2" style="width: 30px;"><strong>' + data.admin.first_name + '</strong><p class="comment-desp">' + data.comment + '</p></div>');


                    }
                },

            });
        }
        else {
            $("input[name='comment']").parent().next(".validation").remove();
            $("input[name='comment']").parent().after("<div class='validation' style='color:red;margin-bottom: 20px;'>Please enter comment</div>");


        }
    });
    $("#number_bedroom").change(function () {
        var myObject = { '$a': 'number_bedroom', '$b': 'number_bathroom', '$c': 'square', '$d': 'type', '$e': 'area_units', '$f': 'category_name', '$g': 'number_floor' };

        setTemplateVariables(myObject);
        /*var temp=$("#template_desp").val();
        temp_update = temp.replace('$a', $(this).val());
        ///type for sale/rent
        p_id=$("input[name='type']").val();
        temp_update = temp_update.replace('$d', p_id);
       // $("#description").val(temp_update);
        ///bathroom
        br=$("#number_bathroom").val();
        temp_update = temp_update.replace('$b', br);
        $("#description").val(temp_update);
        ///square
        sq=$("#square").val();
        temp_update = temp_update.replace('$c', sq);
        $("#description").val(temp_update);*/

    });
    $("#number_bedroom").change(function () {
        var myObject = { '$a': 'number_bedroom', '$b': 'number_bathroom', '$c': 'square', '$d': 'type', '$e': 'area_units', '$f': 'category_name', '$g': 'number_floor' };

        setTemplateVariables(myObject);
        /*var temp=$("#template_desp").val();
        temp_update = temp.replace('$a', $(this).val());
        ///type for sale/rent
        p_id=$("input[name='type']").val();
        temp_update = temp_update.replace('$d', p_id);
       // $("#description").val(temp_update);
        ///bathroom
        br=$("#number_bathroom").val();
        temp_update = temp_update.replace('$b', br);
        $("#description").val(temp_update);
        ///square
        sq=$("#square").val();
        temp_update = temp_update.replace('$c', sq);
        $("#description").val(temp_update);*/

    });
    $("#number_bathroom").change(function () {
        var myObject = { '$a': 'number_bedroom', '$b': 'number_bathroom', '$c': 'square', '$d': 'type', '$e': 'area_units', '$f': 'category_name', '$g': 'number_floor' };

        setTemplateVariables(myObject);
        /* var temp=$("#template_desp").val();
          temp_update = temp.replace('$b', $(this).val());
         ///type for sale/rent
         p_id=$("input[name='type']").val();
         temp_update = temp_update.replace('$d', p_id);
         // $("#description").val(temp_update);
         ///bedroom
         br=$("#number_bedroom").val();
         temp_update = temp_update.replace('$a', br);
         $("#description").val(temp_update);
         ///square
         sq=$("#square").val();
         temp_update = temp_update.replace('$c', sq);
         $("#description").val(temp_update);*/
    });
    $("#square").change(function () {
        var myObject = { '$a': 'number_bedroom', '$b': 'number_bathroom', '$c': 'square', '$d': 'type', '$e': 'area_units', '$f': 'category_name', '$g': 'number_floor' };

        setTemplateVariables(myObject);
        /*var temp=$("#template_desp").val();

        temp_update = temp.replace('$c', $(this).val());
        ///type for sale/rent
        p_id=$("input[name='type']").val();
        temp_update = temp_update.replace('$d', p_id);
        // $("#description").val(temp_update);
        ///bathroom
        br=$("#number_bathroom").val();
        temp_update = temp_update.replace('$b', br);
        $("#description").val(temp_update);
        ///bedroom
        br=$("#number_bedroom").val();
        temp_update = temp_update.replace('$a', br);
        $("#description").val(temp_update)*/;

    });
    $("#area_units").change(function () {
        var myObject = { '$a': 'number_bedroom', '$b': 'number_bathroom', '$c': 'square', '$d': 'type', '$e': 'area_units', '$f': 'category_name', '$g': 'number_floor' };

        setTemplateVariables(myObject);
        /*var temp=$("#template_desp").val();

        temp_update = temp.replace('$c', $(this).val());
        ///type for sale/rent
        p_id=$("input[name='type']").val();
        temp_update = temp_update.replace('$d', p_id);
        // $("#description").val(temp_update);
        ///bathroom
        br=$("#number_bathroom").val();
        temp_update = temp_update.replace('$b', br);
        $("#description").val(temp_update);
        ///bedroom
        br=$("#number_bedroom").val();
        temp_update = temp_update.replace('$a', br);
        $("#description").val(temp_update)*/;

    });


});
function filterFunction() {
    var input, filter, ul, li, a, i;
    input = document.getElementById("myInputadmin");
    filter = input.value.toUpperCase();
    div = document.getElementById("dropdown-menu-admin");
    a = div.getElementsByTagName("li");

    for (i = 0; i < a.length; i++) {
        txtValue = a[i].textContent || a[i].innerText;

        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            // alert("here");
            a[i].style.display = "";
        } else {

            a[i].style.display = "none";
        }
    }
}
function filterFunctionCheck() {
    var input, filter, ul, li, a, i;
    input = document.getElementById("myInputchecklist");
    filter = input.value.toUpperCase();
    div = document.getElementById("dropdown-menu-checklist");
    a = div.getElementsByTagName("li");

    for (i = 0; i < a.length; i++) {
        txtValue = a[i].textContent || a[i].innerText;

        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            // alert("here");
            a[i].style.display = "";
        } else {

            a[i].style.display = "none";
        }
    }
}

$(document).ready(function () {
    var selectedMod = $('input[name="moderation_status_hidden"]').val();
    $('input[name="moderation_status"]').val(selectedMod);
    $('#mode-status-select').on('change', function () {
        var newVal = $('#mode-status-select').val()
        console.log(newVal);
        $('input[name="moderation_status"]').val(newVal);
    });
});


