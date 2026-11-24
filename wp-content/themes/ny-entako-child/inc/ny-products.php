<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ny_render_products_shortcode( $atts ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '<p class="ny-empty-prod">WooCommerce n\'est pas actif.</p>';
	}

	$atts = shortcode_atts(
		array(
			'limit'        => 9,
			'category'     => '',
			'orderby'      => 'date',
			'order'        => 'DESC',
			'facebook_url' => '#',
		),
		$atts,
		'ny_products'
	);

	$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => (int) $atts['limit'],
		'paged'          => $paged,
		'order'          => strtoupper( $atts['order'] ) === 'ASC' ? 'ASC' : 'DESC',
	);

	switch ( $atts['orderby'] ) {
		case 'price':
			$args['meta_key'] = '_price';
			$args['orderby']  = 'meta_value_num';
			break;
		case 'title':
			$args['orderby'] = 'title';
			break;
		case 'menu_order':
			$args['orderby'] = 'menu_order title';
			break;
		default:
			$args['orderby'] = 'date';
			break;
	}

	if ( ! empty( $atts['category'] ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy'         => 'product_cat',
				'field'            => 'slug',
				'terms'            => sanitize_title( $atts['category'] ),
				'include_children' => true,
			),
		);
	}

	$query = new WP_Query( $args );

	ob_start();

	if ( ! $query->have_posts() ) {
		echo '<div class="ny-block-prod" data-found-posts="0" data-shown-posts="0"><p class="ny-empty-prod">Aucun produit disponible pour le moment.</p></div>';
		wp_reset_postdata();
		return ob_get_clean();
	}

	echo '<div class="ny-block-prod" data-found-posts="' . esc_attr( (int) $query->found_posts ) . '" data-shown-posts="' . esc_attr( (int) $query->post_count ) . '">';

	while ( $query->have_posts() ) {
		$query->the_post();
		$product = wc_get_product( get_the_ID() );

		if ( ! $product ) {
			continue;
		}

		$promo_badge = '';
		if ( $product->is_on_sale() ) {
			$regular_price = (float) $product->get_regular_price();
			$sale_price    = (float) $product->get_sale_price();

			if ( $regular_price > 0 && $sale_price > 0 && $sale_price < $regular_price ) {
				$percent     = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
				$promo_badge = '<span class="ny-badge-promo">PROMO -' . esc_html( $percent ) . '%</span>';
			} else {
				$promo_badge = '<span class="ny-badge-promo">PROMO</span>';
			}
		}

		$terms         = get_the_terms( $product->get_id(), 'product_cat' );
		$raw_cat_name  = ( $terms && ! is_wp_error( $terms ) )
			? wp_specialchars_decode( $terms[0]->name, ENT_QUOTES )
			: 'PRODUIT';
		$cat_label     = mb_strtoupper( $raw_cat_name, 'UTF-8' );
		$product_title = wp_specialchars_decode( get_the_title(), ENT_QUOTES );

		$short_desc    = wp_specialchars_decode( wp_strip_all_tags( $product->get_short_description() ), ENT_QUOTES );
		$fallback_note = ! empty( $short_desc )
			? $short_desc
			: wp_specialchars_decode( wp_trim_words( wp_strip_all_tags( get_the_content() ), 10, '...' ), ENT_QUOTES );

		$img_id  = $product->get_image_id();
		$img_url = $img_id
			? wp_get_attachment_image_url( $img_id, 'medium' )
			: 'http://nyentako.local/wp-content/uploads/2026/11/produit-ny-entako-removebg-preview.png';

		$raw_price  = wc_get_price_to_display( $product );
		$price_text = ( '' !== $product->get_price() )
			? number_format_i18n( (float) $raw_price, 0 ) . ' Ar'
			: '';

		$order_url = ! empty( $atts['facebook_url'] ) ? esc_url( $atts['facebook_url'] ) : esc_url( get_permalink( $product->get_id() ) );
		?>
		<div class="ny-card-prod" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
			<div class="ny-block-img">
				<?php echo $promo_badge; ?>
				<button type="button" class="ny-btn-wishlist" aria-label="Ajouter aux favoris" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
					<i class="fa-regular fa-heart"></i>
				</button>
				<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" class="ny-img-prod">
					<img src="<?php echo esc_url( $img_url ); ?>" class="ny-custom-img-prod" alt="<?php echo esc_attr( $product_title ); ?>">
				</a>
			</div>
			<div class="ny-info-prod">
				<h5 class="ny-ttl-prod"><?php echo esc_html( $cat_label ); ?></h5>
				<h5 class="ny-cat-prod">
					<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"><?php echo esc_html( $product_title ); ?></a>
				</h5>
				<p class="ny-not-prod"><?php echo esc_html( $fallback_note ); ?></p>
			</div>
			<div class="ny-order-prod">
				<p class="ny-price-prod"><?php echo esc_html( $price_text ); ?></p>
				<div class="ny-btn-order">
					<a href="<?php echo $order_url; ?>" class="ny-link-order" target="_blank" rel="noopener noreferrer">
						<i class="fa-brands fa-facebook-f"></i>
						<span>Commander</span>
					</a>
				</div>
			</div>
		</div>
		<?php
	}

	echo '</div>';

	wp_reset_postdata();

	return ob_get_clean();
}
add_shortcode( 'ny_products', 'ny_render_products_shortcode' );