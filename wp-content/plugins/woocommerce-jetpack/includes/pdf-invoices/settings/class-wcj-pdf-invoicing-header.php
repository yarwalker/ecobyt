<?php
/**
 * WooCommerce Jetpack PDF Invoicing Header
 *
 * The WooCommerce Jetpack PDF Invoicing Header class.
 *
 * @version  2.3.0
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_PDF_Invoicing_Header' ) ) :

class WCJ_PDF_Invoicing_Header extends WCJ_Module {

	/**
	 * Constructor.
	 *
	 * @version 2.3.0
	 */
	public function __construct() {
		$this->id         = 'pdf_invoicing_header';
		$this->parent_id  = 'pdf_invoicing';
		$this->short_desc = __( 'Header', 'woocommerce-jetpack' );
		$this->desc       = '';
		parent::__construct( 'submodule' );
	}

	/**
	 * get_settings.
	 *
	 * @version 2.3.0
	 */
	function get_settings() {

		$settings = array();
		$invoice_types = wcj_get_invoice_types();
		foreach ( $invoice_types as $invoice_type ) {

			$settings[] = array( 'title' => strtoupper( $invoice_type['desc'] ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_invoicing_' . $invoice_type['id'] . '_header_options' );

			$settings = array_merge( $settings, array(

				array(
					'title'    => __( 'Enable Header', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_header_enabled',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),

				array(
					'title'    => __( 'Header Image', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_header_image',
					'default'  => '',
					'type'     => 'text',
					'css'      => 'width:33%;min-width:300px;',
					'desc'     => __( 'Enter a URL to an image you want to show in the invoice\'s header. Upload your image using the <a href="/wp-admin/media-new.php">media uploader</a>.', 'woocommerce-jetpack' ),
					'desc_tip' => __( 'Leave blank to disable', 'woocommerce-jetpack' ),
				),

				array(
					'title'    => __( 'Header Image Width in mm', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_header_image_width_mm',
					'default'  => 50,
					'type'     => 'number',
				),

				array(
					'title'    => __( 'Header Title', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_header_title_text',
					'default'  => $invoice_type['title'],
					'type'     => 'text',
				),

				array(
					'title'    => __( 'Header Text', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_header_text',
					'default'  => __( 'Company Name', 'woocommerce-jetpack' ),
					'type'     => 'text',
				),

				array(
					'title'    => __( 'Header Text Color', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_header_text_color',
					'default'  => '#cccccc',
					'type'     => 'color',
					'css'      => 'width:6em;',
				),

				array(
					'title'    => __( 'Header Line Color', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_header_line_color',
					'default'  => '#cccccc',
					'type'     => 'color',
					'css'      => 'width:6em;',
				),

				array(
					'title'    => __( 'Header Margin', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_margin_header',
					'default'  => 10,//PDF_MARGIN_HEADER
					'type'     => 'number',
				),
			) );

			$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_invoicing_' . $invoice_type['id'] . '_header_options' );
		}

		return $settings;
	}
}

endif;

return new WCJ_PDF_Invoicing_Header();
