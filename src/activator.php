<?php namespace Lti\Sitemap;

/**
 * Fired during plugin activation
 */

class Activator {

	public static function activate() {
		add_filter('rewrite_rules_array', array(__CLASS__, 'rewrite_rules_array'), 1, 1);
		/**
		 * @var \WP_Rewrite $wp_rewrite
		 */
		global $wp_rewrite;
		$wp_rewrite->flush_rules(false);

//		$stored_options = get_option("lti_sitemap_options");
//		if (empty($stored_options)||$stored_options===false) {
//			$options = Plugin_Settings::get_defaults();
//			update_option("lti_sitemap_options", $options);
//		}
	}

	/**
	 * @param array $rewriteRules
	 *
	 * @see \WP_Rewrite::rewrite_rules
	 * @return array
	 */
	public static function rewrite_rules_array($rewriteRules) {
		return array_merge(array(
			'sitemap(-+([a-zA-Z0-9_-]+))?\.xml$' => 'index.php?lti_sitemap=params=$matches[2]',
		),$rewriteRules);
	}

}
