<?php
/**
 * Create  Table code
 * Check to see if the table exists already, if not, then create it  -function
 */
function ce21_create_table(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'membership_types_ce21';
    try {
        if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    TenantId mediumint(9) NOT NULL,
                    membershipTypeId mediumint(9) NOT NULL,
                    membershipName text NOT NULL,
                    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY  (id)
                ) $charset_collate;";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            if ($wpdb->last_error) {
                throw new Exception($wpdb->last_error);
            }
        }
    } catch (Exception $e){
        $errorMsg = ' Error on line '.$e->getLine().' in '.$e->getFile() . ' : <b>'.$e->getMessage().'</b>';
        ce21_error_log_api($errorMsg);
    }
}

/**
 * Create new customer role for site  -function
 */
function ce21_add_customer_role(){
    add_role('CE21_Customer', __(
        'CE21 Customer'),
        array(
            'read'  => true, // Allows a user to read
        )
    );
}

/**
 * Create page code -function
 */

function ce21_create_authentication_page(){
    $new_page_title = 'Article Authentication';
    $slug = 'article-authentication-ce21';
    $new_page_content = 'Sorry, You are not allowed to access this article content.';
    $new_page_template = ''; //ex. template-custom.php. Leave blank if you don't want a custom page template.

    //don't change the code bellow, unless you know what you're doing
    //$page_check = get_page_by_title($new_page_title);
    $page_check = get_page_by_path( 'article-authentication-ce21' );
    //print_r($page);
    $new_page = array(
        'post_type' => 'page',
        'post_title' => $new_page_title,
        'post_name' => $slug,
        'post_content' => $new_page_content,
        'post_status' => 'publish',
    );

    if(!isset($page_check->ID)){
        $new_page_id = wp_insert_post($new_page);
        update_post_meta($new_page_id,'_article_authentication',$new_page_id);
        if(!empty($new_page_template)){
            update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
        }
    } else if( $page_check->post_status == 'trash' ) {
        wp_update_post(array(
            'ID' => $page_check->ID,
            'post_status' => 'publish'
        ));
    }
}
// Add our text to the bulk edit box
add_action( 'bulk_edit_custom_box', 'sso_ce21_on_bulk_edit_custom_box', 10, 2);

function sso_ce21_on_bulk_edit_custom_box($column_name, $post_type)
{
    global $post;
    if ('membership_plan_ce21' == $column_name) {
        try {
            global $wpdb;
            $table_name_ce21 		= $wpdb->prefix . 'membership_types_ce21';
            $Memberships_Data_ce21 	= $wpdb->get_results("SELECT * FROM ".$table_name_ce21." ORDER BY id ASC");
            $details 				= get_post_meta( $post->ID, '_post_authentication_custom_metabox_ce21', false );

            if(! empty($Memberships_Data_ce21)) {
                ?>
                <fieldset class="inline-edit-col-center" style="max-width:400px;">
                    <div class="inline-edit-col">
                        <span class="title inline-edit-categories-label">Groups</span>
                        <ul class="cat-checklist category-checklist">
                            <label><input type="checkbox" id="ckbulkCheckAll" /> Select All</label>
                            <?php foreach ($Memberships_Data_ce21 as $key => $value) { ?>
                                <label> <input type="checkbox" class="be_group radio customCheckAll" value="<?php echo esc_attr($value->membershipTypeId); ?>"
                                               name="_post_authentication_custom_metabox_ce21[]" />
                                        <?php echo esc_html($value->membershipName); ?>
                                </label>
                            <?php } ?>
                        </ul>
                    </div>
                </fieldset>
                <?php
            } else {
                echo "Groups are not available.";
            }
            ce21_wp_error_handle();
        } catch (Exception $e) {
            $errorMsg = ' Error on line '.$e->getLine().' in '.$e->getFile() . ' : <b>'.$e->getMessage().'</b>';
            ce21_error_log_api($errorMsg);
        }
    }
}


add_action( 'wp_ajax_manage_wp_posts_using_bulk_quick_save_bulk_edit', 'sso_ce21_manage_wp_posts_using_bulk_quick_save_bulk_edit' );

