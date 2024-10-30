<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.ce21.com
 * @since      1.0.0
 *
 * @package    Single_Sign_On_Ce21
 * @subpackage Single_Sign_On_Ce21/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Single_Sign_On_Ce21
 * @subpackage Single_Sign_On_Ce21/admin
 * @author     CE21 <support@ce21.com>
 */
class Single_Sign_On_Ce21_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Single_Sign_On_Ce21_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Single_Sign_On_Ce21_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/single-sign-on-ce21-admin.css', array(), $this->version, 'all' );

		if($hook == 'toplevel_page_single_sign_on_ce21') {
			
			wp_enqueue_style( 'bootstrap-5.2', SINGLE_SIGN_ON_CE21__PLUGIN_URL .'admin/datatable-bootstrap/bootstrap-5.2.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'dataTables.bootstrap5', SINGLE_SIGN_ON_CE21__PLUGIN_URL .'admin/datatable-bootstrap/dataTables.bootstrap5.min.css', array(), $this->version, 'all' );

		}
        if($hook == 'ce21_page_program_list_settings') {
            wp_enqueue_style( 'DT_bootstrap', plugin_dir_url( __FILE__ ) . 'css/new_bootstrap.css', array(), $this->version, 'all' );
            wp_enqueue_style( 'multiselect', plugin_dir_url( __FILE__ ) . 'css/jquery.multiselect.css', array(), $this->version, 'all' );
        }
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'calendar' ) {
			wp_enqueue_style( 'DT_bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'bootstrap', 'https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css', array(), $this->version, 'all' );
            wp_enqueue_style( 'jquery.datetimepicker.min.css', plugin_dir_url( __FILE__ ) . 'css/jquery.datetimepicker.min.css', array(), $this->version, 'all' );
        }

        if($hook == 'ce21_page_directory_settings') {
            wp_enqueue_style( 'new_bootstrap', plugin_dir_url( __FILE__ ) . 'css/new_bootstrap.css', array(), $this->version, 'all' );
        }

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Single_Sign_On_Ce21_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Single_Sign_On_Ce21_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		 wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/single-sign-on-ce21-admin.js', array( 'jquery' ), $this->version, false );
		 wp_enqueue_script( 'ce21-admin-dev', plugin_dir_url( __FILE__ ) . 'js/admin-dev.js', array( 'jquery' ), $this->version, false );

		if($hook == 'toplevel_page_single_sign_on_ce21') {
			wp_enqueue_script( 'bootstrap.min.js', SINGLE_SIGN_ON_CE21__PLUGIN_URL .'admin/datatable-bootstrap/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'jquery.dataTables.min.js', SINGLE_SIGN_ON_CE21__PLUGIN_URL .'admin/datatable-bootstrap/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'dataTables-bootstrap5.js', SINGLE_SIGN_ON_CE21__PLUGIN_URL .'admin/datatable-bootstrap/dataTables.bootstrap5.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'custome-dev.js', plugin_dir_url( __FILE__ ) . 'js/custome-dev.js', array( 'jquery' ), $this->version, false );
			
        }
        if($hook == 'ce21_page_program_list_settings') {
            wp_enqueue_script( 'multiselect.js', plugin_dir_url( __FILE__ ) . 'js/jquery.multiselect.js', array( 'jquery' ), $this->version, false );
        }
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'calendar' ) {
			wp_enqueue_script( 'bootstrap.min.js', SINGLE_SIGN_ON_CE21__PLUGIN_URL .'admin/datatable-bootstrap/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script( 'jquery.dataTables.min.js', SINGLE_SIGN_ON_CE21__PLUGIN_URL .'admin/datatable-bootstrap/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script( 'dataTables-bootstrap5.js', SINGLE_SIGN_ON_CE21__PLUGIN_URL .'admin/datatable-bootstrap/dataTables.bootstrap5.min.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script( 'jquery.datetimepicker.full.min.js', plugin_dir_url( __FILE__ ) . 'js/jquery.datetimepicker.full.min.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script( 'ce21-single-sign-on-calendar.js', plugin_dir_url( __FILE__ ) . 'js/ce21-single-sign-on-calendar.js', array( 'jquery' ), $this->version, false );
        }

        if($hook == 'ce21_page_directory_settings') {
            wp_enqueue_script( 'ce21-common-functions.js', plugin_dir_url( __FILE__ ) . 'js/common-functions.js', array( 'jquery' ), $this->version, false );
        }

	}
}
