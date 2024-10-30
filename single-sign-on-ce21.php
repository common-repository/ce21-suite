<?php

/**
 * The plugin CE21 Suite
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.ce21.com
 * @package           Single_Sign_On_Ce21
 *
 * @wordpress-plugin
 * Plugin Name:       CE21 Suite
 * Plugin URI:        https://www.ce21.com
 * Description:       CE21 Suite.
 * Version:           2.2.0
 * Author:            CE21
 * Author URI:        https://www.ce21.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       single-sign-on-ce21
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
			
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SINGLE_SIGN_ON_CE21_VERSION', '2.1.1' );

define( 'SINGLE_SIGN_ON_CE21__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SINGLE_SIGN_ON_CE21__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs before plugin activation.
 * This function will check weather curl is enable or not 
 */
if ( ! function_exists( 'curl_version' ) ) {
    try {
        $curlError = 'no PHP cURL library activated';
        throw new Exception($curlError);
    } catch (Exception $e) {
        $errorMsg = $e->getMessage();
        ce21_error_log_api($errorMsg);
    }

}
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/ce21-functions.php';
require plugin_dir_path( __FILE__ ) . 'includes/single-sign-on-ce21-api-helper.php';
include(plugin_dir_path( __FILE__ ) . 'programs/ce21-programs-functions.php');
include(plugin_dir_path( __FILE__ ) . 'membership/ce21-membership-functions.php');
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-single-sign-on-ce21-activator.php
 */
function ce21_activate_single_sign_on($network_wide) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-single-sign-on-ce21-activator.php';
	Single_Sign_On_Ce21_Activator::activate($network_wide);
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-single-sign-on-ce21-deactivator.php
 */
function ce21_deactivate_single_sign_on() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-single-sign-on-ce21-deactivator.php';
	Single_Sign_On_Ce21_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'ce21_activate_single_sign_on' );
register_deactivation_hook( __FILE__, 'ce21_deactivate_single_sign_on' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-single-sign-on-ce21.php';
require plugin_dir_path( __FILE__ ) . 'includes/session-helper.php';

/**
 * JWT library included
 */
require_once('vendor/autoload.php');
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
session_start();
global $sesionHelper;
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_single_sign_on_ce21() {
    $plugin = new Single_Sign_On_Ce21();
    $plugin->run();
}
run_single_sign_on_ce21();

/**
* WP Error Handling function
*/
function ce21_wp_error_handle(){
    global $wpdb;
    if($wpdb->last_error){
        throw new Exception($wpdb->last_error);
    }
}

/*
 * General Function to use for Get API
 */
function getApiCall($baseUrl, $dataArray, $api_Access_token){
    try{
        if(!empty($dataArray)) {
            $data = http_build_query($dataArray);
            $baseUrl = $baseUrl."?".$data;
        }

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_Access_token
            )
        );
        $response = wp_remote_get($baseUrl, $args);
        $err = is_wp_error($response);
        $httpcode = wp_remote_retrieve_response_code($response);

        if ( $httpcode != 200 ){
            $response_message = array(
                'success' => false,
                'access_token' => '',
                'message' => 'Something went wrong! Please check API settings'
            );
            return $response_message;
        }
        if ($err)
        {
            $response_message = array(
                'success' => false,
                'access_token' => '',
                'message' => 'Something went wrong while generating access token!'
            );
            return $response_message;
        }
        else
        {
            return $response_obj = json_decode($response['body']);
        }

    }
    catch (Exception $e)
    {
        $errorMsg = ' Error on line '.$e->getLine().' in '.$e->getFile() . ' : <b>'.$e->getMessage().'</b>';
        ce21_error_log_api($errorMsg);
        $response_message = array(
            'success' => false,
            'message' => $errorMsg
        );
        return $response_message;
    }
}

/**
* Create End Point for the login api and membership group update api
* The code that handle the user authentication from CE21.
* Create a user if does not exist and login.
*/

add_action( 'rest_api_init', 'ce21_my_authentication_route' );

