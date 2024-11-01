<?php
/**
 * All Custom Post types and its categories are stored Here
 *
 * @author  DarthVader
 * @modified by R2-D2
 * @since  1.0.0
 */

add_action( 'init', 'xloc_register_post_types', 999 );
function xloc_register_post_types() {
	$post_types = array(
		"houses" => array(
			'slug'          => 'house',
			'singular_name' => 'House',
			'plural_name'   => 'Houses',
			'description'   => "Related to house post type",
			'supports'      => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		)
	);
	foreach ( $post_types as $post_type ) {
		extract( $post_type );
		$labels = array(
			'name'               => sprintf( __( '%s', 'xlocate' ), $plural_name ),
			'singular_name'      => sprintf( __( '%s', 'xlocate' ), $singular_name ),
			'menu_name'          => sprintf( __( '%s', 'xlocate' ), $plural_name ),
			'name_admin_bar'     => sprintf( __( '%s', 'xlocate' ), $singular_name ),
			'add_new'            => __( 'Add New', 'xlocate' ),
			'add_new_item'       => sprintf( __( 'Add New %s', 'xlocate' ), $singular_name ),
			'new_item'           => sprintf( __( 'New %s', 'xlocate' ), $singular_name ),
			'edit_item'          => sprintf( __( 'Edit %s', 'xlocate' ), $singular_name ),
			'view_item'          => sprintf( __( 'View %s', 'xlocate' ), $singular_name ),
			'all_items'          => sprintf( __( 'All %s', 'xlocate' ), $plural_name ),
			'search_items'       => sprintf( __( 'Search %s', 'xlocate' ), $plural_name ),
			'parent_item_colon'  => sprintf( __( 'Parent %s:', 'xlocate' ), $plural_name ),
			'not_found'          => sprintf( __( 'No %s found.', 'xlocate' ), $plural_name ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'xlocate' ), $plural_name )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => sprintf( __( '%s.', 'xlocate' ), $description ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $slug ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 56,
			'menu_icon'          => 'dashicons-store',
			'supports'           => $supports,
		);

		register_post_type( $slug, $args );
	}
}

/**
 * All Categories
 *
 * @author  DarthVader
 * @modified by R2-D2
 * @since  1.0.0
 */
add_action( 'init', 'xloc_register_taxnomies' );
function xloc_register_taxnomies() {
	$categories = array(
		"categories" => array(
			'slug'                    => 'xlocate-category',
			'category_post_type_slug' => 'house',
			'singular_name'           => 'Category',
			'plural_name'             => 'Categories'
		)
	);

	foreach ( $categories as $category ) {
		extract( $category );

		$labels = array(
			'name'              => sprintf( __( '%s', 'xlocate' ), $plural_name ),
			'singular_name'     => sprintf( __( '%s', 'xlocate' ), $singular_name ),
			'search_items'      => sprintf( __( 'Search %s', 'xlocate' ), $plural_name ),
			'all_items'         => sprintf( __( 'All %s', 'xlocate' ), $plural_name ),
			'parent_item'       => sprintf( __( 'Parent %s', 'xlocate' ), $singular_name ),
			'parent_item_colon' => sprintf( __( 'Parent %s:', 'xlocate' ), $singular_name ),
			'edit_item'         => sprintf( __( 'Edit %s', 'xlocate' ), $singular_name ),
			'update_item'       => sprintf( __( 'Update %s', 'xlocate' ), $singular_name ),
			'add_new_item'      => sprintf( __( 'Add New %s', 'xlocate' ), $singular_name ),
			'new_item_name'     => sprintf( __( 'New %s Name', 'xlocate' ), $singular_name ),
			'menu_name'         => sprintf( __( '%s', 'xlocate' ), $singular_name ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => $slug ),
			'menu_position'     => 55
		);

		register_taxonomy( $slug, array( $category_post_type_slug ), $args );
	}
}