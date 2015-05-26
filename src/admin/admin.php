<?php namespace Lti\Sitemap;

use Lti\Sitemap\Helpers\Google_Helper;
use Lti\Sitemap\Helpers\Bing_Helper;
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
	private $current_page = "admin";
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
		if ( ! class_exists( '\Lti\Seo\LTI_SEO' ) ) {
			$page = add_menu_page( 'LTI', 'LTI', 'manage_options', 'lti-sitemap-options',
				array( $this, 'options_page' ),
				'data:image/svg+xml;base64, PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMb2dvIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDQwMCA0MDAiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDQwMCA0MDAiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHBhdGggaWQ9IlBhcnJvdCIgZmlsbD0iI0YxRjFGMSIgZD0iTTI1Ni44LDE0Ny43Yy0zLjQtNC45LTEwLTYuMS0xNS0yLjdjLTQuOSwzLjQtNiwxMC0yLjcsMTVjMy40LDQuOSwxMCw2LDE1LDIuNw0KCUMyNTksMTU5LjIsMjYwLjIsMTUyLjYsMjU2LjgsMTQ3Ljd6IE0yNTYuOCwxNDcuN2MtMy40LTQuOS0xMC02LjEtMTUtMi43Yy00LjksMy40LTYsMTAtMi43LDE1YzMuNCw0LjksMTAsNiwxNSwyLjcNCglDMjU5LDE1OS4yLDI2MC4yLDE1Mi42LDI1Ni44LDE0Ny43eiBNMjU2LjgsMTQ3LjdjLTMuNC00LjktMTAtNi4xLTE1LTIuN2MtNC45LDMuNC02LDEwLTIuNywxNWMzLjQsNC45LDEwLDYsMTUsMi43DQoJQzI1OSwxNTkuMiwyNjAuMiwxNTIuNiwyNTYuOCwxNDcuN3ogTTI4MiwxOTguMmMtNi42LDE1LjEtMTIuNSwyOC40LTE5LjQsNDRjMjEuOC0wLjQsMzkuMi0zLjYsNTUuMi0xOC43DQoJQzMwNC4xLDIxMy44LDI5My40LDIwNi4yLDI4MiwxOTguMnogTTMxMS41LDEzMC41Yy0zLjksNy44LTI0LjIsNTctMjQuMiw1N3MxMS4zLDQuNywyMS42LDljMjEsOC44LDIzLjQsMzEuMSwxMiw2MS41DQoJQzM5NC44LDIwNi43LDM2OS4zLDE1MS40LDMxMS41LDEzMC41eiBNMzExLjUsMTMwLjVjLTMuOSw3LjgtMjQuMiw1Ny0yNC4yLDU3czExLjMsNC43LDIxLjYsOWMyMSw4LjgsMjMuNCwzMS4xLDEyLDYxLjUNCglDMzk0LjgsMjA2LjcsMzY5LjMsMTUxLjQsMzExLjUsMTMwLjV6IE0yNjIuNywyNDIuMmMyMS44LTAuNCwzOS4yLTMuNiw1NS4yLTE4LjdjLTEzLjgtOS43LTI0LjUtMTcuMy0zNS45LTI1LjMNCglDMjc1LjQsMjEzLjMsMjY5LjUsMjI2LjYsMjYyLjcsMjQyLjJ6IE0yNDEuOSwxNDVjLTQuOSwzLjQtNiwxMC0yLjcsMTVjMy40LDQuOSwxMCw2LDE1LDIuN2M0LjgtMy40LDYtMTAsMi43LTE1DQoJQzI1My40LDE0Mi44LDI0Ni44LDE0MS41LDI0MS45LDE0NXogTTMxMS41LDEzMC41Yy0zLjksNy44LTI0LjIsNTctMjQuMiw1N3MxMS4zLDQuNywyMS42LDljMjEsOC44LDIzLjQsMzEuMSwxMiw2MS41DQoJQzM5NC44LDIwNi43LDM2OS4zLDE1MS40LDMxMS41LDEzMC41eiBNMjYyLjcsMjQyLjJjMjEuOC0wLjQsMzkuMi0zLjYsNTUuMi0xOC43Yy0xMy44LTkuNy0yNC41LTE3LjMtMzUuOS0yNS4zDQoJQzI3NS40LDIxMy4zLDI2OS41LDIyNi42LDI2Mi43LDI0Mi4yeiBNMjQxLjksMTQ1Yy00LjksMy40LTYsMTAtMi43LDE1YzMuNCw0LjksMTAsNiwxNSwyLjdjNC44LTMuNCw2LTEwLDIuNy0xNQ0KCUMyNTMuNCwxNDIuOCwyNDYuOCwxNDEuNSwyNDEuOSwxNDV6IE0yNTYuOCwxNDcuN2MtMy40LTQuOS0xMC02LjEtMTUtMi43Yy00LjksMy40LTYsMTAtMi43LDE1YzMuNCw0LjksMTAsNiwxNSwyLjcNCglDMjU5LDE1OS4yLDI2MC4yLDE1Mi42LDI1Ni44LDE0Ny43eiBNMjgyLDE5OC4yYy02LjYsMTUuMS0xMi41LDI4LjQtMTkuNCw0NGMyMS44LTAuNCwzOS4yLTMuNiw1NS4yLTE4LjcNCglDMzA0LjEsMjEzLjgsMjkzLjQsMjA2LjIsMjgyLDE5OC4yeiBNMzExLjUsMTMwLjVjLTMuOSw3LjgtMjQuMiw1Ny0yNC4yLDU3czExLjMsNC43LDIxLjYsOWMyMSw4LjgsMjMuNCwzMS4xLDEyLDYxLjUNCglDMzk0LjgsMjA2LjcsMzY5LjMsMTUxLjQsMzExLjUsMTMwLjV6IE0zMTEuNSwxMzAuNWMtMy45LDcuOC0yNC4yLDU3LTI0LjIsNTdzMTEuMyw0LjcsMjEuNiw5YzIxLDguOCwyMy40LDMxLjEsMTIsNjEuNQ0KCUMzOTQuOCwyMDYuNywzNjkuMywxNTEuNCwzMTEuNSwxMzAuNXogTTI4MiwxOTguMmMtNi42LDE1LjEtMTIuNSwyOC40LTE5LjQsNDRjMjEuOC0wLjQsMzkuMi0zLjYsNTUuMi0xOC43DQoJQzMwNC4xLDIxMy44LDI5My40LDIwNi4yLDI4MiwxOTguMnogTTI1Ni44LDE0Ny43Yy0zLjQtNC45LTEwLTYuMS0xNS0yLjdjLTQuOSwzLjQtNiwxMC0yLjcsMTVjMy40LDQuOSwxMCw2LDE1LDIuNw0KCUMyNTksMTU5LjIsMjYwLjIsMTUyLjYsMjU2LjgsMTQ3Ljd6IE0yNTYuOCwxNDcuN2MtMy40LTQuOS0xMC02LjEtMTUtMi43Yy00LjksMy40LTYsMTAtMi43LDE1YzMuNCw0LjksMTAsNiwxNSwyLjcNCglDMjU5LDE1OS4yLDI2MC4yLDE1Mi42LDI1Ni44LDE0Ny43eiBNMjU2LjgsMTQ3LjdjLTMuNC00LjktMTAtNi4xLTE1LTIuN2MtNC45LDMuNC02LDEwLTIuNywxNWMzLjQsNC45LDEwLDYsMTUsMi43DQoJQzI1OSwxNTkuMiwyNjAuMiwxNTIuNiwyNTYuOCwxNDcuN3ogTTIwMCwwQzg5LjcsMCwwLDg5LjcsMCwyMDBzODkuNywyMDAsMjAwLDIwMHMyMDAtODkuNywyMDAtMjAwUzMxMC4zLDAsMjAwLDB6IE0yMTAuNywxNDIuOA0KCWwxMi41LTMuMmwxNy42LTQuM2w0LjYtMS4yYzctMC44LDE0LjQsMi4xLDE4LjcsOC4zYzYuNCw5LDQuMiwyMS41LTQuNywyNy42Yy03LjQsNS4yLTE3LDQuNi0yMy43LTAuN2wtNC4xLTQuNWwtMTEuOC0xMi43DQoJTDIxMC43LDE0Mi44eiBNMzAuMSwxOTUuOEMzMi4zLDEwNCwxMDcuNywzMCwyMDAsMzBjOTMuNywwLDE3MCw3Ni4zLDE3MCwxNzBjMCw4Ni02NC4yLDE1Ny4zLTE0Ny4yLDE2OC41DQoJYzEuMi0zNC45LTIuNS03NS4yLDE3LjUtMTA4LjNjMy4yLTUuMyw2LjMtMTAuOCw5LjMtMTYuMWMxOS4zLTM0LjYsMzQuMi02OS44LDUxLjEtMTA3LjNjNS4zLTEyLDQtMTcuMS01LjEtMjIuNw0KCWMxLjktMTYtOC44LTMwLjktMjEuMS0zOS43Yy0yMC42LTE0LjctNDAuMi0xMi43LTY0LjMtMTQuNmMxNC44LDEuNiwyNC43LDYuOCwzNS43LDE3LjJzOS4zLDExLjUsMTEsMjIuMw0KCWMtMy40LTAuNy02LjctMS4yLTEwLjEtMS42Yy0yLjgtMTcuMS0xOS4xLTI4LjEtMzQuNy0zMi4xYy0yNC40LTYuMy00MiwyLjYtNjUuMiw5LjRjMTQuMy0zLjgsMjUuNS0yLjYsMzkuNSwzLjINCgljMTQuNyw2LDEyLjgsNy41LDE5LDE4LjJjMC41LDAuOCwwLjgsMS40LDEuMiwyLjFjLTIuNywwLjQtNS4zLDAuOS04LDEuNGMtOC0xNC45LTI2LjQtMjAuNC00Mi4xLTE5LjYNCgljLTI1LjEsMS4yLTM5LjQsMTQuOS01OS41LDI4LjJjMTIuNi03LjgsMjMuNi05LjksMzguNy04LjVjMTUuOSwxLjUsMTQuNCwzLjUsMjMuNSwxMS44YzAuMywwLjMsMC42LDAuNiwwLjksMC45DQoJYy0yLjYsMS4yLTUuMiwyLjUtNy44LDMuOGMtMTEuOC0xMi0zMS4xLTEyLjItNDYtNi45Yy0yMy44LDguMy0zMy41LDI1LjQtNDksNDRjOS44LTExLjEsMTkuOC0xNi4zLDM0LjUtMTkuMg0KCWMxNS42LTMuMSwxNC44LTAuOCwyNS45LDQuNmMwLjMsMC4yLDAuNywwLjMsMSwwLjVjLTIuOCwyLjQtNS41LDUtOC4xLDcuNmMtMTMuOC04LTMxLjYtNC40LTQ0LjYsMy4xDQoJQzQ2LjYsMTYxLjUsMzkuNSwxNzcuNSwzMC4xLDE5NS44YzAsMC40LDAsMC45LDAsMS4zYzYuNy05LjIsMTQuOC0xNC45LDI2LjQtMTkuNmMxNC44LTYsMTQuNC0zLjYsMjYuMy0wLjRjMS42LDAuNCwyLjksMC44LDQsMS4xDQoJYy0xLjMsMi4zLTIuNiw0LjYtMy44LDdjLTMuNCw2LjgtNi4yLDE0LTguNCwyMS40Yy0xLjUsNC45LTMuMyw5LjctNS4yLDE0LjRjLTguNSwyMC41LTIwLDM4LjItMjEuMyw1NS4yDQoJYy0xMS41LTIyLjktMTgtNDguOC0xOC03Ni4yYzAtMSwwLTEuOSwwLTIuOSIvPg0KPC9zdmc+' );
		} else {
			$page = add_submenu_page( 'lti-seo-options', lsmint( 'admin.menu_title' ), lsmint( 'admin.menu_item' ),
				'manage_options', 'lti-sitemap-options', array( $this, 'options_page' ) );
		}
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
	 * Adds a LTI Sitemap button to the admin sidebar
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return mixed
	 */
	public function plugin_actions( $links, $file ) {
		if ( $file == 'lti-sitemap/lti-sitemap.php' && function_exists( "admin_url" ) ) {
			array_unshift( $links,
				'<a href="' . admin_url( 'admin.php?page=lti-sitemap-options' ) . '">' . lsmint( 'general.settings' ) . '</a>' );
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
				$this->google_connector->init_service( 'http://dev.linguisticteam.org',
					'http://dev.linguisticteam.org/sitemap.xml' );
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
			$this->settings->remove( 'google_access_token' );
		}
	}

	private function google_submit() {
		$webmaster = $this->google_connector->get_service();
		$webmaster->submit_sitemap();
		$this->message = lsmint( 'google.msg.submit' );
	}

	private function google_resubmit() {
		$webmaster = $this->google_connector->get_service();
		$webmaster->submit_sitemap();
		$this->message = lsmint( 'google.msg.resubmit' );
	}

	private function google_delete() {
		$webmaster = $this->google_connector->get_service();
		$webmaster->delete_sitemap();
		$this->message = lsmint( 'google.msg.delete' );
	}

	private function google_logout() {
		$this->settings->remove( 'google_access_token' );
		$this->message = lsmint( 'google.msg.logout' );
		$this->google_connector->revoke_token();
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
			case isset( $post_variables['lti_sitemap_google_resubmit'] ):
				$update_type = "google_resubmit";
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

	public static function get_admin_slug() {
		return admin_url( 'admin.php?page=lti-sitemap-options' );
	}
}