function sso_ce21_manage_wp_posts_using_bulk_quick_save_bulk_edit(){

    try {
        // we need the post IDs
        $post_ids = ( isset( $_POST[ 'post_ids' ] ) && !empty( $_POST[ 'post_ids' ] ) ) ? sanitize_post($_POST[ 'post_ids' ]) : NULL;
        $new_values = ( isset( $_POST[ 'fields' ] ) && !empty( $_POST[ 'fields' ] ) ) ? sanitize_post($_POST[ 'fields' ]) : NULL;

        // if we have post IDs
        if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {

            // get the custom fields

            foreach( $post_ids as $post_id ) {

                $values_to_save = array();

                if (!empty($new_values)) {
                    foreach($new_values as $new_value ) {
                        $values_to_save[] = $new_value ;
                    }
                }
                $original_meta = get_post_meta( $post_id, '_post_authentication_custom_metabox_ce21' ,true);

                if( empty($original_meta) ){
                    update_post_meta( $post_id, '_post_authentication_custom_metabox_ce21',$values_to_save );
                } else{
                    $new_meta = array_merge($values_to_save,$original_meta);

                    update_post_meta( $post_id, '_post_authentication_custom_metabox_ce21',$new_meta );
                }



            }

        }

    } catch (Exception $e){
        $errorMsg = ' Error on line '.$e->getLine().' in '.$e->getFile() . ' : <b>'.$e->getMessage().'</b>';
        ce21_error_log_api($errorMsg);
    }
}
add_action('wp_ajax_data_fetch' , 'sso_ce21_data_fetch');
add_action('wp_ajax_nopriv_data_fetch','sso_ce21_data_fetch');
function sso_ce21_data_fetch(){
    if( isset($_POST['post_id']) ){
        $pid = sanitize_text_field($_POST['post_id']);
        $details =  get_post_meta( $pid, '_post_authentication_custom_metabox_ce21', true );
        if($details) {
            echo wp_send_json(array('success' => true, 'result' => $details));
        }
        else {
            wp_send_json_error();
        }
        exit();
    }
}
require plugin_dir_path( __FILE__ ) . '/quick-edit-functions.php';


// JavaScript functions to set/update checkbox

/*
*  Create Sign In Page
*/
function ce21_sign_in_form_page() {
    $new_page_title = 'Sign In';
    $slug = 'sign-in-ce21';
    $new_page_content = '[ce21-sso-sign-in]';
    $new_page_template = '';

    $page_check = get_page_by_path( 'sign-in-ce21' );
    //print_r($page);
    $new_page = array(
        'post_type' => 'page',
        'post_title' => $new_page_title,
        'post_name' => $slug,
        'post_content' => $new_page_content,
        'post_status' => 'publish',
    );

    if(!isset($page_check->ID)){
        $new_page_id = wp_insert_post($new_page);
        update_post_meta($new_page_id,'_sign_in_ce21',$new_page_id);
        if(!empty($new_page_template)){
            update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
        }
    } else if( $page_check->post_status == 'trash' ) {
        wp_update_post(array(
            'ID' => $page_check->ID,
            'post_status' => 'publish'
        ));
    }
}

function ce21_sign_in_ajax_api()
{
    wp_verify_nonce( sanitize_text_field($_POST['security']), 'ajax-login-nonce' );
    $ce21Email = sanitize_text_field($_POST['username']);
    $ce21Password = sanitize_text_field($_POST['password']);
    $postid = sanitize_text_field($_POST['postid']);
    $postURL = get_permalink($postid);
    $post_data = array(
        'email' => $ce21Email,
        'password' => $ce21Password,
    );

    $authorizeURI_ce21 = get_option( 'authorizeURI_ce21' );
    $get_error_log_api_url_ce21 = $authorizeURI_ce21.'/WPAuthorize/VerifyLogin';
	
    $args = array(
        'method' => 'POST',
        'timeout' => 30,
        'redirection' => 10,
        'httpversion' => '1.1',
        'sslverify' => false,
        'headers' => array(
            "Content-Type: application/json"
        ),
        'body' => $post_data,
        'cookies' => array()
    );
    $response = wp_remote_post($get_error_log_api_url_ce21, $args);

    $newResponse = json_decode($response['body']);
    if($newResponse->isSuccess){
        $sesionHelper = new session_helper_ce21($newResponse->data->tenantId);
		
		$sesionHelper->create_session( $newResponse->data->firstName, $newResponse->data->lastName, $newResponse->data->email, $newResponse->data->tenantId, time() );			
        		
		if(isset($newResponse->data->catalogLoginUrl) && $newResponse->data->catalogLoginUrl != "" ) {
			$loginUrl = $newResponse->data->catalogLoginUrl;
			$redirectUrl = $loginUrl . "&returnUrl=" . $postURL;
		}else{
			$redirectUrl = $postURL;
		}
		
        echo wp_send_json(array('isSuccess' => true, 'redirectURL' => $redirectUrl, 'message'=> 'Login Successfully'));
    }else{
        echo wp_send_json($newResponse);
    }

    die();
}


/*
 * Function used to generate shotcode for ce21 sso signin shortcode
 * */