function ce21_my_authentication_route() {
    register_rest_route(
    	'ce21', 'authentication', 
    		array(
                'methods' => 'GET',
                'callback' => 'ce21_authentication_phrase',
                'permission_callback' => '__return_true',
            )
    );
    /* Update and delete CE21 members plans data */
    register_rest_route( 
    	'ce21', 'membership/update', 
    		array(
                'methods' => 'POST',
                'callback' => 'ce21_membership_update',
                'permission_callback' => '__return_true',
            )
    );
    /* LogOff CE21 member */
    register_rest_route(
        'ce21', 'logoff',
        array(
            'methods' => 'GET',
            'callback' => 'ce21_log_off',
            'permission_callback' => '__return_true',
        )
    );
}
/* LogOff CE21 member */
function ce21_log_off($data){
    $tid = get_option('tenantId_ce21');
    $returnUrl  = $data->get_param('returnUrl');
    $sesionHelper = new session_helper_ce21($tid);
    $sesionHelper->unset_session();

    $url_ce21 = filter_var($returnUrl, FILTER_SANITIZE_URL);

    if ( !empty($returnUrl) && ($returnUrl != NULL) && ($returnUrl != null) && filter_var($url_ce21, FILTER_VALIDATE_URL) !== false) {
        header('Location: '.$returnUrl);
    } else {
        $authorizeURI_ce21 = get_option('authorizeURI_ce21');
        header('Location: '.$authorizeURI_ce21);
    }
    exit;
}

