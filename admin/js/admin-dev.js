jQuery(document).ready(function($) {

    jQuery("#ckbCheckAll").click(function () {
        jQuery(".qbe_group.be_group").prop('checked', $(this).prop('checked'));
        jQuery(".customCheckAll").prop('checked', $(this).prop('checked'));
    });

    jQuery("#ckbulkCheckAll").click(function () {
        jQuery(".customCheckAll").prop('checked', $(this).prop('checked'));
        console.log('Admin Dev');
    });
});