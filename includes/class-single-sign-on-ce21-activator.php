<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.ce21.com
 * @since      1.0.0
 *
 * @package    Single_Sign_On_Ce21
 * @subpackage Single_Sign_On_Ce21/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Single_Sign_On_Ce21
 * @subpackage Single_Sign_On_Ce21/includes
 * @author     CE21 <support@ce21.com>
 */
class Single_Sign_On_Ce21_Activator {


	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 * Add customer role
	 */
	public static function activate($network_wide) {

        /**
         * Create a table on activation code
         * check if multi-site or single site
         */
        if ( is_multisite() && $network_wide ) {
            global $wpdb;
            // Get all blogs in the network and activate plugin on each one
            $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );
                ce21_create_table();
                ce21_create_api_settings_table();
                ce21_create_calendar_events_table();
                restore_current_blog();
            }
        } else {
            ce21_create_table();
            ce21_create_api_settings_table();
            ce21_create_calendar_events_table();
        }


        /**
         * Create a page on activation code
         * check if multi-site or single site
         */
        if ( is_multisite() && $network_wide ) {
            global $wpdb;
            // Get all blogs in the network and activate plugin on each one
            $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );
                ce21_create_authentication_page();
                ce21_sign_in_form_page();
                ce21_add_customer_role();
                restore_current_blog();
            }

        } else {
            ce21_create_authentication_page();
            ce21_sign_in_form_page();
            ce21_add_customer_role();
        }
	}
}