function ce21_authentication_phrase($data) {
	$token_ce21 = $data->get_param('x');

	$log = 'Updated on ' . date("F j, Y, h:i:s a").PHP_EOL;
	$log .= ' TOKEN: '. $token_ce21 .PHP_EOL;

	try {
		$log .= ' try'.PHP_EOL;
		
	    $key_ce21 	= "ixqv4z0ZOY0bmNCjBK7v3wgijyAv0D3jvyt6bk3lpEDUtVxdR72ZjuGW1hcR6TP";
	    $user_data = JWT::decode( $token_ce21, new Key( $key_ce21 , 'HS256'));
	    $tid 		= $user_data->tenantId;
	    $customerId	= $user_data->customerId;
	    $emailId    = $user_data->email;
        $firstName  = $user_data->firstName;
        $lastName   = $user_data->lastName;
	    $type 		= $user_data->type;
	    $returnUrl  = $data->get_param('returnUrl');

		$user_id 	= email_exists($emailId);
		$tenantId_ce21 = get_option('tenantId_ce21');

		$log .= ' tid: '. $tid .PHP_EOL;
			$log .= ' customerId: '. $customerId . PHP_EOL;
			$log .= ' emailId: '. $emailId .PHP_EOL;
			$log .= ' tenantId_ce21: '. $tenantId_ce21 .PHP_EOL;
			$log .= ' email_exists (wp-user-id): '. $user_id .PHP_EOL;
			$log .= ' type: '. $type .PHP_EOL;
			$log .= ' returnUrl: '. $returnUrl .PHP_EOL;
			$log .= '--------------------------' .PHP_EOL;

		if($tenantId_ce21 != $tid){
            $url_ce21 = filter_var($returnUrl, FILTER_SANITIZE_URL);

            if (filter_var($url_ce21, FILTER_VALIDATE_URL) !== false) {
                $returnUrlQuery = parse_url($returnUrl, PHP_URL_QUERY);

                if ($returnUrlQuery) {
                    $returnUrl .= '&msg=You are not authorized.';
                } else {
                    $returnUrl .= '?msg=You are not authorized.';
                }
                header('Location: '.$returnUrl);

            } else {
                wp_redirect(get_option('siteurl'));
            }
			$log .= ' ======================' .PHP_EOL;
			file_put_contents('plugin-log.txt', $log, FILE_APPEND);
            exit;
        }
        /* Check Admin or not  */
        if (!empty($type) && $type == 'CE21_Staff'){

            $user_id  =  email_exists($emailId);
            if( !empty($user_id) && $user_id != null && $user_id != NULL ){
				
				$log .= 'USER EXISTS ' .PHP_EOL;
				
                /* Already exiting users  */
                wp_set_current_user( $user_id, $emailId );
                wp_set_auth_cookie( $user_id );
                $user_id_role = new WP_User($user_id);
                $user_id_role->set_role('administrator');
                wp_update_user([
                    'ID' => $user_id, // this is the ID of the user you want to update.
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                ]);
            } else {
				$log .= 'NEW USER ' .PHP_EOL;
				
                /* Create a new user*/
                $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
                $new_user_id = wp_create_user( $emailId, $random_password, $emailId );
                add_user_meta( $new_user_id, '_CE21_CustomerId', $customerId);
                wp_update_user([
                    'ID' => $new_user_id, // this is the ID of the user you want to update.
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                ]);

                $user_id_role = new WP_User($new_user_id);
                $user_id_role->set_role('administrator');

                wp_set_current_user( $new_user_id, $emailId );
                wp_set_auth_cookie( $new_user_id );
            }

            $url_ce21 = filter_var($returnUrl, FILTER_SANITIZE_URL);
			
			$log .= 'url_ce21: '.$url_ce21 .PHP_EOL;
			$log .= 'siteurl: '.get_option('siteurl') .PHP_EOL;
            
			if (filter_var($url_ce21, FILTER_VALIDATE_URL) !== false) {
				$log .= 'HEADER LOCATION' .PHP_EOL;
                header('Location: '.$returnUrl);
            } else {
				$log .= 'WP REDIRECTION' .PHP_EOL;
                wp_redirect(get_option('siteurl'));
            }

        }

        $sesionHelper = new session_helper_ce21($tid);
        $sesionHelper->create_session( $firstName, $lastName, $emailId, $tid, time() );

        $url_ce21 = filter_var($returnUrl, FILTER_SANITIZE_URL);
        if (filter_var($url_ce21, FILTER_VALIDATE_URL) !== false) {
			$log .= 'HEADER LOCATION (not staff)' .PHP_EOL;
            header('Location: '.$returnUrl);
        } else {
			$log .= 'WP REDIRECTION (not staff)' .PHP_EOL;
            wp_redirect(get_option('siteurl'));
        }
		$log .= ' ======================' .PHP_EOL;
		file_put_contents('plugin-log.txt', $log, FILE_APPEND);
        exit;

	} catch (\Exception $e) {
		
		$log .= 'catch: '.PHP_EOL;
		$log .= 'Exception: '. $e .PHP_EOL;
		
		$returnUrl  = $data->get_param('returnUrl');
		$url_ce21 = filter_var($returnUrl, FILTER_SANITIZE_URL);

		if (filter_var($url_ce21, FILTER_VALIDATE_URL) !== false) {
			$returnUrlQuery = parse_url($returnUrl, PHP_URL_QUERY);
			if ($returnUrlQuery) {
			    $returnUrl .= '&msg=Sorry,Something went wrong, Please try again.';
			} else {
			    $returnUrl .= '?msg=Sorry,Something went wrong, Please try again.';
			}	
			$log .= 'HEADER LOCATION' .PHP_EOL;
			
			header('Location: '.$returnUrl);
		} else {
			$log .= 'WP REDIRECTION' .PHP_EOL;			
			wp_redirect(get_option('siteurl'));
		}
		$log .= ' ======================' .PHP_EOL;
		file_put_contents('plugin-log.txt', $log, FILE_APPEND);
		exit;
	}
	$log .= ' ======================' .PHP_EOL;
	file_put_contents('plugin-log.txt', $log, FILE_APPEND);
	
	exit;
}

/**
* Update and delete CE21 member plans
*/

