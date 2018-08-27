<?php
/**
 * Configure responsive images sizes
 *
 * @package WordPress
 * @subpackage FoundationPress
 * @since FoundationPress 2.6.0
 */

// Add featured image sizes
//
// Sizes are optimized and cropped for landscape aspect ratio
// and optimized for HiDPI displays on 'small' and 'medium' screen sizes.
add_image_size( 'featured-small', 640, 200, true ); // name, width, height, crop
add_image_size( 'featured-medium', 1280, 400, true );
add_image_size( 'featured-large', 1440, 400, true );
add_image_size( 'featured-xlarge', 1920, 400, true );

// Add additional image sizes
add_image_size( 'fp-small', 640 );
add_image_size( 'fp-medium', 1024 );
add_image_size( 'fp-large', 1200 );
add_image_size( 'fp-xlarge', 1920 );

// Register the new image sizes for use in the add media modal in wp-admin
function foundationpress_custom_sizes( $sizes ) {
	return array_merge( $sizes, array(
		'fp-small'  => __( 'FP Small' ),
		'fp-medium' => __( 'FP Medium' ),
		'fp-large'  => __( 'FP Large' ),
		'fp-xlarge'  => __( 'FP XLarge' ),
	) );
}
add_filter( 'image_size_names_choose', 'foundationpress_custom_sizes' );

// Add custom image sizes attribute to enhance responsive image functionality for content images
function foundationpress_adjust_image_sizes_attr( $sizes, $size ) {

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
add_filter( 'wp_calculate_image_sizes', 'foundationpress_adjust_image_sizes_attr', 10 , 2 );

// Remove inline width and height attributes for post thumbnails
function remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
	$html = preg_replace( '/(width|height)=\"\d*\"\s/', '', $html );
	return $html;
}
add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10, 3 );



/**
 * Image template functions
 *
 * @package standard
 */



/**
 * Remove image width and height on
 * the_post_thumbnail and when embedding images
 *
 * @param string $html
 * @return string $html
 */
function sp_remove_thumbnail_dimensions( string $html ) {
	$html = preg_replace( '/(width|height)="\d*"\s/', '', $html );
	return $html;
}
add_filter( 'post_thumbnail_html', 'sp_remove_thumbnail_dimensions', 10 );
add_filter( 'image_send_to_editor', 'sp_remove_thumbnail_dimensions', 10 );



/**
 * Set the default embed type to 'none' instead of 'link'
 *
 * @return void
 */
function wpb_imagelink_setup() {
	$image_set = get_option( 'image_default_link_type' );
	if ( 'none' !== $image_set ) {
		update_option( 'image_default_link_type', 'none' );
	}
}
add_action( 'admin_init', 'wpb_imagelink_setup', 10 );



/**
 * Add various image sizes to theme
 *
 * @return void
 */
function sp_add_image_sizes() {
	add_image_size( 'featured', 1170, 660, true );
	add_image_size( 'product', 640, 640, true );
	add_image_size( 'icon-large', 360, 360, true );
	add_image_size( 'icon-small', 180, 180, true );
}
add_action( 'after_setup_theme', 'sp_add_image_sizes' );



/**
 * Adds custom image sizes to editors
 *
 * @param array $sizes
 * @return void
 */
function sp_custom_sizes( array $sizes ) {
	return array_merge( $sizes, array(
		'featured'   => 'Featured',
		'product'    => 'Square',
		'icon-large' => 'Icon (large)',
		'icon-small' => 'Icon (small)',
	) );
}
add_filter( 'image_size_names_choose', 'sp_custom_sizes' );
