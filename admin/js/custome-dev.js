(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );

/*
* Catalog form validation
*/
function catalog_form_validate_ce21(form) {
	if( jQuery('#catalog_old_url_cf21').val().length != 0 ) {
		var catalog_url_cf21 = jQuery('#catalog_url_cf21').val();
		var catalog_old_url_cf21 = jQuery('#catalog_old_url_cf21').val();
		
		var retVal = confirm("Are you sure you want to change " + catalog_old_url_cf21 + " to " + catalog_url_cf21 + "?");
		if (retVal == true) {
		  	return true;
		} else {
		  	return false;
		}
	}
}

jQuery(document).ready(function(){
	// Add minus icon for collapse element which is open by default
	jQuery('.collapse').on('shown.bs.collapse', function(){
	jQuery(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
	}).on('hidden.bs.collapse', function(){
	jQuery(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
	});
});



/*data tavel listing*/
jQuery(document).ready(function() {
    jQuery('#member_plans_listing_ce21').DataTable();

} );