function ce21_membership_update($data){

	$parameters_ce21 = $data->get_params();

	if (!empty($parameters_ce21) ) {

		global $wpdb;
		$membershipId	 = $parameters_ce21['membershipTypeId'];
		$membershipName  = $parameters_ce21['name'];
		$tenantId 		 = $parameters_ce21['tenantId'];
		$isDelete 		 = $parameters_ce21['isDelete'];
		$table_name_ce21 = $wpdb->prefix . 'membership_types_ce21';
		$response_data   = [];

        $get_data_ce21   = $wpdb->get_results($wpdb->prepare("SELECT id,TenantId,membershipTypeId,membershipName FROM ".$table_name_ce21." WHERE TenantId = %d AND membershipTypeId = %d", $tenantId, $membershipId));

		/*If plan already exists */
		if(! empty($get_data_ce21)) {

			if($isDelete == true) {
				/* If isDelete is equal to true then detele single record */
                $query_delete = $wpdb->query($wpdb->prepare("DELETE FROM $table_name_ce21 WHERE membershipTypeId = %d AND TenantId = %d", $membershipId, $tenantId));

				if($query_delete) {
					$response_data = [ 'success' => true, 'message' => 'Group has been successfully deleted.' ];
				}else{
					$response_data = [ 'success' => false, 'message' => 'Something went wrong.' ];
				}
			}else {
				/*if status isDelete is equal to false then update record */
                $query_update = $wpdb->update(
                    $table_name_ce21,
                    array('TenantId' => $tenantId, 'membershipTypeId' => $membershipId, 'membershipName' => $membershipName),
                    array('membershipTypeId' => $membershipId, 'TenantId' => $tenantId),
                    array('%d','%d','%s'),
                    array('%d', '%d')
                );

				if($query_update){
					$response_data = [ 'success' => true, 'message' => 'Group has been successfully updated.' ];
				}else{
					$response_data = [ 'success' => false, 'message' => 'Something went wrong.' ];
				}
			}
		} else {
			/* If isDelete is equal to false and not found any exting data then insert record*/
			if($isDelete == false) {

                $insertdata = $wpdb->insert($table_name_ce21, array(
                    'TenantId' => $tenantId,
                    'membershipTypeId' => $membershipId,
                    'membershipName' => $membershipName
                ));

				if ($insertdata) {
					$response_data = [ 'success' => true, 'message' => 'Group has been successfully added.' ];
				}else{
					$response_data = [ 'success' => false, 'message' => 'Something went wrong.' ];
				}
			}
		}
		echo json_encode( $response_data );
		exit;
	}
}

/**
* Added Custom meta box for the default post type
* Custom meta box name is Post Membership Plans
* List of CE21 Members plans list under add post and edit post
*/
add_action('add_meta_boxes', 'ce21_post_groups_add_custom_metabox',2);

function ce21_post_groups_add_custom_metabox()
{
    $screens = ['post','page'];
    foreach ($screens as $screen) {
        add_meta_box(
            'post_authentication_option_id_ce21',   // Unique ID
            'Groups',  // Box title
            'ce21_post_groups_metabox_listing',  // Content callback, must be of type callable
            $screen,                  // Post type
            'side',
            'high',
            null
        );
    }
}

function ce21_post_groups_metabox_listing($post)
{
    try {
        global $wpdb;
        $table_name_ce21 		= $wpdb->prefix . 'membership_types_ce21';
        $Memberships_Data_ce21 	= $wpdb->get_results("SELECT * FROM ".$table_name_ce21." ORDER BY id ASC");
        $details 				= get_post_meta( $post->ID, '_post_authentication_custom_metabox_ce21', true );

        if(! empty($Memberships_Data_ce21)) {
            ?>
            <label><input type="checkbox" id="ckbCheckAll" /> Select All </label><br>
            <?php
            foreach ($Memberships_Data_ce21 as $key => $value) {
                ?>
                <label> <input type="checkbox" class="radio customBulkCheckAll" value="<?php echo esc_attr($value->membershipTypeId); ?>" name="_post_authentication_custom_metabox_ce21[]"  <?php if ( ! empty($details) && in_array($value->membershipTypeId, $details)){ echo "checked"; }  ?> /> <?php echo esc_html($value->membershipName); ?></label> </br>
                <?php
            }
        } else {
            echo "Groups are not available.";
        }
        ce21_wp_error_handle();
    } catch (Exception $e) {
        $errorMsg = ' Error on line '.$e->getLine().' in '.$e->getFile() . ' : <b>'.$e->getMessage().'</b>';
        ce21_error_log_api($errorMsg);
    }

}

/**
* Save article's membership plan, Post Detail page save groups
*/
add_action('save_post', 'ce21_post_authentication_save_post_data');

