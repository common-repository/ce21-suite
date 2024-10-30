<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.ce21.com
 * @since      1.0.0
 *
 * @package    Single_Sign_On_Ce21
 * @subpackage Single_Sign_On_Ce21/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Single_Sign_On_Ce21
 * @subpackage Single_Sign_On_Ce21/public
 * @author     CE21 <support@ce21.com>
 */
class Single_Sign_On_Ce21_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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
        $api_settings = get_ce21_ss_api_settings_data();
        $catalog_url = (!empty($api_settings)) ? $api_settings->CatalogURL : '';
        $css_date = date('d/m/YH');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/single-sign-on-ce21-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'ce21programslist-style', plugin_dir_url( __FILE__ ) . 'ce21programslist/css/stylesheet.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'core-main.min.css', plugin_dir_url( __FILE__ ) . 'css/fullcalendar-4.4.2/core/main.min.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'daygrid-main.min.css', plugin_dir_url( __FILE__ ) . 'css/fullcalendar-4.4.2/daygrid/main.min.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'ce21-custom-css-wp', $catalog_url.'/customcss/wp?t='.$css_date, array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/single-sign-on-ce21-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'core-main.min.js', plugin_dir_url( __FILE__ ) . 'js/fullcalendar-4.4.2/core/main.min.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( 'daygrid-main.min.js', plugin_dir_url( __FILE__ ) . 'js/fullcalendar-4.4.2/daygrid/main.min.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( 'interaction-main.min.js', plugin_dir_url( __FILE__ ) . 'js/fullcalendar-4.4.2/interaction/main.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'ppper', SINGLE_SIGN_ON_CE21__PLUGIN_URL . 'admin/js/popper.js', array( 'jquery' ));
		wp_enqueue_script( 'tltip', SINGLE_SIGN_ON_CE21__PLUGIN_URL . 'admin/js/tooltip.js', array( 'jquery' ));

	}

}
