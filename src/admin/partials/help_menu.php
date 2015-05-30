<?php

/**
 * Appears when we click on the help button. (Top-right of the admin screen)
 *
 * Class Lti_Sitemap_Help_Menu
 *
 * @see \Lti\Sitemap\Admin::wp_help_menu
 */
class Lti_Sitemap_Help_Menu {
	public function __construct() {

	}

	public function welcome_tab() {
		return sprintf('<p>%s</p><p><strong>%s</strong></p>',lsmint( 'general_hlp_welcome_1' ),lsmint( 'general_hlp_welcome_2' ));
	}

	public function general_tab() {
		return '<p>' . lsmint( 'general_hlp_general_1' ) . '</p>';
	}

	public function frontpage_tab() {
		return '<p>' . lsmint( 'general_hlp_frontpage_1' ) . '</p>';
	}

	public function social_tab() {
		return '<p>' . lsmint( 'general_hlp_social_1' ) . '</p>';
	}

	public function sidebar() {
		return '<p><strong>' . lsmint( 'general_hlp_about_us' ) . '</strong></p>' .
		       '<p><a href="http://dev.linguisticteam.org/lti-seo-help/" target="_blank">' . lsmint( 'general_hlp_dev_blog' ) . '</a></p>' .
		       '<p><strong>' . lsmint( 'general_hlp_contribute' ) . '</strong></p>' .
		       '<p><a href="https://github.com/DeCarvalhoBruno/lti-wp-seo" target="_blank">' . lsmint( 'general_hlp_github' ) . '</a></p>';
	}
}