add_shortcode('ce21-sso-sign-in','ce21_sso_sign_in_shortcode');
function ce21_sso_sign_in_shortcode()
{
    ob_start();
    include_once( SINGLE_SIGN_ON_CE21__PLUGIN_DIR . 'public/partials/single-sign-on-ce21-sso-sign-in.php');
    return ob_get_clean();
}


/*
 * Function used to check ce21_api_settings table exist or not. If not exist then it will create.
 * */
function ce21_create_api_settings_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ce21_api_settings';
    try
    {
        if ($wpdb->get_var("show tables like '$table_name'") != $table_name)
        {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                    Id int(11) NOT NULL AUTO_INCREMENT,
                    BaseURL varchar(256) NOT NULL,
                    ClientId varchar(256) NOT NULL,
                    SecretKey varchar(500) NOT NULL,
                    CatalogURL varchar(500) NOT NULL,
                    AccessToken text NULL,
                    ExpiryDate datetime NULL,
                    CreatedOn timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    ModifiedOn timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY  (id)
                ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            if ($wpdb->last_error)
            {
                throw new Exception($wpdb->last_error);
            }
        }
    }
    catch (Exception $e)
    {
        $errorMsg = ' Error on line '.$e->getLine().' in '.$e->getFile() . ' : <b>'.$e->getMessage().'</b>';
        ce21_error_log_api($errorMsg);
    }
}


/*
 * Function used to get API Settings data.
 * */
function get_ce21_ss_api_settings_data()
{
    global $wpdb;
    $data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."ce21_api_settings");
    return $data;
}


/*
 * Function used to save API Settings
 * */
add_action('wp_ajax_ce21_single_sign_on_save_api_settings','ce21_single_sign_on_save_api_settings');
add_action('wp_ajax_nopriv_ce21_single_sign_on_save_api_settings','ce21_single_sign_on_save_api_settings');
function ce21_single_sign_on_save_api_settings()
{
    global $wpdb;
    if(isset($_POST))
    {
        if ($_POST['api_settings_id'] != "")
        {
            $update_record = $wpdb->update($wpdb->prefix . 'ce21_api_settings', array(
                                    'BaseURL' => sanitize_url($_POST['base_url']),
                                    'ClientId' => sanitize_text_field($_POST['client_id']),
                                    'SecretKey' => sanitize_text_field($_POST['secret_key']),
                                    'CatalogURL' => sanitize_url($_POST['catalog_url']),
                                    'AccessToken' => NULL,
                                    'ExpiryDate' => NULL,
                                    'ModifiedOn' => date('Y-m-d H:i:s')), array(
                                        'Id' => sanitize_text_field($_POST['api_settings_id'])
                                    )
                                );

            if ($update_record)
            {
                $access_token_response = get_access_token();

                if ($access_token_response['success'])
                {
                    $response = array(
                        'success' => true,
                        'message' => 'API Settings saved successfully!'
                    );
                }
                else
                {
                    $response = array(
                        'success' => false,
                        'message' => $access_token_response['message']
                    );
                }
            }
            else
            {
                $response = array(
                    'success' => false,
                    'message' => 'Something went wrong while saving the data!'
                );
            }


        }
        else
        {
            $insert_record = $wpdb->insert($wpdb->prefix . 'ce21_api_settings', array(
                                    'BaseURL' => sanitize_url($_POST['base_url']),
                                    'ClientId' => sanitize_text_field($_POST['client_id']),
                                    'SecretKey' => sanitize_text_field($_POST['secret_key']),
                                    'CatalogURL' => sanitize_url($_POST['catalog_url']),
                                    'AccessToken' => NULL,
                                    'ExpiryDate' => NULL,
                                    'CreatedOn' => date('Y-m-d H:i:s'),
                                    'ModifiedOn' => date('Y-m-d H:i:s')
                                )
                            );

            if ($insert_record)
            {
                $access_token_response = get_access_token();

                if ($access_token_response['success'])
                {
                    $response = array(
                        'success' => true,
                        'message' => 'API Settings saved successfully!'
                    );
                }
                else
                {
                    $response = array(
                        'success' => false,
                        'message' => $access_token_response['message']
                    );
                }
            }
            else
            {
                $response = array(
                    'success' => false,
                    'message' => 'Something went wrong while saving the data!'
                );
            }
        }
    }
    else
    {
        $response = array(
            'success' => false,
            'message' => 'Something went wrong!'
        );
    }

    if (!empty($response)) {
        echo json_encode($response);
        exit;
    }
}


/*
 * Function used to generate shotcode for ce21-single-sign-on calendar
 * */
