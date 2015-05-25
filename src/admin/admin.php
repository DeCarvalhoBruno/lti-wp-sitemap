<?php namespace Lti\Sitemap;

use Lti\Sitemap\Helpers\Google_Helper;
use Lti\Sitemap\Helpers\Bing_Helper;
use Lti\Sitemap\Helpers\Google_Helper_Webmaster_Site;
use Lti\Sitemap\Helpers\ICanHelp;
use Lti\Sitemap\Plugin\Plugin_Settings;

/**
 * Deals with everything that happens in the admin screen
 *
 *
 * Class Admin
 * @package Lti\Sitemap
 */
class Admin {

	/**
	 * @var string Tracks page type so we can display error/warning messages
	 */
	private $page_type = 'edit';
	/**
	 * @var string Contains messages to be displayed after saves/resets
	 */
	private $message = '';
	/**
	 * @var string In case we forget our own name in the heat of the battle
	 */
	private $plugin_name;
	/**
	 * @var string Plugin version
	 */
	private $version;
	/**
	 * @var \Lti\Sitemap\Plugin\Plugin_Settings
	 */
	private $settings;
	/**
	 * @var string Helps defining what kind of settings to use (settings or postbox values)
	 */
	private $current_page = "options-general";
	/**
	 * @var \Lti\Sitemap\Helpers\Wordpress_Helper
	 */
	private $helper;

	/**
	 * @var Google_Helper
	 */
	private $google_connector;

	/**
	 * @var Bing_Helper
	 */
	private $bing_connector;
	/**
	 * @var bool
	 */
	private $can_send_curl_requests;
	private $google_error;


	/**
	 * @param $plugin_name
	 * @param $plugin_basename
	 * @param $version
	 * @param Plugin_Settings $settings
	 * @param $plugin_path
	 * @param ICanHelp $helper
	 */
	public function __construct(
		$plugin_name,
		$plugin_basename,
		$version,
		Plugin_Settings $settings,
		$plugin_path,
		ICanHelp $helper
	) {

		$this->plugin_name     = $plugin_name;
		$this->plugin_basename = $plugin_basename;
		$this->version         = $version;
		$this->admin_dir_url   = plugin_dir_url( __FILE__ );
		$this->admin_dir       = dirname( __FILE__ );
		$this->plugin_dir      = $plugin_path;
		$this->plugin_dir_url  = plugin_dir_url( $plugin_path . '/index.php' );
		$this->settings        = $settings;
		$this->helper          = $helper;

		$this->can_send_curl_requests = function_exists( 'curl_version' );
		if ( $this->can_send_curl_requests === true ) {
			$this->google_connector = new Google_Helper();
			$this->bing_connector   = new Bing_Helper( $this->helper->sitemap_url() );

			$access_token = $this->settings->get( 'google_access_token' );
			if ( ! is_null( $access_token ) && ! empty( $access_token ) ) {
				$this->google_connector->set_access_token( $access_token );

				if ( $this->google_connector->assess_token_validity() !== true ) {
					$this->settings->remove( 'google_access_token' );
				}
			}
		}
	}

	/**
	 * Adding our CSS stylesheet
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			$this->plugin_dir_url . 'assets/dist/css/lti_sitemap_admin.css',
			array(),
			$this->version,
			'all' );
	}

	/**
	 * Adding our JS
	 * Defining translated values for javascript to use
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			$this->plugin_dir_url . 'assets/dist/js/lti_sitemap_admin.js',
			array( 'jquery' ),
			$this->version,
			false );
	}

	/**
	 * Adding "Help" button to the admin screen
	 */
	public function admin_menu() {
		$page = add_options_page( lsmint( 'admin.menu_title' ), lsmint( 'admin.menu_item' ), 'manage_options',
			'lti-sitemap-options',
			array( $this, 'options_page' ) );
		add_action( 'load-' . $page, array( $this, 'wp_help_menu' ) );
	}

	/**
	 * Defining tabs for the help menu
	 *
	 * @see Admin::admin_menu
	 */
	public function wp_help_menu() {
		include $this->admin_dir . '/partials/help_menu.php';
		$screen = get_current_screen();
		$menu   = new \lti_sitemap_Help_Menu();
		$screen->add_help_tab( array(
			'id'      => 'general_hlp_welcome',
			'title'   => lsmint( 'general_hlp_welcome' ),
			'content' => $menu->welcome_tab()
		) );
		$screen->add_help_tab( array(
			'id'      => 'general_hlp_general',
			'title'   => lsmint( 'general_hlp_general' ),
			'content' => $menu->general_tab()
		) );
		$screen->set_help_sidebar(
			$menu->sidebar()
		);
	}

	/**
	 * Adds a LTI Sitemap button to the WP "Settings" menu item in the admin sidebar
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return mixed
	 */
	public function plugin_actions( $links, $file ) {
		if ( $file == 'lti-sitemap/lti-sitemap.php' && function_exists( "admin_url" ) ) {
			array_unshift( $links,
				'<a href="' . admin_url( 'options-general.php?page=lti-sitemap-options' ) . '">' . lsmint( 'general.settings' ) . '</a>' );
		}

		return $links;
	}

