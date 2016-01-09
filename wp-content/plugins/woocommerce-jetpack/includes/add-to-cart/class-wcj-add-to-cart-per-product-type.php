<?php
/**
 * WooCommerce Jetpack Add to Cart per Product Type
 *
 * The WooCommerce Jetpack Add to Cart per Product Type class.
 *
 * @class    WCJ_Add_To_Cart_Per_Product_Type
 * @version  2.2.0
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'WCJ_Add_To_Cart_Per_Product_Type' ) ) :
 
class WCJ_Add_To_Cart_Per_Product_Type {
    
    /**
     * Constructor.
     */
    public function __construct() {
    
        // Main hooks
        if ( 'yes' === get_option( 'wcj_add_to_cart_enabled' ) ) {		
			if ( get_option( 'wcj_add_to_cart_text_enabled' ) == 'yes' ) {
				add_filter( 'woocommerce_product_single_add_to_cart_text', 	array( $this, 'custom_add_to_cart_button_text' ), 100 );
				add_filter( 'woocommerce_product_add_to_cart_text', 		array( $this, 'custom_add_to_cart_button_text' ), 100 );
			}					
        }        
    }
	
    /**
     * custom_add_to_cart_button_text.
     */
    public function custom_add_to_cart_button_text( $add_to_cart_text ) {
	
		global $woocommerce, $product;
		
		if ( ! $product )
			return $add_to_cart_text;

		$product_type = $product->product_type;
		
		if ( ! in_array( $product_type, array( 'external', 'grouped', 'simple', 'variable' ) ) )
			$product_type = 'other';
		
		$single_or_archive = '';		
		if ( current_filter() == 'woocommerce_product_single_add_to_cart_text' ) $single_or_archive = 'single';
		else if ( current_filter() == 'woocommerce_product_add_to_cart_text' )  $single_or_archive = 'archives';
		
		if ( '' != $single_or_archive ) {
		
			//if ( 'yes' === get_option( 'wcj_add_to_cart_text_enabled_on_' . $single_or_archive . '_in_cart_' . $product_type, 'no' ) ) {
			if ( '' != get_option( 'wcj_add_to_cart_text_on_' . $single_or_archive . '_in_cart_' . $product_type, '' ) ) {
				foreach( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {				
					$_product = $values['data'];				
					if( get_the_ID() == $_product->id )					
						return get_option( 'wcj_add_to_cart_text_on_' . $single_or_archive . '_in_cart_' . $product_type );
				}
			}
			
			$text_on_no_price = get_option( 'wcj_add_to_cart_text_on_' . $single_or_archive . '_no_price_' . $product_type, '' );
			if ( '' != $text_on_no_price && '' === $product->get_price() )
				return $text_on_no_price;
				
			$text_on_zero_price = get_option( 'wcj_add_to_cart_text_on_' . $single_or_archive . '_zero_price_' . $product_type, '' );
			if ( '' != $text_on_zero_price && 0 == $product->get_price() )
				return $text_on_zero_price;				
			
			//if ( get_option( 'wcj_add_to_cart_text_enabled_on_' . $single_or_archive . '_' . $product_type ) == 'yes' )
			if ( '' != get_option( 'wcj_add_to_cart_text_on_' . $single_or_archive . '_' . $product_type ) )
				return get_option( 'wcj_add_to_cart_text_on_' . $single_or_archive . '_' . $product_type );
			else
				return $add_to_cart_text;		
		}

		// Default
		return $add_to_cart_text;
    }
}
 
endif;
 
return new WCJ_Add_To_Cart_Per_Product_Type();
