<?php
/**
 * Yoast Premium SEO Plugin tweaks
 *
 * @link https://yoast.com/
 * @package _s
 */

if ( defined( 'WPSEO_VERSION' ) ) {

	/**
	 * Yoast SEO Disable Automatic Redirects for
	 * Posts And Pages
	 * Credit: Yoast Development Team
	 * Last Tested: May 09 2017 using Yoast SEO Premium 4.7.1 on WordPress 4.7.4
	 */
	add_filter( 'wpseo_premium_post_redirect_slug_change', '__return_true' );

	/**
	 * Yoast SEO Disable Automatic Redirects for
	 * Taxonomies (Category, Tags, Etc)
	 * Credit: Yoast Development Team
	 * Last Tested: May 09 2017 using Yoast SEO Premium 4.7.1 on WordPress 4.7.4
	 */
	add_filter( 'wpseo_premium_term_redirect_slug_change', '__return_true' );

	/**
	 * Sets the priority of the SEO meta box to 'low'
	 *
	 * @param string $priority
	 * @return void
	 */
	function _s_wpseo_lower_metabox_priority( string $priority = null ) {
		return 'low';
	}
	add_filter( 'wpseo_metabox_prio', '_s_wpseo_lower_metabox_priority' );

	/**
	 * Removes Yoast SEO post columns in admin
	 *
	 * @param array $columns
	 * @return void
	 */
	function _s_wpseo_remove_seo_columns_from_posts( array $columns ) {

		unset( $columns['wpseo-score'] );
		unset( $columns['wpseo-title'] );
		unset( $columns['wpseo-metadesc'] );
		unset( $columns['wpseo-focuskw'] );
		unset( $columns['wpseo-score-readability'] );

		return $columns;

	}
	add_filter( 'manage_edit-post_columns', '_s_wpseo_remove_seo_columns_from_posts' );
	add_filter( 'manage_edit-page_columns', '_s_wpseo_remove_seo_columns_from_posts' );
	add_filter( 'manage_edit-sp_solutions_columns', '_s_wpseo_remove_seo_columns_from_posts' );
	add_filter( 'manage_edit-sp_products_columns', '_s_wpseo_remove_seo_columns_from_posts' );

	/**
	 * Removes Yoast SEO taxonomy columns in admin
	 *
	 * @param array $columns
	 * @return void
	 */
	function _s_wpseo_remove_seo_columns_from_tax( array $columns ) {

		if ( ! isset( $_GET['taxonomy'] ) ) {
			return $columns;
		}

		if ( $posts === $columns['wpseo_score'] ) {
			unset( $columns['wpseo_score'] );
		}

		if ( $posts === $columns['wpseo_score_readability'] ) {
			unset( $columns['wpseo_score_readability'] );
		}

		return $columns;
	}
	add_filter( 'manage_edit-category_columns', '_s_wpseo_remove_seo_columns_from_tax' );
	add_filter( 'manage_edit-sp_solutions_cats_columns', '_s_wpseo_remove_seo_columns_from_tax' );
	add_filter( 'manage_edit-sp_products_cats_columns', '_s_wpseo_remove_seo_columns_from_tax' );
	add_filter( 'manage_edit-media_category_columns', '_s_wpseo_remove_seo_columns_from_tax' );
	add_filter( 'manage_edit-document_type_columns', '_s_wpseo_remove_seo_columns_from_tax' );
	add_filter( 'manage_edit-document_language_columns', '_s_wpseo_remove_seo_columns_from_tax' );

}
