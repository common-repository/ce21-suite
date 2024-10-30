<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.ce21.com
 * @since      1.0.0
 *
 * @package    Single_Sign_On_Ce21
 * @subpackage Single_Sign_On_Ce21/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Single_Sign_On_Ce21
 * @subpackage Single_Sign_On_Ce21/includes
 * @author     CE21 <support@ce21.com>
 */
class Single_Sign_On_Ce21 {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Single_Sign_On_Ce21_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SINGLE_SIGN_ON_CE21_VERSION' ) ) {
			$this->version = SINGLE_SIGN_ON_CE21_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'single-sign-on-ce21';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		add_action( 'admin_menu', [ $this, 'ce21_plugin_menu' ] );
		
	}


	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Single_Sign_On_Ce21_Loader. Orchestrates the hooks of the plugin.
	 * - Single_Sign_On_Ce21_i18n. Defines internationalization functionality.
	 * - Single_Sign_On_Ce21_Admin. Defines all hooks for the admin area.
	 * - Single_Sign_On_Ce21_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-single-sign-on-ce21-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-single-sign-on-ce21-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-single-sign-on-ce21-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-single-sign-on-ce21-public.php';

		$this->loader = new Single_Sign_On_Ce21_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Single_Sign_On_Ce21_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Single_Sign_On_Ce21_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Single_Sign_On_Ce21_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Single_Sign_On_Ce21_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Single_Sign_On_Ce21_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Create menu option and page for the CE21 Suite option 
	 */
	public function ce21_plugin_menu() {
		$image_url = SINGLE_SIGN_ON_CE21__PLUGIN_URL . "admin/images/logo-o.png";
		$hook = add_menu_page(
			'CE21 Suite',
			'CE21',
			'manage_options',
			'single_sign_on_ce21',
			[ $this, 'ce21_plugin_settings_page' ],
			$image_url
		);

		$hook .= add_submenu_page(
            'single_sign_on_ce21',
            'CE21 Suite',
            'Group Settings',
            'manage_options',
            'single_sign_on_ce21'
        );

        $hook .= add_submenu_page(
            'single_sign_on_ce21',
            'CE21 API',
            'API Settings',
            'manage_options',
            'api_settings',
            [ $this, 'get_ce21_single_sign_on_api_settings_page' ]
        );

        $hook .= add_submenu_page(
            'single_sign_on_ce21',
            'CE21 Program List',
            'Program List Settings',
            'manage_options',
            'program_list_settings',
            [ $this, 'get_ce21_single_sign_on_program_list_settings_page' ]
        );

        $hook .= add_submenu_page(
            'single_sign_on_ce21',
            'CE21 Calender',
            'Calendar',
            'manage_options',
            'calendar',
            [ $this, 'get_ce21_single_sign_on_calendar_event_list_page' ]
        );

        $hook .= add_submenu_page(
            'single_sign_on_ce21',
            'CE21 Directory',
            'Directory',
            'manage_options',
            'directory_settings',
            [ $this, 'get_ce21_single_sign_on_membership_page' ]
        );
	}
	
	
	/**
	 * Save the catalog data and insert member goup data
	 * @param string catalog URL
	 * @return array|\WP_Error 
	 */

	public function ce21_catalog_data_save($catalogurl) {
		try {
			global $wpdb;
	      	$api_url = $catalogurl."/WPAuthorize?token=dsadknnskldj3k2l4jkl24j23894798ehf89y43274uhg";
			$result = wp_remote_get($api_url);
			if (is_wp_error($result)) {
			    throw new Exception($result['body'], 1);
			}
			
		    $data 	 = json_decode($result['body'], true);
		    $message = "";
			$messageClassName = "";

		    if(! empty($data) && $data['success'] == true) {
	    		/*
				* This code for update catalog url data in option table
	    		*/
	    		update_option( 'authorizeURI_ce21', $data['data']['authorizeURI'] );
	    		update_option( 'backendURI_ce21', $data['data']['backendURI'] );
	    		update_option( 'tenantId_ce21', $data['data']['tenantId'] );
	    		update_option( 'baseAPIURI_ce21', $data['data']['baseAPIURI'] );

	    		/*
				* This code for the update member list
	    		*/
	    		$authorizeURI_ce21 = get_option( 'authorizeURI_ce21' );
	    		$get_Memberships_APi_URL_ce21 = $authorizeURI_ce21.'/WPAuthorize/Membership/List?includeHiddenGroup=true';
				$result = wp_remote_get($get_Memberships_APi_URL_ce21);
				if (is_wp_error($result)) {
					throw new Exception($result['body'], 1);
				}
				$Memberships_Data = json_decode($result['body'], true);
				$values = "";

				if (!empty($Memberships_Data['data']) && $Memberships_Data['success'] == true) {
					$table_name_ce21 = $wpdb->prefix . 'membership_types_ce21';
					$wpdb->query("TRUNCATE TABLE $table_name_ce21");
					foreach ($Memberships_Data['data'] as $key => $value) {	
						$tId              = $value['tenantId'];
						$membershipTypeId = $value['membershipTypeId'];
						$membershipName   = $value['name'];
						$member_name      = str_replace("'","/",$membershipName);
						$insertdata = $wpdb->insert(
							$table_name_ce21,
							array(
								'TenantId' => $tId,
								'membershipTypeId' => $membershipTypeId,
								'membershipName' => $member_name,
							)
						);
					}
                    if ($insertdata) {
						$message = 'Settings saved.';
						$messageClassName = 'updated';
					} else {
						$message = $wpdb->last_error;
						$messageClassName = 'error';
						throw new Exception($message, 1);
					}
				} else {
					$message = 'No groups found.';
					$messageClassName = 'error';
					//throw new Exception($message, 1);
				}	    	
		    } else {		    	
		    	$message = 'Sorry, Something went wrong, Please try again. Error comes on Link Catalog';
				$messageClassName = 'error';
				throw new Exception('Sorry, Something went wrong in API.', 1);
		    }
		} catch (Exception $e) {
	    	$errorMsg = ' Error on line '.$e->getLine().' in '.$e->getFile() . ' : <b>'.$e->getMessage().'</b>';			
			ce21_error_log_api($errorMsg);
		}

		$msgsArr = array( 'message' => $message,  'messageClassName' => $messageClassName );
		return $msgsArr;
	}

	/**
	 * Listing and Synchronize member group data
	 * @return array| WP_Error
	 */
	public function ce21_get_member_plans() {
		
		try{
			global $wpdb;
	    	$authorizeURI_ce21 = get_option( 'authorizeURI_ce21' );
			$get_Memberships_APi_URL_ce21 = $authorizeURI_ce21.'/WPAuthorize/Membership/List?includeHiddenGroup=true';

			$result = wp_remote_get($get_Memberships_APi_URL_ce21);
			if (is_wp_error($result)) {
				throw new Exception($result['body'], 1);
			}
			$Memberships_Data = json_decode($result['body'], true);
			$values = "";
			$message  = "";
		    $messageClassName = "";

			if (!empty($Memberships_Data['data']) && $Memberships_Data['success'] ) {
				$table_name_ce21 = $wpdb->prefix . 'membership_types_ce21';
				$wpdb->query("TRUNCATE TABLE $table_name_ce21");
				foreach ($Memberships_Data['data'] as $key => $value) {	
					$tId              = $value['tenantId'];
					$membershipTypeId = $value['membershipTypeId'];
					$membershipName   = $value['name'];
					$member_name      = str_replace("'","/",$membershipName);
					$insertdata = $wpdb->insert(
						$table_name_ce21,
						array(
							'TenantId' => $tId,
							'membershipTypeId' => $membershipTypeId,
							'membershipName' => $member_name,
						)
					);
				}

				if ( $insertdata ) {
				 	$message = 'Settings saved.';
					$messageClassName = 'updated';

				} else {
				  	$message = $wpdb->last_error;
					$messageClassName = 'error';
					throw new Exception($message, 1);
				}
			} else {
                $message = 'No groups found.';
				$messageClassName = 'error';
				//throw new Exception('Sorry, Something went wrong in API.', 1);
			}
	  	} catch (Exception $e) {
	    	$errorMsg = ' Error on line '.$e->getLine().' in '.$e->getFile() . ' : <b>'.$e->getMessage().'</b>';			
			ce21_error_log_api($errorMsg);
		}

		$msgsArr = array( 'message' => $message,  'messageClassName' => $messageClassName );
		return $msgsArr;
	}

	/**
	 * Plugin CE21 Suite settings page
	 * Display view page
	 */
	public function ce21_plugin_settings_page() {
		
		/**
		 * Catalog url save
		 */
		if (isset($_POST["submit"]) && !empty($_POST["catalog_url_cf21"]) ) {
        	$catalog_url_ce21 = sanitize_url($_POST["catalog_url_cf21"]);
        	$messagesArr 	  = $this->ce21_catalog_data_save( $catalog_url_ce21 );
        }

        /**
		 * Get Member data using api
		 * Member data insert in database
         */	        
        if ( isset($_POST["get_member_plans"]) && !empty($_POST["get_member_plans"]) ) {
        	$messagesArr  = $this->ce21_get_member_plans();
        }
	    ?>

		<div class="wrap">
			<h2>CE21 Suite Settings</h2>
			<?php
				if(!empty($messagesArr['message']) && !empty($messagesArr['messageClassName']))
				{ 
					echo '<div id="setting-error-settings_updated" class="'.$messagesArr['messageClassName'].' settings-error notice is-dismissible"> 
							<p><strong> '.$messagesArr['message'].'</strong></p>
							<button type="button" class="notice-dismiss">
								<span class="screen-reader-text">Dismiss this notice.</span>
							</button>
						</div>';
				} 
			?>
			<div id="poststuff" class="single_sign_on_ce21">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post" onsubmit="return catalog_form_validate_ce21(this);">
								<table class="form-table">
									<tbody>
										<tr>
											<th scope="row"><label for="catalog_url_cf21">Catalog URL</label></th>
											<td>
												<input name="catalog_url_cf21" type="text" id="catalog_url_cf21" value="<?php if(!empty( get_option( 'authorizeURI_ce21' ))){ echo  get_option( 'authorizeURI_ce21' );}?>" placeholder="https://siteurl.com" class="regular-text" required>
												<input type="hidden" name="catalog_old_url_cf21" id="catalog_old_url_cf21" value="<?php if(!empty( get_option( 'authorizeURI_ce21' ))){ echo  get_option( 'authorizeURI_ce21' );}?>">
												<p class="description">Please enter the Catalog url with https or http </p>
											</td>
										</tr>
									</tbody>
								</table>
								<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Link Catalog" ></p>
							</form>
						</div>
					</div>
				</div>
			</div>
			<br class="clear">

			<div class="member-synchronize-ce21">
				<h3>CE21 Group</h3>
			</div>
			<br class="clear">

			<div class="membership_plans_list">
				<div class="accordion" id="accordionExample">
					<div class="accordion-item">
						<div class="member-palan-listing-title-ce21">
							<h4 class="panel-title accordion-header" id="headingOne">
								<a class="accordion-toggle accordion-button " data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
								<span class="glyphicon glyphicon-minus"></span>
								Groups List
								</a>
							</h4>
							<div class="member-synchronize-ce21-form">
								<form name="synchronize-member_ce21"  method="post">
									<input type="submit" name="get_member_plans" id="get_member_plans" class="button button-primary" value="Synchronize" >
								</form>
							</div>
						</div>
						<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
							<div class="accordion-body">
								<table id="member_plans_listing_ce21" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" >
									<?php
									try {

										global $wpdb;
										$table_name_ce21 = $wpdb->prefix . 'membership_types_ce21';
										$membership_plans_data_ce21 = $wpdb->get_results("SELECT * FROM ".$table_name_ce21." ORDER BY id ASC");

										if ($wpdb->last_error) {
											throw new Exception($wpdb->last_error, 1);
										}
									} catch (Exception $e) {
										$errorMsg = ' Error on line '.$e->getLine().' in '.$e->getFile() . ' : <b>'.$e->getMessage().'</b>';			
										ce21_error_log_api($errorMsg);
									}
									?>
									<thead>
										<tr>
											<th align="center" scope="col">Membership Name</th>
										</tr>
									</thead>
									<tbody>
										<?php 
										if( ! empty($membership_plans_data_ce21)) {
											foreach ($membership_plans_data_ce21 as $data_ce21) { 
											?> 
												<tr> <td align="" ><?php echo esc_html($data_ce21->membershipName); ?></td> </tr>
											<?php 
											}
										} else { ?>							        	
											<tr> <td align="" ><?php echo "No records found." ?></td> </tr>
										<?php  } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}


	/*
	 * Function used to get ce21 single sign on api settings_page.
	 * */
	public function get_ce21_single_sign_on_api_settings_page()
    {
		include( SINGLE_SIGN_ON_CE21__PLUGIN_DIR . 'admin/partials/ce21-single-sign-on-api-settings.php');
    }

    /*
	 * Function used to get ce21 single sign on program list settings_page.
	 * */
    public function get_ce21_single_sign_on_program_list_settings_page()
    {
		include( SINGLE_SIGN_ON_CE21__PLUGIN_DIR . 'programs/ce21-programs-settings-page.php');
    }

    /*
	 * Function used to get ce21 single sign on calendar-event-list page.
	 * */
    public function get_ce21_single_sign_on_calendar_event_list_page()
    {
		include( SINGLE_SIGN_ON_CE21__PLUGIN_DIR . 'admin/partials/ce21-single-sign-on-calendar-event-list.php');
    }
    /*
	 * Function used to get ce21 single sign on membership data.
	 * */
    public function get_ce21_single_sign_on_membership_page()
    {
		include( SINGLE_SIGN_ON_CE21__PLUGIN_DIR . 'membership/ce21-membership-page.php');
    }
}