function ce21_post_authentication_save_post_data($post_id)
{
    try {
        //&& $_POST['post_type'] == "post"
        if (isset( $_POST['post_type']) ) {
            $values_to_save = array();
            if(isset($_POST["_post_authentication_custom_metabox_ce21"]) && !empty($_POST["_post_authentication_custom_metabox_ce21"] )){
                $new_values 	= $_POST["_post_authentication_custom_metabox_ce21"];

                if (!empty($new_values)) {
                foreach($new_values as $new_value ) {
                    $values_to_save[] = $new_value ;
                }
                }
                update_post_meta( $post_id, '_post_authentication_custom_metabox_ce21',$values_to_save );
            }
        }
    } catch (Exception $e){
        $errorMsg = ' Error on line '.$e->getLine().' in '.$e->getFile() . ' : <b>'.$e->getMessage().'</b>';
        ce21_error_log_api($errorMsg);
    }
}

/**
* Post list view page display Membership columns
*/
add_filter( 'manage_posts_columns', 'ce21_set_custom_membership_columns' );
add_filter( 'manage_pages_columns', 'ce21_set_custom_membership_columns' );
function ce21_set_custom_membership_columns($columns) {

    $new_columns = array();

    foreach($columns as $key=>$value) {

        if($key=='date') {
           $new_columns['membership_plan_ce21'] = __( 'Groups', 'ce21' );
        }    
        $new_columns[$key]=$value;
    }
    return $new_columns;
}

/**
* Admin post list view page display selected member plans data
* Default post listing page
*
*/
add_action( 'manage_posts_custom_column' , 'ce21_custom_membership_column', 10, 2 );
add_action( 'manage_pages_custom_column' , 'ce21_custom_membership_column', 10, 2 );

function ce21_custom_membership_column( $column, $post_id ) {

    global $post;
    global $wpdb;
    try {
        switch ( $column ) {
            case 'membership_plan_ce21':

                $details_ce21 = get_post_meta( $post->ID, '_post_authentication_custom_metabox_ce21', true );

                if (!empty($details_ce21)) {
                    $table_name_ce21 		 = $wpdb->prefix . 'membership_types_ce21';
                    $membership_type_id_ce21 = implode(',', $details_ce21);
                    $serchdata_ce21   = $wpdb->get_results("SELECT * FROM $table_name_ce21 WHERE membershipTypeId IN ($membership_type_id_ce21)");
                    ce21_wp_error_handle();
                    if ( $serchdata_ce21 ) {
                        $membership_name_ce21 = array();

                        foreach ($serchdata_ce21 as $key => $value) {
                            $membership_name_ce21[] = $value->membershipName;
                        }
                        echo esc_html(implode(', ', $membership_name_ce21));

                    } else {
                        echo '—';
                    }
                } else {
                    echo '—';
                }
            break;
        }
    } catch (Exception $e) {
        $errorMsg = ' Error on line '.$e->getLine().' in '.$e->getFile() . ' : <b>'.$e->getMessage().'</b>';
        ce21_error_log_api($errorMsg);
    }
}

/**
* Restricted the article details page
* This code for single post view page display only staff(Administrator) user only
* If User not login then redirect to Authorize URI page
* If user login type staff(Administrator) then display post content
* If user type CE21_Customer then check custom are Allowed or not allowed post display
*
*/
function sso_ce21_is_blog () {
    return ( is_home() || is_archive() || is_author() || is_category() || is_tag()) && 'post' == get_post_type();
}
add_action( 'wp', 'ce21_single_post_view_function' );
// add_filter( 'body_class', 'custom_class' );
// function custom_class( $classes ) 
	// global $wp_query;
    // $pageid = $wp_query->post->ID;
	// //if ( is_page_template( 'page-example.php' ) ) {
		// $classes[] = $pageid;
	// //}
	// return $classes;
// }
/*
 *New Post view function session wise
 */
