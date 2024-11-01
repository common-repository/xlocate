<?php
/**
 * Shortcode Container File
 * @since 1.0.0
 * @created by DarthVader
 */
add_shortcode( 'xlocate_map', 'xloc_map' );
function xloc_map() {
	$rem = array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'site_url' => site_url(),
		'page_url' => get_permalink()
	);
    wp_localize_script( 'xloc-map', 'rem', $rem );
    wp_enqueue_script( 'xloc-map' );
    wp_enqueue_style( 'xloc-map-css' );
	wp_enqueue_style( 'xloc-skin' );

	ob_start();
	xloc_get_template( 'map-listing.php' );
	return ob_get_clean();
}

add_shortcode( 'xlocate_search_form', 'xloc_search_form' );
function xloc_search_form() {
	wp_enqueue_script( 'xloc-map' );
	wp_enqueue_style( 'xloc-map-css' );
	wp_enqueue_style( 'xloc-skin' );

	ob_start();
	xloc_get_template( 'search-form.php' );
	return ob_get_clean();
}