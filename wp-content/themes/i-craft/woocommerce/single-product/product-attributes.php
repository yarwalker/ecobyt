<?php
/**
 * Product attributes
 *
 * Used by list_attributes() in the products class
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$has_row    = false;
$alt        = 1;
$attributes = $product->get_attributes();

ob_start();
?>
<div class="shop_attributes">

	<?php if ( $product->enable_dimensions_display() ) : ?>

		<?php if ( $product->has_weight() ) : $has_row = true; ?>
			<div class="<?php if ( ( $alt = $alt * -1 ) == 1 ) echo 'alt'; ?>">
				<span class="attr_title"><?php _e( 'Weight', 'i-craft' ) ?>&colon;</span>
				<span class="product_weight"><?php echo $product->get_weight() . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) ); ?></span>
			</div>
		<?php endif; ?>

		<?php if ( $product->has_dimensions() ) : $has_row = true; ?>
			<div class="<?php if ( ( $alt = $alt * -1 ) == 1 ) echo 'alt'; ?>">
				<span class="attr_title"><?php _e( 'Dimensions', 'i-craft' ) ?>&colon;</span>
				<span class="product_dimensions"><?php echo $product->get_dimensions(); ?></span>
			</div>
		<?php endif; ?>

	<?php endif; ?>

	<?php foreach ( $attributes as $attribute ) :
		if ( empty( $attribute['is_visible'] ) || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) ) ) {
			continue;
		} else {
			$has_row = true;
		}
		?>
		<div class="<?php if ( ( $alt = $alt * -1 ) == 1 ) echo 'alt'; ?>">
			<span class="attr_title"><?php echo wc_attribute_label( $attribute['name'] ); ?>&colon;</span>
			<span><?php
				if ( $attribute['is_taxonomy'] ) {

					$values = wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) );
					echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

				} else {

					// Convert pipes to commas and display values
					$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
					echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

				}
			?></span>
		</div>
	<?php endforeach; ?>
	
</div>
<?php
if ( $has_row ) {
	echo ob_get_clean();
} else {
	ob_end_clean();
}