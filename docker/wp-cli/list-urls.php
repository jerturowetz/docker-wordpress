<?php



/**
 * Gets archive pages for all post types in all langs
 *
 * @return void
 */
function wpcli_get_public_archive_pages() {

	global $sitepress;
	$output = [];

	$post_types = get_post_types( [ 'public' => true ], 'names' );
	$languages  = icl_get_languages( 'skip_missing=0&orderby=code' );

	foreach ( $post_types as $post_type ) {

		foreach ( array_keys( $languages ) as $lang ) {

			$sitepress->switch_lang( $lang, true );
			$archive_url = get_post_type_archive_link( $post_type );

			if ( $archive_url ) {
				$output[] = [
					'title' => $post_type . '-archive-' . $lang,
					'url'   => wp_parse_url( $archive_url, PHP_URL_PATH ),
				];
			}
		}
	}

	$sitepress->switch_lang( $sitepress->get_default_language(), true );

	return $output;

}



/**
 * Gets the post permalink for any post type
 *
 * @param object $post
 * @return $permalink
 */
function wpcli_get_post_permalink( object $post = null ) {

	switch ( $post->post_type ) {
		case 'revision':
		case 'nav_menu_item':
			break;
		case 'page':
			$permalink = get_page_link( $post->ID );
			break;
		case 'post':
			$permalink = get_permalink( $post->ID );
			break;
		case 'attachment':
			$permalink = get_attachment_link( $post->ID );
			break;
		default:
			$permalink = get_post_permalink( $post->ID );
			break;
	}

	return $permalink;

}



/**
 * Gets all published posts in all languages
 *
 * @return void
 */
function wpcli_get_published_posts() {

	// Note: attachment pages will not show up as their publish status isn't like other posts
	$posts  = new WP_Query( 'post_type=any&posts_per_page=-1&post_status=publish&suppress_filters=true' );
	$posts  = $posts->posts;
	$output = [];

	foreach ( $posts as $post ) {

		$permalink = wpcli_get_post_permalink( $post );
		$wpml_data = apply_filters( 'wpml_post_language_details', null, $post->ID );
		$lang_code = $wpml_data['language_code'];

		$output[] = [
			'title' => $post->post_name . '-' . $lang_code,
			'url'   => wp_parse_url( $permalink, PHP_URL_PATH ),
		];

	}

	return $output;

}



/**
 * Gets all publshed terms
 *
 * @return void
 */
function wpcli_get_published_terms() {

	global $sitepress;
	$output = [];

	$args       = [ 'public' => true ];
	$taxonomies = get_taxonomies( $args, 'names' );
	$langs      = icl_get_languages( 'skip_missing=0&orderby=code' );

	foreach ( $taxonomies as $taxonomy ) {

		$exceptions = [ 'post_format', 'media_category', 'document_type', 'document_language' ];
		if ( ! in_array( $taxonomy, $exceptions, true ) ) {

			foreach ( array_keys( $langs ) as $lang ) {

				$sitepress->switch_lang( $lang, true );

				$terms = get_terms([
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				]);

				foreach ( $terms as $term ) {

					$output[] = [
						'title' => $term->taxonomy . '-' . $term->slug . '-' . $lang,
						'url'   => wp_parse_url( get_term_link( $term ), PHP_URL_PATH ),
					];
				}
			}
		}
	}

	$sitepress->switch_lang( $sitepress->get_default_language(), true );

	return $output;

}



/**
 * Creates csv from array of rows
 *
 * @param [type] $rows
 * @param string $filename
 * @return void
 */
function wp_cli_create_csv( $rows, string $filename = null ) {

	$filename = $filename ? dirname( __FILE__ ) . '/output/' . $filename : null;

	if ( ! $filename ) {
		WP_CLI::error( 'no filename supplied' );
		return;
	}

	$csv = fopen( $filename, 'w+' );
	foreach ( $rows as $row ) {
		fputcsv( $csv, $row );
	}
	fclose( $csv );

	WP_CLI::success( sprintf( 'File created: %s', $filename ) );

}

$posts    = wpcli_get_published_posts();
$archives = wpcli_get_public_archive_pages();
$terms    = wpcli_get_published_terms();
$urls     = array_merge( $posts, $archives, $terms );
wp_cli_create_csv( $urls, '_urls.csv' );
