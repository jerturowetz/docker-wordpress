<?php

/**
 * Triggers theme cleanup items after_theme_setup
 *
 * @return void
 */
function _s_trigger_theme_cleanup() {
	add_action( 'init', '_s_theme_cleanup' );
}
add_action( 'after_setup_theme', '_s_trigger_theme_cleanup' );

/**
 * WordPress general clenup for unused or bulky items
 *
 * @return void
 */
function _s_theme_cleanup() {

	// Remove the rich editor
	add_filter( 'user_can_richedit', '__return_false', 50 );

	// Remove editURI link
	remove_action( 'wp_head', 'rsd_link' );

	// Remove category feed links
	remove_action( 'wp_head', 'feed_links_extra', 3 );

	// Remove post and comment feed links
	remove_action( 'wp_head', 'feed_links', 2 );

	// remove Windows Live Writer
	remove_action( 'wp_head', 'wlwmanifest_link' );

	// remove Index link
	remove_action( 'wp_head', 'index_rel_link' );

	// removee Previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );

	// remove Start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );

	// remove Canonical link
	remove_action( 'wp_head', 'rel_canonical', 10, 0 );

	// remove Shortlink
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

	// remove Links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

	// remove WP version
	remove_action( 'wp_head', 'wp_generator' );

	// remove Emoji detection script
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );

	// remove Emoji styles
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
}
