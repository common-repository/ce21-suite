<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.ce21.com
 * @since      1.0.0
 *
 * @package    Single_Sign_On_Ce21
 * @subpackage Single_Sign_On_Ce21/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Single_Sign_On_Ce21
 * @subpackage Single_Sign_On_Ce21/includes
 * @author     CE21 <support@ce21.com>
 */
class Single_Sign_On_Ce21_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		remove_role( 'CE21_Customer' );
	}

}