function ce21_single_post_view_function(){
	if(!is_search()) {
    try{
        global $wp, $wpdb;
        $post_type_ce21 = get_post_type();
		$post_page_id = get_the_ID();
        
		
        if(!empty($post_page_id) && !sso_ce21_is_blog()) {
			//echo '<script>console.log('.$post_page_id.')</script>';
			$tenantId_ce21 = get_option('tenantId_ce21');
			$sesionHelper = new session_helper_ce21($tenantId_ce21);
			/*Header Menu Bar Starts*/
				$authorizeURI_ce21 = get_option( 'authorizeURI_ce21' );
				$profile_url = $authorizeURI_ce21.'/Account/MyAccount';
				$name = 'Howdy, '.$sesionHelper->first_name.' '.$sesionHelper->last_name;
				if ( !is_user_logged_in() && isset($sesionHelper->email) ) {
                    ob_start();
                        ?>
                        <style>
                            #ce21_header_menu_bar {
                                direction: ltr;
                                color: #ccc;
                                font-size: 13px;
                                font-weight: 400;
                                font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
                                line-height: 32px;
                                height: 32px;
                                position: fixed;
                                top: 0;
                                left: 0;
                                width: 100%;
                                z-index: 999999;
                                background: #23282d;
                                /* margin-top: 32px !important; */
                            }
                            #ce21_header_menu_bar ul.right-list {
                                text-align: left;
                                float: right;
                                margin: 0;
                                padding: 0;
                                line-height: 32px;
                            }
                            #ce21_header_menu_bar ul.right-list li {
                                background: 0 0;
                                clear: none;
                                list-style: none;
                                margin: 0;
                                padding: 0;
                                position: relative;
                                text-indent: 0;
                                z-index: 99999;
                            }
                            #ce21_header_menu_bar ul.right-list li a {
                                padding: 0 15px 0 7px;
                                color: #eee;
                                text-decoration: none;
                            }
                        </style>
                    <?php
                    echo '<div id="ce21_header_menu_bar"><ul class="right-list"><li><a href="' . esc_url( $profile_url ). '" target="_blank">' . $name . '</a></li></ul></div>';
					//echo esc_html('<style>html{margin-top:32px !important;}</style>');
				}
			/*Header Menu Bar End*/
			//if ($post_type_ce21 == 'post' && is_singular('post')) {

				$authentication_plan_value = get_post_meta(get_the_ID(), '_post_authentication_custom_metabox_ce21', true);
				$table_name_ce21 = $wpdb->prefix . 'membership_types_ce21';
				$membership_plan_ce21 = 0;

				if (!empty($authentication_plan_value)) {
					$membership_plan_ce21 = implode(',', $authentication_plan_value);
				}

                $result_ce21 = $wpdb->get_results("SELECT membershipTypeId FROM $table_name_ce21 WHERE membershipTypeId IN ($membership_plan_ce21)");

				ce21_wp_error_handle();

				if (!empty($authentication_plan_value) && !empty($result_ce21)) {

					if (!is_user_logged_in()) {
						if( isset( $sesionHelper->email ) ){
							$authorizeURI_ce21 = get_option('authorizeURI_ce21');
							$current_user_email_ce21 = $sesionHelper->email; //$user_meta->user_email;
							$membership_TypeId_ce21 = implode(",", $authentication_plan_value);
							$get_Memberships_APi_URL_ce21 = $authorizeURI_ce21 . '/WPAuthorize/GroupAccess?email=' . urlencode($current_user_email_ce21) . '&membershipTypeId=' . $membership_TypeId_ce21;

							$result = wp_remote_get($get_Memberships_APi_URL_ce21);
							$err = is_wp_error($result);
							if ($err) {
								throw new Exception($result['body'], 1);
							}

							$Memberships_access_ce21 = json_decode($result['body'], true);

							// empty is equal to false then redirect to custome error page
							if (empty($Memberships_access_ce21['success']) || empty($Memberships_access_ce21['data']['groupAccessible'])) {
								$postId_ce21 = get_the_ID();
                                $url = home_url("article-authentication-ce21") . '/?postid=' . $postId_ce21 . '&_=' . round(microtime(true) * 1000);
								wp_redirect($url, 301);
                                ?>
							        <script type="text/javascript">location.href = "<?php echo esc_url($url); ?>";</script><?php
								exit;
							}
						} else {
                            //$current_url_ce21 = home_url(add_query_arg(array(), $wp->request));
                            //$return_url_ce21 = get_option('authorizeURI_ce21') . "/wpauthorize/login?returnURL=" . $current_url_ce21;
                            
                            $postId_ce21 = get_the_ID();
							$current_url_ce21 = home_url("article-authentication-ce21") . '/?postid=' . $postId_ce21 . '&_=' . round(microtime(true) * 1000);
                            $return_url_ce21 = get_option('authorizeURI_ce21') . "/WPAuthorize/LoginNew?returnURL=" . $current_url_ce21;
                            
							?>
							<script type="text/javascript">location.href = "<?php echo esc_url($return_url_ce21); ?>";</script><?php
							exit;
						}
					}
				}

			//}

			/*
			* Plugin activation time create article authentication ce21 page
			* if page is article-authentication-ce21 and this page check url param post id
			*/
			if(! empty( $_GET['postid']) && is_page( 'article-authentication-ce21' )) {

				$postId_ce21 = sanitize_text_field($_GET['postid']);
				$authentication_plan_value = get_post_meta( $postId_ce21, '_post_authentication_custom_metabox_ce21', true );
				if( isset($sesionHelper->email ) ) {
					if (!empty($authentication_plan_value)) {

						//TODO check condition
						if (session_id()) {
							$authorizeURI_ce21 = get_option('authorizeURI_ce21');
							$current_user_email_ce21 = $sesionHelper->email;
							$membership_TypeId_ce21 = implode(",", $authentication_plan_value);
							$get_Memberships_APi_URL_ce21 = $authorizeURI_ce21 . '/WPAuthorize/GroupAccess?email=' . urlencode($current_user_email_ce21) . '&membershipTypeId=' . $membership_TypeId_ce21;

                            $result = wp_remote_get($get_Memberships_APi_URL_ce21);
                            $err = is_wp_error($result);
                            if ($err) {
								throw new Exception($result['body'], 1);
							}
							$Memberships_access_ce21 = json_decode($result['body'], true);

							if ($Memberships_access_ce21['success'] == true && $Memberships_access_ce21['data']['groupAccessible'] == true) {
								//echo $get_Memberships_APi_URL_ce21;
								//echo get_permalink($postId_ce21);
								wp_redirect(get_permalink($postId_ce21), 301);
								exit;
							}
						}
					} else {
						$articalID_ce21 = get_permalink($postId_ce21);
						if (!empty($articalID_ce21)) {
							wp_redirect(get_permalink($postId_ce21));
							exit;
						}
					}
				}else{
                    $postId_ce21 = sanitize_text_field($_GET['postid']);
                    $return_url_ce21 = home_url("sign-in-ce21") . '/?postid=' . $postId_ce21 . '&_=' . round(microtime(true) * 1000);

					//$current_url_ce21 = home_url(add_query_arg(array('postid' => $postId_ce21), $wp->request));
					//$return_url_ce21 = get_option('authorizeURI_ce21') . "/wpauthorize/login?returnURL=" . $current_url_ce21; ?>
					<script type="text/javascript">location.href = "<?php echo esc_url($return_url_ce21); ?>";</script><?php
					exit;
				}

			}
		}
    } catch (Exception $e) {
        $errorMsg = ' Error on line '.$e->getLine().' in '.$e->getFile() . ' : <b>'.$e->getMessage().'</b>';
        ce21_error_log_api($errorMsg);
    }
	}
}

