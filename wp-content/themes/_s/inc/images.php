<?php

/**
 * Add image sizes to theme
 *
 * @return void
 */
function _s_custom_image_sizes() {

	/**
	 * Add featured image sizes
	 *
	 * Sizes are optimized and cropped for landscape aspect ratio
	 * and optimized for HiDPI displays on 'small' and 'medium' screen sizes.
	 */
	// add_image_size( 'featured-small', 640, 200, true );
	// add_image_size( 'featured-medium', 1280, 400, true );
	// add_image_size( 'featured-large', 1440, 400, true );
	// add_image_size( 'featured-xlarge', 1920, 400, true );

	/**
	 * Add additional image sizes
	 */
	add_image_size( '_s-small', 640 );
	add_image_size( '_s-medium', 1024 );
	add_image_size( '_s-large', 1200 );
	add_image_size( '_s-xlarge', 1920 );

}
add_action( 'after_setup_theme', '_s_custom_image_sizes' );


/**
 * Register the new image sizes for use in the add media modal in wp-admin
 *
 * @param array $sizes
 * @return void
 */
function _s_custom_image_sizes_to_editor( $sizes ) {
	// Please note the space in the names below is used in the init script renaming process
	// So feel free to change it if you like
	return array_merge( $sizes, array(
		'_s-small'  => __( ' _s Small' ),
		'_s-medium' => __( ' _s Medium' ),
		'_s-large'  => __( ' _s Large' ),
		'_s-xlarge' => __( ' _s XLarge' ),
	) );
}
add_filter( 'image_size_names_choose', '_s_custom_image_sizes_to_editor' );

/**
 * Add custom image sizes attribute to enhance responsive image functionality for content images
 *
 * @param string $sizes
 * @param array $size
 * @return string $sizes
 */
function _s_adjust_image_sizes_attr( $sizes, $size ) {

	// Actual width of image
	$width = $size[0];

	// Full width page template
	if ( is_page_template( 'page-templates/page-full-width.php' ) ) {
		if ( 1200 < $width ) {
			$sizes = '(max-width: 1199px) 98vw, 1200px';
		} else {
			$sizes = '(max-width: 1199px) 98vw, ' . $width . 'px';
		}
	} else { // Default 3/4 column post/page layout
		if ( 770 < $width ) {
			$sizes = '(max-width: 639px) 98vw, (max-width: 1199px) 64vw, 770px';
		} else {
			$sizes = '(max-width: 639px) 98vw, (max-width: 1199px) 64vw, ' . $width . 'px';
		}
	}

	return $sizes;
}
add_filter( 'wp_calculate_image_sizes', '_s_adjust_image_sizes_attr', 10, 2 );

/**
 * Remove inline width and height attributes for post thumbnails
 *
 * @param [type] $html
 * @param [type] $post_id
 * @param [type] $post_image_id
 * @return void
 */
function _s_remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
	$html = preg_replace( '/(width|height)=\"\d*\"\s/', '', $html );
	return $html;
}
add_filter( 'post_thumbnail_html', '_s_remove_thumbnail_dimensions', 10, 3 );
// add_filter( 'image_send_to_editor', '_s_remove_thumbnail_dimensions', 10, 3 );

/**
 * Set the default embed type to 'none' instead of 'link'
 *
 * @return void
 */
function _s_set_image_default_link_type() {
	$image_set = get_option( 'image_default_link_type' );
	if ( 'none' !== $image_set ) {
		update_option( 'image_default_link_type', 'none' );
	}
}
add_action( 'admin_init', '_s_set_image_default_link_type', 10 );