	/**
	 * User input validation
	 *
	 * @param array $post_variables
	 */
	public function validate_input( $post_variables, $update_type ) {
		if ( wp_verify_nonce( $post_variables['lti_sitemap_token'], 'lti_sitemap_options' ) !== false ) {
			unset( $post_variables['_wpnonce'], $post_variables['option_page'], $post_variables['_wp_http_referer'] );
			$google_access_token = $this->settings->get( 'google_access_token' );
			$this->settings      = $this->settings->save( $post_variables );

			/**
			 * We save values into a new settings object, and our google access token, when set, isn't a part of the form
			 * so we make sure it's saved if it existed before this form submission.
			 */
			if ( ! is_null( $google_access_token ) ) {
				$this->settings->set( 'google_access_token', $google_access_token );
			}

			$this->page_type = "lti_update";

			if ( method_exists( $this, $update_type ) ) {
				call_user_func( array( $this, $update_type ), $post_variables );
			}
			update_option( 'lti_sitemap_options', $this->settings );
		} else {
			$this->page_type = "lti_error";
			$this->message   = lsmint( "opt.msg.error_token" );
		}
	}

	private function google_auth( $post_variables ) {
		try {
			$this->settings->set( 'google_access_token',
				$this->google_connector->authenticate( $post_variables['google_auth_token'] ) );
			$this->message = lsmint( 'opt.msg.google_logged_in' );
		} catch ( \Google_Auth_Exception $e ) {
			$this->google_error = array(
				'error'           => lsmint( 'opt.err.google_auth_failure' ),
				'google_response' => $e->getMessage()
			);
			$this->settings->remove( 'google_auth_token' );
			$this->settings->remove( 'google_access_token' );
		}
	}

	private function google_delete( $post_variables ) {
		$this->google_connector->init_site_service('http://caprica.linguisticteam.org',
								'http://caprica.linguisticteam.org/sitemap.xml');
	}

	/**
	 * Renders the admin view
	 *
	 */
	public function options_page() {

		/**
		 * General > Bing > Send sitemap button
		 */
		$bing_url = filter_input( INPUT_GET, 'bing_url' );
		if ( ! is_null( $bing_url ) && wp_verify_nonce( filter_input( INPUT_GET, 'lti-sitemap-options' ),
				'bing_url_submission' )
		) {
			include $this->admin_dir . '/partials/bing_sitemap_submission.php';

			return;
		}

		$post_variables = $this->helper->filter_var_array( $_POST );
		$update_type    = '';
		/**
		 * Form submission handler
		 */
		switch ( true ) {
			case isset( $post_variables['lti_sitemap_update'] ):
				$update_type = "normal";
				break;
			case isset( $post_variables['lti_sitemap_google_auth'] ):
				$update_type = "google_auth";
				break;
			case isset( $post_variables['lti_sitemap_google_submit'] ):
				$update_type = "google_submit";
				break;
			case isset( $post_variables['lti_sitemap_google_delete'] ):
				$update_type = "google_delete";
				break;
			case isset( $post_variables['lti_sitemap_google_logout'] ):
				$update_type = "google_logout";
				break;
			/**
			 * Settings reset handler
			 */
			case isset( $post_variables['lti_sitemap_reset'] ):
				$this->settings = new Plugin_Settings();
				update_option( 'lti_sitemap_options', $this->settings );

				$this->page_type = "lti_reset";
				$this->message   = lsmint( 'opt.msg.reset' );
				break;
			default:
				$this->page_type = "lti_edit";
		}

		if ( isset( $post_variables['lti_sitemap_token'] ) && ! empty( $update_type ) ) {
			$this->validate_input( $post_variables, $update_type );
		}

		include $this->admin_dir . '/partials/options-page.php';
	}

	public function register_setting() {
		Activator::activate();
	}

	public function plugin_row_meta( $links, $file ) {
		if ( $file == $this->plugin_basename ) {
			$links[] = '<a href="http://dev.linguisticteam.org/lti-sitemap-help/" target="_blank">' . lsmint( 'admin.help' ) . '</a>';
			$links[] = '<a href="https://github.com/DeCarvalhoBruno/lti-wp-sitemaop" target="_blank">' . lsmint( 'admin.contribute' ) . '</a>';
		}

		return $links;
	}

	public function set_current_page( $page ) {
		$this->current_page = $page;
	}

	public function get_settings() {
		return $this->settings;
	}

	public function get_setting( $setting ) {
		return $this->settings->get( $setting );
	}

	public function get_page_type() {
		return $this->page_type;
	}

	public function get_message() {
		return $this->message;
	}

	public static function get_plugin_admin_url() {
		return admin_url( 'options-general.php?page=lti-sitemap-options' );
	}
}