add_shortcode('ce21-calendar','ce21_single_sign_on_calendar_shortcode');
function ce21_single_sign_on_calendar_shortcode()
{
    ob_start();
    include_once(SINGLE_SIGN_ON_CE21__PLUGIN_DIR . 'public/partials/single-sign-on-ce21-calendar.php');
    return ob_get_clean();
}


/*
 * Function used to get ce21 calendar events.
 * */
add_action('wp_ajax_get_ce21_single_sign_on_calendar_events','get_ce21_single_sign_on_calendar_events');
add_action('wp_ajax_nopriv_get_ce21_single_sign_on_calendar_events','get_ce21_single_sign_on_calendar_events');
function get_ce21_single_sign_on_calendar_events()
{
    $events_array = [];
    /*Calendar Event Fetch using API starts*/
    $access_token_response = get_access_token();
    $date = new DateTime(sanitize_text_field($_REQUEST['start']));
    $startD = $date->format('Y-m-d');

    if ($access_token_response['success']) {
        $api_settings = get_ce21_ss_api_settings_data();
        $api_Access_token = (!empty($api_settings)) ?  $api_settings->AccessToken : '';
        $base_url = (!empty($api_settings)) ? $api_settings->BaseURL : '';
        $url = $base_url."/wp/calendar?startDate=".$startD;

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_Access_token
            )
        );
        $res = wp_remote_get($url, $args);

        //print_r($res);
        $res = json_decode($res['body']);

        if(!empty( $res )){
            $timezone = $res->momentZone;
            $dateFormat = $res->dateFormat;
            date_default_timezone_set($timezone);
            $timezoneText = date('T'); // => IST
            if($dateFormat == 'dd/MM/yyyy'){
                $dtFormate = 'd/m/Y';
            }else{
                $dtFormate = 'm/d/Y';
            }
        }else{
            $timezoneText = date('T'); // => IST
            $dtFormate = 'm/d/Y';
        }

        if(!empty( $res->events )){
            foreach ($res->events as $res_event)
            {
                $timestamp=$res_event->start;
                $dt =  date($dtFormate, $timestamp/1000);
                $sdt =  date('Y-m-d H:i:s', $timestamp/1000);
                $timestampend=$res_event->end;
                $edt =  date('Y-m-d H:i:s', $timestampend/1000);
                $desctime = str_replace(array( '(', ')' ), '', $res_event->time);
                $desc = '<h4>'.$res_event->title.'</h4>';
                $desc .= '<div>Type: '.$res_event->producttype.'</div>';
                $desc .= '<div>Time: '.$dt.' '.$desctime.'</div>';
                $desc .= '<div class="ce21PriceDetail">'.$res_event->price.'</div>';
                $new_event = [
                    'title'=>$res_event->title,
                    'description'=> $desc,
                    'start'=>$sdt,
                    'end'=>$edt,
                    'type'=>$res_event->producttype,
                    'time'=>$res_event->time,
                    //'allDay'=>true,
                    'className'=>$res_event->class,
                    'url'=>$res_event->url
                ];

                array_push($events_array,$new_event);
            }
        }

    }

    /*Calendar Event Fetch using API ends*/

    global $wpdb;
    $ce21_calendar_events = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ce21_calendar_events");

    foreach ($ce21_calendar_events as $ce21_calendar_event)
    {
        /*$start_date = date('Y-m-d', strtotime($ce21_calendar_event->StartDate));
        $end_date = date('Y-m-d', strtotime($ce21_calendar_event->EndDate));*/
        $dateObject = new DateTime($ce21_calendar_event->StartDate);
        $Time =  $dateObject->format('h:i A');
        $Time =  $Time.' ('.$timezoneText.')';
        $TimeText =  $dateObject->format($dtFormate.' h:i A');

        $desc = '<h4>'.$ce21_calendar_event->Title.'</h4>';
        $desc .= '<div>Time: '.$TimeText.' '.$timezoneText.'</div>';

        $event = [
                'title'=>$ce21_calendar_event->Title,
                'description'=> $desc,
                'start'=>$ce21_calendar_event->StartDate,
                'end'=>$ce21_calendar_event->EndDate,
                //'allDay'=>true,
                'type'=>'',
                'time'=>$Time,
                'className'=>'',
                //'className'=>'fct-ev',
                'url'=>$ce21_calendar_event->Url
        ];

        array_push($events_array,$event);
    }

    echo json_encode($events_array);exit;
}