/**
* Articles Authentication(Custom Error) page could not deleted
* This condition for apply all user role
*/
add_action('wp_trash_post', 'ce21_restrict_post_deletion',10, 1);

function ce21_restrict_post_deletion($post_id) {
	
	if (get_post_type() == 'page' ) {
        $page = get_page_by_path( 'article-authentication-ce21' );
        $signInPage = get_page_by_path( 'sign-in-ce21' );
        
        if ($post_id == $page->ID || $post_id == $signInPage->ID) {
            wp_die('Sorry, You are not allowed to delete this page.');
        }
    }
}

/**
* Remove admin Dashboard page for ce21 customer user view only
*/
add_action( 'admin_menu', 'ce21_remove_menus' );

function ce21_remove_menus() {

	$user = wp_get_current_user();
	if ( in_array( 'CE21_Customer', (array) $user->roles ) ) {
		remove_menu_page( 'index.php' );  //Dashboard
	}
}

/**
 * Error log send through API global function
 *
 */

function ce21_error_log_api($data) {
    $authorizeURI_ce21 = get_option( 'authorizeURI_ce21' );
    $get_error_log_api_url_ce21 = $authorizeURI_ce21.'/WPAuthorize/ErrorLog';
    $LoginUser = "-";
    $current_user_ce21 = wp_get_current_user();
    if ($current_user_ce21) {
        $LoginUser = $current_user_ce21->user_login;
    }
    unset($_SERVER['SERVER_SIGNATURE']);

    $Etitle = $out = strlen($data) > 300 ? substr($data,0,300)."..." : $data;

    $errorArray_ce21 = array (
        'Title' => $Etitle,
        'Description' => $data,
        'Time' => current_time( 'mysql', 1 ),
        'ServerVariables' => sanitize_post($_SERVER),
        'LoginUser' => $LoginUser,
        'SiteDetail' => get_site_url(),
        'UserIp' => sanitize_text_field($_SERVER['REMOTE_ADDR'])
    );
    $errorArray_ce21 = json_encode($errorArray_ce21);
    $error_data = array(
         'error' => $errorArray_ce21
    );
    $encoded_data = json_encode($error_data);
    $args = array(
        'method' => 'POST',
        'timeout' => 30,
        'redirection' => 10,
        'httpversion' => '1.1',
        'sslverify' => false,
        'headers' => array(
            "Content-Type: application/json",
            "Postman-Token: 7f3b6deb-18e7-47cd-b0bc-bd6d071afd78",
            "cache-control: no-cache",
        ),
        'body' => $encoded_data,
        'cookies' => array()
    );
    $response = wp_remote_post($get_error_log_api_url_ce21, $args);
}

