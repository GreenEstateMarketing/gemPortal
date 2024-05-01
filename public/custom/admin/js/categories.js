 $(document).ready(function()
    {
    function setParent($default)
    {
        p_id= $("#parent_id").val();
        if(p_id=="")
        {
            $("#parent_id_check").prop('checked',true);
            $("#parent_id_check").trigger("change");
        }
        else
        {
            $("#parent_id_check").prop('checked',false);
            $("#parent_id_check").trigger("change");
        }
    }
    $("#parent_id_check").change(function () {
    if ($(this).is(':checked')) {
    switchStatus = $(this).is(':checked');
    $("#parent_id").parent().hide();
    $("#parent_id").attr('disabled',true);
    $(".parentCategory").hide();
}
    else{
    switchStatus = $(this).is(':checked');

    $("#parent_id").parent().show();
    $("#parent_id").attr('disabled',false);
    $(".parentCategory").show();


}
});
    setParent($default=true);
});