function ce21_my_custom_js() {
    $style = 'bootstrap';
    if( ( wp_style_is( $style, 'queue' ) ) && ( wp_style_is( $style, 'done' ) ) ) {
        echo '<script type="text/javascript">var isBootstrapUse = true;</script>';
    }else{
        echo '<script type="text/javascript">var isBootstrapUse = false;</script>';
    }

}
// Add hook for front-end <head></head>
add_action( 'wp_head', 'ce21_my_custom_js' );


/*
 * Function used to check ce21_calendar_events table exist or not. If not exist then it will create.
 * */
function ce21_create_calendar_events_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ce21_calendar_events';
    try
    {
        if ($wpdb->get_var("show tables like '$table_name'") != $table_name)
        {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                    Id int(11) NOT NULL AUTO_INCREMENT,
                    Title text NOT NULL,
                    StartDate datetime NULL,
                    EndDate datetime NULL,
                    Url text NULL,
                    CreatedOn timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    ModifiedOn timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY  (id)
                ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            if ($wpdb->last_error)
            {
                throw new Exception($wpdb->last_error);
            }
        }
    }
    catch (Exception $e)
    {
        $errorMsg = ' Error on line '.$e->getLine().' in '.$e->getFile() . ' : <b>'.$e->getMessage().'</b>';
        ce21_error_log_api($errorMsg);
    }
}


/*
 * Function used to add new calendar event
 * */
add_action('wp_ajax_ce21_ss_add_new_calendar_event','ce21_ss_add_new_calendar_event');
add_action('wp_ajax_nopriv_ce21_ss_add_new_calendar_event','ce21_ss_add_new_calendar_event');
function ce21_ss_add_new_calendar_event()
{
    global $wpdb;
    if(isset($_POST))
    {

        $start_date = date('Y-m-d H:i:s', strtotime( sanitize_text_field($_POST['start_date']) ));
        $end_date = date('Y-m-d H:i:s', strtotime( sanitize_text_field($_POST['end_date']) ));

        $insert_record = $wpdb->insert($wpdb->prefix . 'ce21_calendar_events', array(
                'Title' => sanitize_text_field($_POST['title']),
                'StartDate' => $start_date,
                'EndDate' => $end_date,
                'Url' => sanitize_url($_POST['url']),
                'CreatedOn' => date('Y-m-d H:i:s'),
                'ModifiedOn' => date('Y-m-d H:i:s')
            )
        );

        if ($insert_record)
        {
            $response = array(
                'success' => true,
                'message' => 'Calender event added successfully!'
            );

        }
        else
        {
            $response = array(
                'success' => false,
                'message' => 'Something went wrong while saving the data!'
            );
        }

    }
    else
    {
        $response = array(
            'success' => false,
            'message' => 'Something went wrong!'
        );
    }

    if (!empty($response)) {
        echo json_encode($response);
        exit;
    }
}


/*
 * Function used to delete ce21-single-sign-on-calendar-event
 * */
add_action('wp_ajax_ce21_ss_delete_calendar_event','ce21_ss_delete_calendar_event');
add_action('wp_ajax_nopriv_ce21_ss_delete_calendar_event','ce21_ss_delete_calendar_event');
function ce21_ss_delete_calendar_event()
{
    global $wpdb;
    if (isset($_POST))
    {
        $wpdb->delete($wpdb->prefix . 'ce21_calendar_events', array(
                'Id' => sanitize_text_field($_POST['id'])
            )
        );

        $return_response = array(
            'success' => true,
            'message' => 'Calendar event deleted successfully!'
        );
    }
    else
    {
        $return_response = array(
            'success' => false,
            'message' => 'Something went wrong!'
        );
    }

    if (!empty($return_response))
    {
        echo json_encode($return_response);
        exit;
    }
}


/*
 * Function used to get ce21 single calendar events.
 * */
add_action('wp_ajax_get_ce21_single_sign_on_calendar_event','get_ce21_single_sign_on_calendar_event');
add_action('wp_ajax_nopriv_get_ce21_single_sign_on_calendar_event','get_ce21_single_sign_on_calendar_event');
function get_ce21_single_sign_on_calendar_event()
{
    global $wpdb;
    if (isset($_POST))
    {
        $event = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ce21_calendar_events WHERE Id=%d", sanitize_text_field($_POST['id'])));

        $event_array = [
                'id' => $event->Id,
                'title' => $event->Title,
                'start_date' => $event->StartDate,
                'end_date' => $event->EndDate,
                'url' => $event->Url
        ];

        $return_response = array(
            'success' => true,
            'event' => $event_array,
            'message' => 'Calendar event fetched successfully!'
        );
    }
    else
    {
        $return_response = array(
            'success' => false,
            'event' => '',
            'message' => 'Something went wrong!'
        );
    }

    if (!empty($return_response))
    {
        echo json_encode($return_response);
        exit;
    }
}


