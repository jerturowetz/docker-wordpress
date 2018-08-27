<?php
/**
 * WPML Plugin tweaks
 *
 * @link https://wpml.org/
 * @package _s
 */

if ( function_exists( 'icl_object_id' ) ) {

	/**
	 * Removes WPML Content Setup box from the bottom of posts and pages
	 *
	 * @return void
	 */
	function _s_wpml_disable_icl_metabox() {
		global $post;
		if ( isset( $post->post_type ) ) {
			remove_meta_box( 'icl_div_config', $post->post_type, 'normal' );
		}
	}
	add_action( 'admin_head', '_s_wpml_disable_icl_metabox', 99 );

	/**
	 * Disable Language Selector CSS & JS
	 *
	 * @link https://wpml.org/documentation/support/wpml-coding-api/#disabling-wpmls-css-and-js-files
	 * @return void
	 */
	function _s_wpml_disable_language_selector_css() {
		define( 'WPML_ENVIRONMENT', 'test' ); // turn off on production
		define( 'ICL_DONT_LOAD_NAVIGATION_CSS', true );
		define( 'ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true );
		define( 'ICL_DONT_LOAD_LANGUAGES_JS', true );
	}
	add_action( 'plugins_loaded', '_s_wpml_disable_language_selector_css' );

}
