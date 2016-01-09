<?php
/**
 * WooCommerce Jetpack Checkout Custom Info
 *
 * The WooCommerce Jetpack Checkout Custom Info class.
 *
 * @version 2.2.0
 * @since   2.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_Checkout_Custom_Info' ) ) :

class WCJ_Checkout_Custom_Info extends WCJ_Module {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id         = 'checkout_custom_info';
		$this->short_desc = __( 'Checkout Custom Info', 'woocommerce-jetpack' );
		$this->desc       = __( 'Add custom info to WooCommerce checkout page.', 'woocommerce-jetpack' );
		parent::__construct();

	    if ( $this->is_enabled() ) {
			for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_custom_info_total_number', 1 ) ); $i++) {
				add_action(
					get_option( 'wcj_checkout_custom_info_hook_' . $i, 'woocommerce_checkout_after_order_review' ),
					array( $this, 'add_checkout_custom_info' )
				);
			}
	    }
	}

	/**
	 * get_settings.
	 */
	function get_settings() {

		//global $woocommerce;

	    $settings = array();

		// Checkout Custom Info Options
		$settings[] = array( 'title' => __( 'Checkout Custom Info Blocks', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_checkout_custom_info_blocks_options' );

		$settings[] = array(
			'title'    => __( 'Total Blocks', 'woocommerce-jetpack' ),
			'id'       => 'wcj_checkout_custom_info_total_number',
			'default'  => 1,
			'type'     => 'custom_number',
			'desc'     => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
			'custom_attributes'
			           => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
		);

		$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_checkout_custom_info_blocks_options' );

		$total_number = apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_custom_info_total_number', 1 ) );

		for ( $i = 1; $i <= $total_number; $i++) {

			$settings = array_merge( $settings, array(

				array( 'title' => __( 'Info Block', 'woocommerce-jetpack' ) . ' #' . $i, 'type' => 'title', 'desc' => '', 'id' => 'wcj_checkout_custom_info_options_' . $i, ),

				array(
					'title'    => __( 'Content', 'woocommerce-jetpack' ),
					'id'       => 'wcj_checkout_custom_info_content_' . $i,
					'default'  => '[wcj_cart_items_total_weight before="Total weight: " after=" kg"]',
					'type'     => 'textarea',
					'css'      => 'width:30%;min-width:300px;height:100px;',
				),

				array(
					'title'    => __( 'Position', 'woocommerce-jetpack' ),
					'id'       => 'wcj_checkout_custom_info_hook_' . $i,
					'default'  => 'woocommerce_checkout_after_order_review',
					'type'     => 'select',
					'options'  => array(
						'woocommerce_before_checkout_form'              => __( 'Before checkout form', 'woocommerce-jetpack' ),
						'woocommerce_checkout_before_customer_details'  => __( 'Before customer details', 'woocommerce-jetpack' ),
						'woocommerce_checkout_billing'                  => __( 'Billing', 'woocommerce-jetpack' ),
						'woocommerce_checkout_shipping'                 => __( 'Shipping', 'woocommerce-jetpack' ),
						'woocommerce_checkout_after_customer_details'   => __( 'After customer details', 'woocommerce-jetpack' ),
						'woocommerce_checkout_before_order_review'      => __( 'Before order review', 'woocommerce-jetpack' ),
						'woocommerce_checkout_order_review'             => __( 'Order review', 'woocommerce-jetpack' ),
						'woocommerce_checkout_after_order_review'       => __( 'After order review', 'woocommerce-jetpack' ),
						'woocommerce_after_checkout_form'               => __( 'After checkout form', 'woocommerce-jetpack' ),

						/*
						'woocommerce_before_checkout_shipping_form'     => __( 'woocommerce_before_checkout_shipping_form', 'woocommerce-jetpack' ),
						'woocommerce_after_checkout_shipping_form'      => __( 'woocommerce_after_checkout_shipping_form', 'woocommerce-jetpack' ),
						'woocommerce_before_order_notes'                => __( 'woocommerce_before_order_notes', 'woocommerce-jetpack' ),
						'woocommerce_after_order_notes'                 => __( 'woocommerce_after_order_notes', 'woocommerce-jetpack' ),

						'woocommerce_before_checkout_billing_form'      => __( 'woocommerce_before_checkout_billing_form', 'woocommerce-jetpack' ),
						'woocommerce_after_checkout_billing_form'       => __( 'woocommerce_after_checkout_billing_form', 'woocommerce-jetpack' ),
						'woocommerce_before_checkout_registration_form' => __( 'woocommerce_before_checkout_registration_form', 'woocommerce-jetpack' ),
						'woocommerce_after_checkout_registration_form'  => __( 'woocommerce_after_checkout_registration_form', 'woocommerce-jetpack' ),

						'woocommerce_review_order_before_cart_contents' => __( 'woocommerce_review_order_before_cart_contents', 'woocommerce-jetpack' ),
						'woocommerce_review_order_after_cart_contents'  => __( 'woocommerce_review_order_after_cart_contents', 'woocommerce-jetpack' ),
						'woocommerce_review_order_before_shipping'      => __( 'woocommerce_review_order_before_shipping', 'woocommerce-jetpack' ),
						'woocommerce_review_order_after_shipping'       => __( 'woocommerce_review_order_after_shipping', 'woocommerce-jetpack' ),
						'woocommerce_review_order_before_order_total'   => __( 'woocommerce_review_order_before_order_total', 'woocommerce-jetpack' ),
						'woocommerce_review_order_after_order_total'    => __( 'woocommerce_review_order_after_order_total', 'woocommerce-jetpack' ),
						*/
					),
					'css'      => 'width:250px;',
				),

				array(
					'title'    => __( 'Order (i.e. Priority)', 'woocommerce-jetpack' ),
					'id'       => 'wcj_checkout_custom_info_priority_' . $i,
					'default'  => 10,
					'type'     => 'number',
					'css'      => 'width:250px;',
				),

				array( 'type'  => 'sectionend', 'id' => 'wcj_checkout_custom_info_options_' . $i ),
			) );
		}

	    return $this->add_enable_module_setting( $settings );
	}

	/**
	 * add_checkout_custom_info.
	 */
	function add_checkout_custom_info() {
		$current_filter = current_filter();
		$total_number = apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_custom_info_total_number', 1 ) );
		for ( $i = 1; $i <= $total_number; $i++) {
			if ( '' != get_option( 'wcj_checkout_custom_info_content_' . $i ) && $current_filter === get_option( 'wcj_checkout_custom_info_hook_' . $i ) ) {
				echo do_shortcode( get_option( 'wcj_checkout_custom_info_content_' . $i ) );
			}
		}
	}
}

endif;

return new WCJ_Checkout_Custom_Info();