/*
 * Function used to add new calendar event
 * */
add_action('wp_ajax_ce21_ss_edit_calendar_event','ce21_ss_edit_calendar_event');
add_action('wp_ajax_nopriv_ce21_ss_edit_calendar_event','ce21_ss_edit_calendar_event');
function ce21_ss_edit_calendar_event()
{
    global $wpdb;
    if(isset($_POST))
    {
        $start_date = date('Y-m-d H:i:s', strtotime( sanitize_text_field($_POST['start_date']) ));
        $end_date = date('Y-m-d H:i:s', strtotime( sanitize_text_field($_POST['end_date']) ));

        $update_record = $wpdb->update($wpdb->prefix . 'ce21_calendar_events', array(
            'Title' => sanitize_text_field($_POST['title']),
            'StartDate' => $start_date,
            'EndDate' => $end_date,
            'Url' => sanitize_url($_POST['url']),
            'ModifiedOn' => date('Y-m-d H:i:s')), array(
                'Id' => sanitize_text_field($_POST['id'])
            )
        );

        if ($update_record)
        {
            $response = array(
                'success' => true,
                'message' => 'Calender event updated successfully!'
            );

        }
        else
        {
            $response = array(
                'success' => false,
                'message' => 'Something went wrong while updating the data!'
            );
        }
    }
    else
    {
        $response = array(
            'success' => false,
            'message' => 'Something went wrong!'
        );
    }

    if (!empty($response)) {
        echo json_encode($response);
        exit;
    }
}


/*
 * Function used to load ce21-single-sign-on-calendar-events into datatable
 * */
add_action('wp_ajax_load_ce21_single_sign_on_calendar_events', 'load_ce21_single_sign_on_calendar_events');
add_action('wp_ajax_nopriv_load_ce21_single_sign_on_calendar_events', 'load_ce21_single_sign_on_calendar_events');

function load_ce21_single_sign_on_calendar_events() {
    global $wpdb;

    $request = sanitize_post($_REQUEST);
    $col = array(
        0 => 'title',
        1 => 'start_date',
        2 => 'end_date',
        3 => 'url',
        4 => 'action'
    );  //create column like table in database

    $sql = "SELECT * FROM " . $wpdb->prefix . "ce21_calendar_events";

    $query = $wpdb->get_results($sql);
    $totalData = $wpdb->num_rows;
    $totalFilter = $totalData;

    //Search
    $sql = "SELECT * FROM " . $wpdb->prefix . "ce21_calendar_events";
    $font_family = str_replace("'", "\'", $request['search']['value']);
    $column = $col[$request['order'][0]['column']];
    $order = $request['order'][0]['dir'];
    if (!empty($request['search']['value'])) {
        $sql .= " WHERE (Title Like %s OR StartDate Like %s OR EndDate Like %s OR Url Like %s )";
        $query = $wpdb->get_results($wpdb->prepare($sql, $request['search']['value'] ."%", $request['search']['value'] . "%", $font_family . "%", $request['search']['value'] . "%"));
        $totalData = $wpdb->num_rows;

        $sql .= " ORDER BY %s %s LIMIT %d , %d";
        $query = $wpdb->get_results($wpdb->prepare($sql, $request['search']['value'] ."%", $request['search']['value'] . "%", $font_family . "%", $request['search']['value'] . "%", $column, $order, $request['start'], $request['length']));
    }else{
        $query = $wpdb->get_results($sql);
        $totalData = $wpdb->num_rows;
        $sql .= " ORDER BY %s %s LIMIT %d , %d";
        $query = $wpdb->get_results($wpdb->prepare($sql, $column, $order, $request['start'], $request['length']));
    }
    //Order

    $data = array();

    foreach ($query as $row) {
        $subdata = array();
        $subdata[] = ($row->Title != '') ? $row->Title : '-';
        $subdata[] = ($row->StartDate != '') ? $row->StartDate : '-';;
        $subdata[] = ($row->EndDate != '') ? $row->EndDate : '-';;
        $subdata[] = ($row->Url != '') ? $row->Url : '-';;
        $subdata[] = '<button onclick="ce21_ss_open_edit_calendar_event_modal('.$row->Id.');">Edit</button>
                      <button onclick=  "ce21_ss_delete_calendar_event('.$row->Id.');">Delete</button>';
        $data[] = $subdata;
    }

    $json_data = array(
        "draw" => intval($request['draw']),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFilter),
        "data" => $data
    );
    echo json_encode($json_data);
    exit;
}

