<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package _s
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function _s_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', '_s_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function _s_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', '_s_pingback_header' );

/**
 * Gets the theme version #
 *
 * @return string $theme_version
 */
function _s_get_theme_version() {
	global $wp_scripts;
	$theme_data    = wp_get_theme();
	$theme_version = $theme_data->get( 'Version' );

	return $theme_version;
}

/**
 * Turns a delimited string into an array
 *
 * @param string $delimiter
 * @param string $string
 * @return false|array $output
 */
function _s_turn_delimited_string_into_an_array( string $delimiter, string $string ) {
	$output = [];
	if ( false !== strpos( $string, $delimiter ) ) {
		$output = explode( $delimiter, $string );
	} else {
		$output[] = $string;
	}

	$output = _s_remove_empty_strings_from_array( $output );

	if ( empty( $output ) ) {
		return false;
	}

	return $output;
}

/**
 * Removes empty strings from array
 *
 * @param array $array_of_strings
 * @return false|array $array_of_strings
 */
function _s_remove_empty_strings_from_array( array $array_of_strings ) {

	foreach ( $array_of_strings as $key => $string ) {

		if ( empty( trim( $string ) ) ) {
			unset( $array_of_strings[ $key ] );
		} else {
			$array_of_strings[ $key ] = trim( $string );
		}
	}

	if ( empty( $array_of_strings ) ) {
		return false;
	}

	return $array_of_strings;

}

/**
 * Determines if a post, identified by the specified ID, exist
 * within the WordPress database.
 *
 * Note that this function uses the 'acme_' prefix to serve as an
 * example for how to use the function within a theme. If this were
 * to be within a class, then the prefix would not be necessary.
 *
 * @param    int    $id    The ID of the post to check
 * @return   bool          True if the post exists; otherwise, false.
 * @since    1.0.0
 */
function _s_post_exists( $id ) {
	return is_string( get_post_status( $id ) );
}
