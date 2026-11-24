<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ny_render_categories_shortcode() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '';
	}

	$default_cat_id = (int) get_option( 'default_product_cat' );

	$parent_categories = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'parent'     => 0,
		'exclude'    => $default_cat_id ? array( $default_cat_id ) : array(),
		'orderby'    => 'menu_order',
		'order'      => 'ASC',
	) );

	$product_counts = wp_count_posts( 'product' );
	$total_articles = isset( $product_counts->publish ) ? (int) $product_counts->publish : 0;

	$shop_url       = get_permalink( wc_get_page_id( 'shop' ) );
	$current_cat_id = is_product_category() ? (int) get_queried_object_id() : 0;

	$icons_by_slug = array(
		'beaute-soins' => 'fa-pump-soap',
		'electronique' => 'fa-mobile-screen-button',
		'vetements'    => 'fa-shirt',
		'jeux-jouets'  => 'fa-gamepad',
		'materiels'    => 'fa-wrench',
		'maisons-deco' => 'fa-house',
	);

	ob_start();
	?>
	<div class="ny-cat">
		<div class="ny-block-title-cat">
			<p class="ny-title-cat">Catégories</p>
			<p class="ny-total-cat"><?php echo esc_html( $total_articles ); ?> articles</p>
		</div>

		<div class="ny-cat-list">
			
			<a href="<?php echo esc_url( $shop_url ); ?>" data-cat-slug="" class="ny-block-cat <?php echo ( 0 === $current_cat_id ) ? 'active' : ''; ?>">
				<div class="ny-block-icon-cat">
					<i class="fa-solid fa-border-all icon-white"></i>
				</div>
				<p class="ny-name-cat">Toutes les catégories</p>
				<p class="ny-count-badge"><?php echo esc_html( $total_articles ); ?></p>
			</a>

			<?php if ( ! empty( $parent_categories ) && ! is_wp_error( $parent_categories ) ) : ?>
				<?php foreach ( $parent_categories as $parent_cat ) :
					$subcategories = get_terms( array(
						'taxonomy'   => 'product_cat',
						'hide_empty' => false,
						'parent'     => $parent_cat->term_id,
						'orderby'    => 'menu_order',
						'order'      => 'ASC',
					) );

					$has_subcategories = ! empty( $subcategories ) && ! is_wp_error( $subcategories );
					$icon_class        = isset( $icons_by_slug[ $parent_cat->slug ] ) ? $icons_by_slug[ $parent_cat->slug ] : 'fa-tag';
					$is_parent_active  = ( $current_cat_id === (int) $parent_cat->term_id );

					$has_active_child = false;
					if ( $has_subcategories ) {
						foreach ( $subcategories as $sub_cat ) {
							if ( $current_cat_id === (int) $sub_cat->term_id ) {
								$has_active_child = true;
								break;
							}
						}
					}

					$group_classes = 'ny-cat-group' . ( ( $is_parent_active || $has_active_child ) ? ' open' : '' );
					$cat_link      = get_term_link( $parent_cat );
				?>

					<?php if ( $has_subcategories ) : ?>
						<div class="<?php echo esc_attr( $group_classes ); ?>">
							<a href="<?php echo esc_url( $cat_link ); ?>" data-cat-slug="<?php echo esc_attr( $parent_cat->slug ); ?>" class="ny-block-cat ny-has-subcat <?php echo $is_parent_active ? 'active' : ''; ?>">
								<div class="ny-block-icon-cat">
									<i class="fa-solid <?php echo esc_attr( $icon_class ); ?> icon-white"></i>
								</div>
								<p class="ny-name-cat"><?php echo esc_html( wp_specialchars_decode( $parent_cat->name, ENT_QUOTES ) ); ?></p>
								<p class="ny-count-badge"><?php echo esc_html( (int) $parent_cat->count ); ?></p>
								<i class="fa-solid fa-chevron-down ny-chevron-cat"></i>
							</a>

							<div class="ny-subcat-list">
								<?php foreach ( $subcategories as $sub_cat ) :
									$is_sub_active = ( $current_cat_id === (int) $sub_cat->term_id );
									$sub_link      = get_term_link( $sub_cat );
								?>
									<a href="<?php echo esc_url( $sub_link ); ?>" data-cat-slug="<?php echo esc_attr( $sub_cat->slug ); ?>" class="ny-subcat-item <?php echo $is_sub_active ? 'active' : ''; ?>">
										<span><?php echo esc_html( wp_specialchars_decode( $sub_cat->name, ENT_QUOTES ) ); ?></span>
										<span class="ny-subcat-count"><?php echo esc_html( (int) $sub_cat->count ); ?></span>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					<?php else : ?>
						<a href="<?php echo esc_url( $cat_link ); ?>" data-cat-slug="<?php echo esc_attr( $parent_cat->slug ); ?>" class="ny-block-cat <?php echo $is_parent_active ? 'active' : ''; ?>">
							<div class="ny-block-icon-cat">
								<i class="fa-solid <?php echo esc_attr( $icon_class ); ?> icon-white"></i>
							</div>
							<p class="ny-name-cat"><?php echo esc_html( wp_specialchars_decode( $parent_cat->name, ENT_QUOTES ) ); ?></p>
							<p class="ny-count-badge"><?php echo esc_html( (int) $parent_cat->count ); ?></p>
						</a>
					<?php endif; ?>

				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'ny_categories', 'ny_render_categories_shortcode' );