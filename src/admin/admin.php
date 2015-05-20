<?php namespace Lti\Sitemap;

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
	}

	/**
	 * Adding our CSS stylesheet
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			$this->plugin_dir_url . 'assets/dist/css/lti_sitemap_admin.css',
			array( ),
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
	 * @param $data
	 */
	public function validate_input( $data ) {
		unset( $data['_wpnonce'], $data['option_page'], $data['_wp_http_referer'] );
		$this->settings = $this->settings->save( $data );
		update_option( 'lti_sitemap_options', $this->settings);
	}

	/**
	 * Renders the admin view
	 *
	 */
	public function options_page() {
		$post_variables = $this->helper->filter_var_array( $_POST );

		if ( isset( $post_variables['lti_sitemap_update'] ) ) {
			if ( isset( $post_variables['lti_sitemap_token'] ) ) {
				if ( wp_verify_nonce( $post_variables['lti_sitemap_token'], 'lti_sitemap_options' ) !== false ) {
					$this->validate_input( $post_variables );
					$this->page_type = "lti_update";
					$this->message   = lsmint( 'opt.msg.updated' );

				} else {
					$this->page_type = "lti_error";
					$this->message   = lsmint( "opt.msg.error_token" );
				}
			}
		} elseif ( isset( $post_variables['lti_sitemap_reset'] ) ) {
			$this->settings = new Plugin_Settings();
			update_option( 'lti_sitemap_options', $this->settings );

			$this->page_type = "lti_reset";
			$this->message   = lsmint( 'opt.msg.reset' );
		} else {
			$this->page_type = "lti_edit";
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

	public function get_setting($setting){
		return $this->settings->get($setting);
	}

	public function get_page_type() {
		return $this->page_type;
	}

	public function get_message() {
		return $this->message;
	}
}