/**
* if new site created, create table for new site
*/
global $wp_version;
if ( version_compare( $wp_version, '5.1', '<' ) ) {
    add_action( 'wpmu_new_blog', 'ce21_on_create_table_when_new_site_created' );
}
else {
    add_action( 'wp_initialize_site', 'ce21_on_create_table_when_new_site_created', 99 );
}

add_action( 'activate_blog', 'ce21_on_create_table_when_new_site_created' );

function ce21_on_create_table_when_new_site_created( $blog_id ) {
    if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if ( $blog_id instanceof WP_Site ) {
        $blog_id = (int) $blog_id->blog_id;
    }

    if ( is_plugin_active_for_network( 'single-sign-on-ce21/single-sign-on-ce21.php' ) ) {
        switch_to_blog( $blog_id );
        ce21_create_table();
        ce21_create_api_settings_table();
        ce21_create_calendar_events_table();
        ce21_create_authentication_page();
        ce21_sign_in_form_page();
        ce21_add_customer_role();
        restore_current_blog();
    }
}

/*
 * AJAX script register
 */
function ce21_ajax_login_init(){

    wp_register_script('ajax-login-script', plugin_dir_url( __FILE__ ) . 'public/js/single-sign-on-ce21-ajax.js', array('jquery') );
    wp_enqueue_script('ajax-login-script');

    wp_localize_script( 'ajax-login-script', 'ajax_login_object', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'redirecturl' => home_url(),
        'loadingmessage' => __('Sending user info, please wait...')
    ));

    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action( 'wp_ajax_nopriv_ce21_sign_in_ajax_api', 'ce21_sign_in_ajax_api' );
}

// Execute the action only if the user isn't logged in

add_action('init', 'ce21_ajax_login_init');


/*
 * This function call will check ce21_api_settings table exist or not. If not exist then it will create.
 * */
ce21_create_api_settings_table();


/*
 * This function call will check ce21_calendar_events table exist or not. If not exist then it will create.
 * */
ce21_create_calendar_events_table();

/**
 * This function runs when WordPress completes its upgrade process
 * It iterates through each plugin updated to see if ours is included
 * @param $upgrader_object Array
 * @param $options Array
 */
function wp_upe_upgrade_completed($upgrader_object, $options)
{
    // The path to our plugin's main file
    $our_plugin = plugin_basename(__FILE__);
    // If an update has taken place and the updated type is plugins and the plugins element exists
    if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset($options['plugins'])) {
        // Iterate through the plugins being updated and check if ours is there
        foreach ($options['plugins'] as $plugin) {
            if ($plugin == $our_plugin) {
                // Set a transient to record that our plugin has just been updated
                set_transient('wp_upe_updated', 1);
            }
        }
    }
}
add_action('upgrader_process_complete', 'wp_upe_upgrade_completed', 10, 2);
