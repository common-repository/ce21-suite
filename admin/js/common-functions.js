/*
* Copy shortcode function
*/
function copyShortcode() {
    var copyText = document.getElementById("ce21_generated_shortcode");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");

    var copy_short_code = document.getElementById("copy_short_code");
    copy_short_code.innerHTML = "Shortcode Copied";
}

jQuery(document).ready(function() {
    /*
    * On click Generate membership shortcode function
    */
    jQuery("#btn_save_ce21_membership_settings").click(function() {
        var ce21_membership = jQuery('#ce21_membership').find(":selected").val();
        jQuery('#ce21_generated_shortcode').val('[ce21_directory group="'+ce21_membership+'"]');
        jQuery('#ce21_div_generated_shortcode').show();
    });
});