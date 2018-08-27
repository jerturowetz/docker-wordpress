<?php
/**
 * _s Login fuctions
 *
 * @package _s
 */

/**
 * Changes the url on the login logo from WordPress to the site address
 *
 * @return string
 */
function _s_change_login_url() {
	return home_url();
}
add_filter( 'login_headerurl', '_s_change_login_url' );

/**
 * Change the title on the login logo to the site name
 *
 * @return string
 */
function _s_change_login_title() {
	return get_bloginfo();
}
add_filter( 'login_headertitle', '_s_change_login_title' );

/**
 * Disables shake on login screen
 *
 * @return void
 */
function _s_kill_login_shake() {
	remove_action( 'login_head', 'wp_shake_js', 12 );
}
add_action( 'login_head', '_s_kill_login_shake' );

/**
 * Loads login.min.css stylesheet on the login page
 *
 * @return void
 */
function _s_custom_login_stylesheet() {
	wp_enqueue_style( 'custom-login', get_template_directory_uri() . '/login.css', [], _s_get_theme_version() );
}
add_action( 'login_enqueue_scripts', '_s_custom_login_stylesheet' );

