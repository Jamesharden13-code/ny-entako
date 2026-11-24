<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueuing stylesheets and scripts for the child theme
 */
add_action( 'wp_enqueue_scripts', 'nyentako_child_enqueue_assets' );
function nyentako_child_enqueue_assets() {
	$theme_version = wp_get_theme()->get( 'Version' );

	// Styles parent, enfant et Font Awesome
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'ny-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.5.1' );
	wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'parent-style', 'ny-fontawesome' ), $theme_version );

	// AJAX script for filtering products and categories
	wp_enqueue_script(
		'ny-filter-js',
		get_stylesheet_directory_uri() . '/assets/js/ny-filter.js',
		array(),
		$theme_version,
		true
	);

	wp_localize_script(
		'ny-filter-js',
		'nyAjaxConfig',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		)
	);
}

/**
 * Inclusion of functional modules
 */
require_once get_stylesheet_directory() . '/inc/ny-categories.php';
require_once get_stylesheet_directory() . '/inc/ny-products.php';
require_once get_stylesheet_directory() . '/inc/ny-contact.php';
require_once get_stylesheet_directory() . '/inc/ny-ajax.php';