/*Mini Calendar code starts*/
function ce21_mini_calendar_shortcode( $atts ) {
    $dateComponents = getdate();
    $month = $dateComponents['mon'];
    $year = $dateComponents['year'];

    ob_start();
    ?>
    <div id="ce21MiniCalendarContainer">
        <?php ce21_getMiniCalendarHTML($month,$year); ?>
    </div>
    <div id="ce21MiniCalendarLoader">
        <span class="loader-inner line-scale-pulse-out">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </span>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('body').tooltip({
                selector: '.mini-tooltip',
                html: true
            });
            /*if (isBootstrapUse) {
                jQuery('.mini-tooltip').tooltip({html: true,});
            } else {
                var tooltip = new Tooltip('.mini-tooltip', {
                    html: true
                });
            }*/
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'ce21-mini-calendar', 'ce21_mini_calendar_shortcode' );

function ce21_getMiniCalendarHTML($month,$year){

    /*Mini Calendar Event Fetch using API starts*/
    $access_token_response = get_access_token();
    //$date = new DateTime($year);
    //$startD = $date->format('Y-m-d');
    $startD = $year.'-'.$month.'-01';

    if ($access_token_response['success']) {
        $api_settings = get_ce21_ss_api_settings_data();
        $api_Access_token = (!empty($api_settings)) ? $api_settings->AccessToken : '';
        $base_url = (!empty($api_settings)) ? $api_settings->BaseURL : '';
        $url = $base_url . "/wp/minicalendar?startDate=" . $startD;

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_Access_token
            )
        );
        $res = wp_remote_get($url, $args);
        $res = json_decode($res['body']);
    }
    $eventDays = [];
    $i=0;
    //print_r($res);  
    if(!empty($res) && !empty($res->days)){
        foreach ($res->days as $days){
            if($days->hasProduct){
                $eventDays[$i]['day'] = $days->day;
                $eventDays[$i]['color'] = $days->color;
                $eventDays[$i]['className'] = $days->className;
                $eventDays[$i]['tooltip'] = $days->tooltip;
                $eventDays[$i]['url'] = $res->fullUrl;
                $i++;
            }
        }
    }
    //print_r($eventDays);
    /*Mini Calendar Event Fetch using API starts*/

    /*Mini Calendar Event Fetch from database start*/
     global $wpdb; 
     $start_dt = new DateTime($year.'-'.$month.'-01 00:00:00');
     $startDate = $start_dt->format('Y-m-d H:i:s');
     $end_dt = new DateTime($year.'-'.$month.'-01 23:59:59');
     $end_dt->modify('last day of this month');
     $endDate = $end_dt->format('Y-m-d H:i:s');    
     
     $ce21_calendar_events = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."ce21_calendar_events WHERE `StartDate` BETWEEN %s AND %s", $startDate, $endDate));
   
     foreach ($ce21_calendar_events as $ce21_calendar_event)
     {     
         $eventDays[$i]['day'] = date('d', strtotime($ce21_calendar_event->StartDate));
         $eventDays[$i]['color'] = '#bf9000';
         $eventDays[$i]['className'] = 'cal-day';
         $eventDays[$i]['tooltip'] = $ce21_calendar_event->Title;
         $eventDays[$i]['url'] = !empty($ce21_calendar_event->Url) ? $ce21_calendar_event->Url : (!empty($res) ? $res->fullUrl : "");         
         $i++;
     } 
     /*Mini Calendar Event Fetch from database ends*/    

    $dateObj   = DateTime::createFromFormat('!m', $month);
    $monthName = $dateObj->format('F');
    $tgl = '1 '.$monthName.' '.$year;
    $prevMonth = date("m",mktime(0,0,0,date("m", strtotime($tgl))-1,1,date("Y", strtotime($tgl))));
    $prevYear  = date("Y",mktime(0,0,0,date("m", strtotime($tgl))-1,1,date("Y", strtotime($tgl))));
    $nextMonth = date("m",mktime(0,0,0,date("m", strtotime($tgl))+1,1,date("Y", strtotime($tgl))));
    $nextYear  = date("Y",mktime(0,0,0,date("m", strtotime($tgl))+1,1,date("Y", strtotime($tgl))));

    // What is the first day of the month in question?
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);
    // How many days does this month contain?
    $numberDays = date('t',$firstDayOfMonth);
    // Retrieve some information about the first day of the
    // month in question.
    $dateComponents = getdate($firstDayOfMonth);
    // What is the name of the month in question?
    $monthName = $dateComponents['month'];
    // What is the index value (0-6) of the first day of the
    // month in question.
    $dayOfWeek = $dateComponents['wday'];
    ?>
    <div id="calendarContainer" class="calendarContainer" style="margin-bottom:15px;">
        <table class="calendar" style="border-collapse: collapse;">
            <tbody>
                <tr>
                    <td>
                        <a class="ce21_calendar_nav" aria-label="Click here to go to previous month" data-month="<?php echo esc_attr($prevMonth); ?>"
                           data-year="<?php echo esc_attr($prevYear); ?>" href="javascript: void(0);" >&lt;&lt;</a>
                    </td>
                    <td style="text-align: center; width: 130px">
                        <?php if(empty($res)){ ?>
                            <a href=""><?php echo esc_html($monthName.' '.$year); ?></a>
                        <?php } else { ?>
                            <a href="<?php echo esc_url($res->fullUrl); ?>"><?php echo esc_html($monthName.' '.$year); ?></a>
                        <?php } ?>
                    </td>
                    <td>
                        <a class="ce21_calendar_nav" aria-label="Click here to go to next month" data-month="<?php echo esc_attr($nextMonth); ?>"
                           data-year="<?php echo esc_attr($nextYear); ?>" href="javascript: void(0);">&gt;&gt;</a>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="calendar" cellspacing="2" cellpadding="2" style="border-width: 0px; border-collapse: collapse;">
            <tbody>
                <tr>
                    <th align="center" aria-label="Sunday" abbr="Sunday" scope="col">Sun</th>
                    <th align="center" aria-label="Monday" abbr="Monday" scope="col">Mon</th>
                    <th align="center" aria-label="Tuesday" abbr="Tuesday" scope="col">Tue</th>
                    <th align="center" aria-label="Wednesday" abbr="Wednesday" scope="col">Wed</th>
                    <th align="center" aria-label="Thursday" abbr="Thursday" scope="col">Thu</th>
                    <th align="center" aria-label="Friday" abbr="Friday" scope="col">Fri</th>
                    <th align="center" aria-label="Saturday" abbr="Saturday" scope="col">Sat</th>
                </tr>
                <?php $currentDay = 1; ?>
                <tr>
                    <?php
                        // The variable $dayOfWeek is used to
                        // ensure that the calendar
                        // display consists of exactly 7 columns.
                        if ($dayOfWeek > 0) {
                            echo "<td class='ohter' colspan='".esc_attr($dayOfWeek)."'>&nbsp;</td>";
                        }
                        $month = str_pad($month, 2, "0", STR_PAD_LEFT);
                        while ($currentDay <= $numberDays) {
                            // Seventh column (Saturday) reached. Start a new row.
                            if ($dayOfWeek == 7) {
                                $dayOfWeek = 0;
                                echo "</tr><tr>";
                            }
                            $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
                            $date = "$year-$month-$currentDayRel";

                            $key = array_search($currentDay,array_column($eventDays, 'day'));
                            if( !empty($key) || $key===0 ){
                                ?>
                                <td class="day <?php echo esc_attr(' '.$eventDays[$key]['className']); ?>" rel="<?php echo esc_attr($date); ?>">
                                    <a href="<?php echo esc_attr($eventDays[$key]['url']); ?>" class="exist clsCalenderTooltip mini-tooltip"
                                       style="background-color:<?php echo esc_attr($eventDays[$key]['color']); ?>"
                                       data-toggle="tooltip" title="<?php echo esc_attr($eventDays[$key]['tooltip']); ?>">
                                        <?php echo esc_html($currentDay); ?>
                                    </a>
                                </td>
                                <?php
                            }else{
                                echo "<td class='day' rel='". esc_attr($date)."'>".esc_html($currentDay)."</td>";
                            }

                            // Increment counters
                            $currentDay++;
                            $dayOfWeek++;
                        }
                        // Complete the row of the last week in month, if necessary
                        if ($dayOfWeek != 7) {
                            $remainingDays = 7 - $dayOfWeek;
                            echo "<td class='ohter' colspan=" . esc_attr( $remainingDays ) , ">&nbsp;</td>";
                        }
                    ?>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}
/*
* Function used to get ce21 calendar events.
*/
add_action('wp_ajax_get_ce21_mini_calendar','get_ce21_mini_calendar');
add_action('wp_ajax_nopriv_get_ce21_mini_calendar','get_ce21_mini_calendar');
function get_ce21_mini_calendar()
{
    header('Content-type: application/json');
    $month = sanitize_text_field($_REQUEST['month']);
    $year  = sanitize_text_field($_REQUEST['year']);
    ob_start();
    ce21_getMiniCalendarHTML($month,$year);
    $res['content'] = ob_get_clean();
    $res['success'] = true;
    echo json_encode($res);
    exit;
}
/*Mini Calendar code ends*/
