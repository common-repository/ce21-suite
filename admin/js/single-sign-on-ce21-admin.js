(function( $ ) {
	'use strict';


	$(document).on('click','#bulk_edit', function() {


		// define the bulk edit row
		var $bulk_row = $( '#bulk-edit' );

		// get the selected post ids that are being edited
		var $post_ids =[];
		var fields =[];

		$bulk_row.find( '#bulk-titles' ).children().each( function() {
			$post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
		});



		fields = $bulk_row.find(".be_group:checkbox:checked").map(function(){
			return $(this).val();
		}).get();

		// save the data
		$.ajax({
			url: ajaxurl, // this is a variable that WordPress has already defined for us
			type: 'POST',
			async: false,
			cache: false,
			data: {
				action: 'manage_wp_posts_using_bulk_quick_save_bulk_edit', // this is the name of our WP AJAX function that we'll set up next
				post_ids: $post_ids, // and these are the 2 parameters we're passing to our function
				fields: fields,
			},success: function(data){
				jQuery(data).find('#message').html(data);
				console.log('s-',data)
				return false;
			},error: function(data){
				jQuery(data).find('#message').html(data);
				console.log('e-',data)
				return false;
			}
		});

	});


	/*
	* Function used to save api settings
	* */
	$(document).on('click', '#btn_save_ce21_ss_api_settings',function() {
		$('#ce21_ss_admin_loader_div').show();
		var api_settings_id = $('#api_settings_id').val();
		var base_url = $('#txt_ce21_ss_base_url').val();
		var client_id = $('#txt_ce21_ss_client_id').val();
		var secret_key = $('#txt_ce21_ss_secret_key').val();
		var catalog_url = $('#txt_ce21_ss_catalog_url').val();

		if (base_url == '')
		{
			$('#ce21_ss_api_settings_notification_div').text("Please enter Base URL!");
			$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-danger");
			$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-success");
			$('#ce21_ss_api_settings_notification_div').addClass("ce21-ss-alert-danger");
			$('#ce21_ss_api_settings_notification_div').show();
			$('#ce21_ss_admin_loader_div').hide();
			return false;
		}
		if (client_id == '')
		{
			$('#ce21_ss_api_settings_notification_div').text("Please enter Client Id!");
			$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-danger");
			$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-success");
			$('#ce21_ss_api_settings_notification_div').addClass("ce21-ss-alert-danger");
			$('#ce21_ss_api_settings_notification_div').show();
			$('#ce21_ss_admin_loader_div').hide();
			return false;
		}
		if (secret_key == '')
		{
			$('#ce21_ss_api_settings_notification_div').text("Please enter Secret Key!");
			$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-danger");
			$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-success");
			$('#ce21_ss_api_settings_notification_div').addClass("ce21-ss-alert-danger");
			$('#ce21_ss_api_settings_notification_div').show();
			$('#ce21_ss_admin_loader_div').hide();
			return false;
		}
		if (catalog_url == '')
		{
			$('#ce21_ss_api_settings_notification_div').text("Please enter Catalog URL!");
			$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-danger");
			$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-success");
			$('#ce21_ss_api_settings_notification_div').addClass("ce21-ss-alert-danger");
			$('#ce21_ss_api_settings_notification_div').show();
			$('#ce21_ss_admin_loader_div').hide();
			return false;
		}

		var $params = {action: 'ce21_single_sign_on_save_api_settings', api_settings_id: api_settings_id, base_url: base_url, client_id: client_id, secret_key: secret_key, catalog_url: catalog_url};
		$.ajax({
			type: "POST",
			url: ajax_login_object.ajaxurl,
			dataType: 'json',
			data: $params,
			success: function (response) {
				if (response)
				{
					if (response.success == true)
					{
						$('#ce21_ss_api_settings_notification_div').text(response.message);
						$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-danger");
						$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-success");
						$('#ce21_ss_api_settings_notification_div').addClass("ce21-ss-alert-success");
						$('#ce21_ss_api_settings_notification_div').show();
						$('#ce21_ss_admin_loader_div').hide();
					}
					else
					{
						$('#ce21_ss_api_settings_notification_div').text(response.message);
						$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-danger");
						$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-success");
						$('#ce21_ss_api_settings_notification_div').addClass("ce21-ss-alert-danger");
						$('#ce21_ss_api_settings_notification_div').show();
						$('#ce21_ss_admin_loader_div').hide();
					}
				}
			},
			error: function (response) {
				$('#ce21_ss_api_settings_notification_div').text("Something went wrong!");
				$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-danger");
				$('#ce21_ss_api_settings_notification_div').removeClass("ce21-ss-alert-success");
				$('#ce21_ss_api_settings_notification_div').addClass("ce21-ss-alert-danger");
				$('#ce21_ss_api_settings_notification_div').show();
				$('#ce21_ss_admin_loader_div').hide();
			}
		});

	});

	/*
	* Function program list generate shortcode
	*
	*/
	$(document).on('click','#btn_save_ce21_program_settings' ,function() {
		$('#ce21_ss_admin_loader_div').show();
		var topicArea = $('#topicArea').val();
		var tags = $('#tags').val();
		var productTypes = $('#productTypes').val();
		var categories = $('#categories').val();
		var page_size = $('#page_size').val();
		var sort_order = $('#sort_order').val();

		var $params = {
			action: 'ce21_save_programs_list_settings',
			topicArea : topicArea,
			tags : tags,
			productTypes : productTypes,
			categories : categories,
			page_size : page_size,
			sort_order : sort_order
		};
		$.ajax({
			type: "POST",
			url: ajax_login_object.ajaxurl,
			dataType: 'json',
			data: $params,
			success: function (response) {
				if (response)
				{
					if (response.success == true)
					{
						$('#ce21_ss_api_settings_notification_div').text(response.message);
						$('#ce21_generated_shortcode').val(response.shortcode);
						$('#ce21_ss_admin_loader_div').hide();
						$('#ce21_div_generated_shortcode').show();
						$('#ce21_ss_api_settings_notification_div').show();
					}
					else
					{
						$('#ce21_ss_api_settings_notification_div').text("Something went wrong!");
						$('#ce21_ss_admin_loader_div').hide();
					}
				}
			},
			error: function (response) {
				$('#ce21_ss_api_settings_notification_div').text("Something went wrong!");
				$('#ce21_ss_admin_loader_div').hide();
			}
		});
	});

})( jQuery );


/*
* inlineEditPost.edit = function( id ) {
	// and then we overwrite the function with our own code
		// we create a copy of the WP inline edit post function
		var $wp_inline_edit = inlineEditPost.edit;
		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		$wp_inline_edit.apply( this, arguments );
		// now we take care of our business
		// get the post ID
		var $post_id = 0;
		if ( typeof( id ) == 'object' )
			$post_id = parseInt( this.getId( id ) );
		if ( $post_id > 0 ) {
			// define the edit row
			var $edit_row = $( '#edit-' + $post_id );
			// get the release date
			var $release_date = $( '#release_date-' + $post_id ).text();
			// populate the release date
			$edit_row.find(".be_group:checkbox:checked").map(function(){
				return $(this).val();
			}).get();
		}
	};
* */