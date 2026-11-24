<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_ny_filter_products', 'ny_ajax_filter_products_handler' );
add_action( 'wp_ajax_nopriv_ny_filter_products', 'ny_ajax_filter_products_handler' );

function ny_ajax_filter_products_handler() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		wp_send_json_error( array( 'message' => 'WooCommerce inactif' ) );
	}

	$category     = isset( $_POST['category'] ) ? sanitize_title( wp_unslash( $_POST['category'] ) ) : '';
	$orderby      = isset( $_POST['orderby'] ) ? sanitize_key( wp_unslash( $_POST['orderby'] ) ) : 'date';
	$order        = isset( $_POST['order'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_POST['order'] ) ) ) : 'DESC';
	$limit        = isset( $_POST['limit'] ) ? max( 1, (int) $_POST['limit'] ) : 9;
	$facebook_url = isset( $_POST['facebook_url'] ) ? esc_url_raw( wp_unslash( $_POST['facebook_url'] ) ) : '#';

	$html = ny_render_products_shortcode(
		array(
			'limit'        => $limit,
			'category'     => $category,
			'orderby'      => $orderby,
			'order'        => $order,
			'facebook_url' => $facebook_url,
		)
	);

	$title_text = 'Tous les Produits';
	if ( ! empty( $category ) ) {
		$term = get_term_by( 'slug', $category, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			$title_text = wp_specialchars_decode( $term->name, ENT_QUOTES );
		}
	}

	wp_send_json_success(
		array(
			'html'       => $html,
			'title_text' => $title_text,
		)
	);
}