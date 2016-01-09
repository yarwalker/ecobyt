<?php
/**
 * WooCommerce Jetpack Payment Gateways Currency
 *
 * The WooCommerce Jetpack Payment Gateways Currency class.
 *
 * @version 2.3.5
 * @since   2.3.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Payment_Gateways_Currency' ) ) :

class WCJ_Payment_Gateways_Currency extends WCJ_Module {

	/**
	 * Constructor.
	 *
	 * @version 2.3.5
	 */
	function __construct() {

		$this->id         = 'payment_gateways_currency';
		$this->short_desc = __( 'Gateways Currency', 'woocommerce-jetpack' );
		$this->desc       = __( 'Currency per WooCommerce payment gateway.', 'woocommerce-jetpack' );
		parent::__construct();

		add_action( 'init', array( $this, 'add_settings_hook' ) );

		if ( $this->is_enabled() ) {
			/* add_action( 'init', array( $this, 'add_hooks' ) ); */
			$this->add_hooks();
		}
	}

	/**
	 * add_hooks.
	 *
	 * @version 2.3.2
	 * @since   2.3.2
	 */
	function add_hooks() {
		add_filter( 'woocommerce_currency_symbol', array( $this, 'change_currency_symbol' ), PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_currency',        array( $this, 'change_currency_code' ), PHP_INT_MAX, 1 );

		add_filter( 'woocommerce_paypal_supported_currencies', array( $this, 'extend_paypal_supported_currencies' ), PHP_INT_MAX, 1 );

		add_filter( 'woocommerce_get_price', array( $this, 'change_price_by_gateway' ), PHP_INT_MAX, 2 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_checkout_script' ) );
		add_action( 'init',               array( $this, 'register_script' ) );
	}

	/**
	 * is_cart_or_checkout.
	 *
	 * @version 2.3.5
	 */
	function is_cart_or_checkout() {
		//if ( wcj_is_frontend() ) {
		if ( ! is_admin() ) {
			if ( is_cart() || is_checkout() ) return true;
		}
		return false;
	}

	/**
	 * change_price_by_gateway.
	 */
	function change_price_by_gateway( $price, $product ) {
		if ( $this->is_cart_or_checkout() ) {
			global $woocommerce;
			$current_gateway = $woocommerce->session->chosen_payment_method;
			if ( '' != $current_gateway ) {
				$gateway_currency_exchange_rate = get_option( 'wcj_gateways_currency_exchange_rate_' . $current_gateway );
				$price = $price * $gateway_currency_exchange_rate;
			}
		}
		return $price;
	}

	/**
	 * extend_paypal_supported_currencies.
	 */
	function extend_paypal_supported_currencies( $supported_currencies ) {
		if ( $this->is_cart_or_checkout() ) {
			global $woocommerce;
			$current_gateway = $woocommerce->session->chosen_payment_method;
			if ( '' != $current_gateway ) {
				$gateway_currency = get_option( 'wcj_gateways_currency_' . $current_gateway );
				$supported_currencies[] = $gateway_currency;
			}
		}
		return $supported_currencies;
	}

	/**
	 * change_currency_symbol.
	 */
	public function change_currency_symbol( $currency_symbol, $currency ) {
		if ( $this->is_cart_or_checkout() ) {
			global $woocommerce;
			$current_gateway = $woocommerce->session->chosen_payment_method;
			if ( '' != $current_gateway ) {
				$gateway_currency = get_option( 'wcj_gateways_currency_' . $current_gateway );
				return wcj_get_currency_symbol( $gateway_currency );
			}
		}
		return $currency_symbol;
	}

	/**
	 * change_currency_code.
	 */
	public function change_currency_code( $currency ) {
		if ( $this->is_cart_or_checkout() ) {
			global $woocommerce;
			$current_gateway = $woocommerce->session->chosen_payment_method;
			/* $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
			if ( ! array_key_exists( $current_gateway, $available_gateways ) ) {
				$current_gateway = get_option( 'woocommerce_default_gateway', '' );
				if ( '' == $current_gateway ) {
					$current_gateway = current( $available_gateways );
					$current_gateway = isset( $current_gateway->id ) ? $current_gateway->id : '';
				}
			} */
			if ( '' != $current_gateway ) {
				$gateway_currency  = get_option( 'wcj_gateways_currency_' . $current_gateway );
				return $gateway_currency;
			}
		}
		return $currency;
	}

	/**
	 * get_settings.
	 */
	function get_settings() {
		$settings = array();
		$settings = apply_filters( 'wcj_payment_gateways_currency_settings', $settings );
		return $this->add_enable_module_setting( $settings );
	}

	/**
	 * add_settings_hook.
	 *
	 * @version 2.3.2
	 */
	function add_settings_hook() {
		add_filter( 'wcj_payment_gateways_currency_settings', array( $this, 'add_currency_settings' ) );
	}

	/**
	 * register_script.
	 */
	public function register_script() {
		wp_register_script( 'wcj-payment-gateways-checkout', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/checkout.js', array( 'jquery' ), false, true );
	}

	/**
	 * enqueue_checkout_script.
	 */
	public function enqueue_checkout_script() {
		if( ! is_checkout() )
			return;
		wp_enqueue_script( 'wcj-payment-gateways-checkout' );
	}

	/**
	 * add_currency_settings.
	 */
	function add_currency_settings( $settings ) {

		$settings[] = array(
			'title' => __( 'Payment Gateways Currency Options', 'woocommerce-jetpack' ),
			'type'  => 'title',
			'desc'  => __( 'This section lets you set different currency for each payment gateway.', 'woocommerce-jetpack' ),
			'id'    => 'wcj_payment_gateways_currency_options'
		);

		$currency_from = get_woocommerce_currency();

		global $woocommerce;
		$available_gateways = $woocommerce->payment_gateways->payment_gateways();
		foreach ( $available_gateways as $key => $gateway ) {

			$currency_to = get_option( 'wcj_gateways_currency_' . $key, get_woocommerce_currency() );
			$custom_attributes = array(
				'currency_from'        => $currency_from,
				'currency_to'          => $currency_to,
				'multiply_by_field_id' => 'wcj_gateways_currency_exchange_rate_' . $key,
			);
			if ( $currency_from == $currency_to ) $custom_attributes['disabled'] = 'disabled';

			$settings = array_merge( $settings, array(

				array(
					'title'     => $gateway->title,
//					'desc'      => __( 'currency', 'woocommerce-jetpack' ),
					'id'        => 'wcj_gateways_currency_' . $key,
					'default'   => get_woocommerce_currency(),
					'type'      => 'select',
					'options'   => wcj_get_currencies_names_and_symbols(),
				),

				array(
					'title'                    => '',
//					'desc'                     => __( 'exchange rate', 'woocommerce-jetpack' ) . ' ' . $currency_from . ' / ' . $currency_to,
					'id'                       => 'wcj_gateways_currency_exchange_rate_' . $key,
					'default'                  => 1,
					'type'                     => 'exchange_rate',
					'custom_attributes'        => array( 'step' => '0.000001', 'min'  => '0', ),
					'custom_attributes_button' => $custom_attributes,
					'css'                      => 'width:100px;',
					'value'                    => $currency_from . '/' . $currency_to,
					'value_title'              => sprintf( __( 'Grab %s rate from Yahoo.com', 'woocommerce-jetpack' ), $currency_from . '/' . $currency_to ),
				),

			) );
		}

		$settings[] = array(
			'title'    => __( 'Exchange Rates Updates', 'woocommerce-jetpack' ),
			'id'       => 'wcj_gateways_currency_exchange_rate_update_auto',
			'default'  => 'manual',
			'type'     => 'select',
			'options'  => array(
				'manual' => __( 'Enter Rates Manually', 'woocommerce-jetpack' ),
				'auto'   => __( 'Automatically via Currency Exchange Rates module', 'woocommerce-jetpack' ),
			),
			'desc'     => ( '' == apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ) ) ?
				__( 'Visit', 'woocommerce-jetpack' ) . ' <a href="' . admin_url( 'admin.php?page=wc-settings&tab=jetpack&wcj-cat=prices_and_currencies&section=currency_exchange_rates' ) . '">' . __( 'Currency Exchange Rates module', 'woocommerce-jetpack' ) . '</a>'
				:
				apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
			'custom_attributes'
			           => apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),
		);

		$settings[] = array(
			'type'  => 'sectionend',
			'id'    => 'wcj_payment_gateways_currency_options'
		);

		return $settings;
	}
}

endif;

return new WCJ_Payment_Gateways_Currency();
