<?php
/**
 * WooCommerce Jetpack PDF Invoicing
 *
 * The WooCommerce Jetpack PDF Invoicing class.
 *
 * @version 2.3.10
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_PDF_Invoicing' ) ) :

class WCJ_PDF_Invoicing extends WCJ_Module {

	/**
	 * Constructor.
	 *
	 * @version 2.3.10
	 */
	public function __construct() {

		$this->id            = 'pdf_invoicing';
		$this->short_desc    = __( 'PDF Invoicing', 'woocommerce-jetpack' );
		$this->section_title = __( 'General', 'woocommerce-jetpack' );
		$this->desc          = __( 'WooCommerce Invoices, Proforma Invoices, Credit Notes and Packing Slips.', 'woocommerce-jetpack' );
		parent::__construct();

		$this->add_tools( array(
			'renumerate_invoices' => array(
				'title' => __( 'Invoices Renumerate', 'woocommerce-jetpack' ),
				'desc'  => __( 'Tool renumerates all invoices, proforma invoices, credit notes and packing slips.', 'woocommerce-jetpack' ),
			),
			'invoices_report' => array(
				'title' => __( 'Invoices Report', 'woocommerce-jetpack' ),
				'desc'  => __( 'Invoices Monthly Reports.', 'woocommerce-jetpack' ),
			),
		) );

		if ( $this->is_enabled() ) {

			add_action( 'init', array( $this, 'catch_args' ) );
			add_action( 'init', array( $this, 'generate_pdf_on_init' ) );

			$this->the_pdf_invoicing_report_tool = include_once( 'pdf-invoices/class-wcj-pdf-invoicing-report-tool.php' );

//			define ( 'K_PATH_IMAGES', $_SERVER['DOCUMENT_ROOT'] );

			$invoice_types = wcj_get_enabled_invoice_types();
			foreach ( $invoice_types as $invoice_type ) {
				$the_hook = get_option( 'wcj_invoicing_' . $invoice_type['id'] . '_create_on', 'woocommerce_new_order' );
				if ( 'disabled' != $the_hook && 'manual' != $the_hook && '' != $the_hook ) {
					add_action( $the_hook, array( $this, 'create_' . $invoice_type['id'] ) );
				}
			}

			/* $this->meta_box_screen   = 'shop_order';
			$this->meta_box_context  = 'side';
			$this->meta_box_priority = 'default';
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) ); */
		}
	}

	/**
	 * create_invoices_report_tool.
	 *
	 * @version 2.3.10
	 * @since   2.3.10
	 */
	function create_invoices_report_tool() {
		//$the_tool = include_once( 'pdf-invoices/class-wcj-pdf-invoicing-report-tool.php' );
		return $this->the_pdf_invoicing_report_tool->create_invoices_report_tool();
	}

	/**
	 * create_renumerate_invoices_tool.
	 *
	 * @version 2.3.10
	 * @since   2.3.10
	 */
	function create_renumerate_invoices_tool() {
		$the_tool = include_once( 'pdf-invoices/class-wcj-pdf-invoicing-renumerate-tool.php' );
		return $the_tool->create_renumerate_invoices_tool();
	}

	/**
	 * create_meta_box.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	/* public function create_meta_box() {

		$current_post_id = get_the_ID();

		$html = '';
		$html .= '<table>';
		$invoice_types = wcj_get_invoice_types();
		foreach ( $invoice_types as $invoice_type ) {
			$the_hook = get_option( 'wcj_invoicing_' . $invoice_type['id'] . '_create_on', 'woocommerce_new_order' );
			if ( 'disabled' != $the_hook && '' != $the_hook ) {
				$html .= '<tr>';

				$html .= '<th style="text-align:left;">';
				$html .= $invoice_type['title'];
				$html .= '</th>';

				$html .= '<td style="text-align:right;">';
				$html .= '<a href="';
				if ( false == wcj_is_invoice_created( $current_post_id, $invoice_type['id'] ) ) {
					$html .= add_query_arg( array( 'create_invoice_for_order_id' => $current_post_id, 'invoice_type_id' => $invoice_type['id'] ), remove_query_arg( 'delete_invoice_for_order_id' ) );
					$html .= '">' . __( 'Create', 'woocommerce-jetpack' ) . '</a>';
				} else {
					$html .= add_query_arg( array( 'delete_invoice_for_order_id' => $current_post_id, 'invoice_type_id' => $invoice_type['id'] ), remove_query_arg( 'create_invoice_for_order_id' )  );
					$html .= '">' . __( 'Delete', 'woocommerce-jetpack' ) . '</a>';
				}
				$html .= '</td>';

				$html .= '</tr>';
			}
		}
		$html .= '</table>';
		echo $html;
	} */
	/**
	 * create_invoice.
	 */
	function create_invoice( $order_id ) {
		return $this->create_document( $order_id, 'invoice' );
	}

	/**
	 * create_proforma_invoice.
	 */
	function create_proforma_invoice( $order_id ) {
		return $this->create_document( $order_id, 'proforma_invoice' );
	}

	/**
	 * create_packing_slip.
	 */
	function create_packing_slip( $order_id ) {
		return $this->create_document( $order_id, 'packing_slip' );
	}

	/**
	 * create_credit_note.
	 */
	function create_credit_note( $order_id ) {
		return $this->create_document( $order_id, 'credit_note' );
	}

	/**
	 * create_custom_doc.
	 *
	 * @version 2.2.7
	 * @since   2.2.7
	 */
	function create_custom_doc( $order_id ) {
		return $this->create_document( $order_id, 'custom_doc' );
	}
	/**
	 * create_document.
	 */
	function create_document( $order_id, $invoice_type ) {

		if ( false == wcj_is_invoice_created( $order_id, $invoice_type ) ) {
			wcj_create_invoice( $order_id, $invoice_type );
		}
		/*
		if ( '' == get_post_meta( $order_id, '_wcj_invoicing_' . $invoice_type . '_number', true ) ) {
			if ( 'yes' === get_option( 'wcj_invoicing_' . $invoice_type . '_sequential_enabled' ) ) {
				$the_invoice_number = get_option( 'wcj_invoicing_' . $invoice_type . '_numbering_counter', 1 );
				update_option( 'wcj_invoicing_' . $invoice_type . '_numbering_counter', ( $the_invoice_number + 1 ) );
			} else {
				$the_invoice_number = $order_id;
			}
			update_post_meta( $order_id, '_wcj_invoicing_' . $invoice_type . '_number', $the_invoice_number );
			update_post_meta( $order_id, '_wcj_invoicing_' . $invoice_type . '_date', time() );
		}
		*/
	}

	/**
	 * delete_document.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function delete_document( $order_id, $invoice_type ) {
		if ( true == wcj_is_invoice_created( $order_id, $invoice_type ) ) {
			wcj_delete_invoice( $order_id, $invoice_type );
		}
	}
	/**
	 * catch_args.
	 *
	 * @version 2.3.0
	 */
	function catch_args() {
		$this->order_id        = ( isset( $_GET['order_id'] ) )                                             ? $_GET['order_id'] : 0;
		$this->invoice_type_id = ( isset( $_GET['invoice_type_id'] ) )                                      ? $_GET['invoice_type_id'] : '';
		$this->save_as_pdf     = ( isset( $_GET['save_pdf_invoice'] ) && '1' == $_GET['save_pdf_invoice'] ) ? true : false;
		$this->get_invoice     = ( isset( $_GET['get_invoice'] ) && '1' == $_GET['get_invoice'] )           ? true : false;

		if ( isset( $_GET['create_invoice_for_order_id'] ) && ( is_super_admin() || is_shop_manager() ) ) {
			$this->create_document( $_GET['create_invoice_for_order_id'], $this->invoice_type_id );
		}
		if ( isset( $_GET['delete_invoice_for_order_id'] ) && ( is_super_admin() || is_shop_manager() ) ) {
			$this->delete_document( $_GET['delete_invoice_for_order_id'], $this->invoice_type_id );
		}
	}
	/**
	 * generate_pdf_on_init.
	 */
	function generate_pdf_on_init() {

		// Check if all is OK
		if ( ( true !== $this->get_invoice ) ||
			 ( 0 == $this->order_id ) ||
			 ( ! is_user_logged_in() ) ||
			 ( ! current_user_can( 'administrator' ) && ! is_shop_manager() && get_current_user_id() != intval( get_post_meta( $this->order_id, '_customer_user', true ) ) ) )
			return;

		$the_invoice = wcj_get_pdf_invoice( $this->order_id, $this->invoice_type_id );
//		$invoice = new WCJ_PDF_Invoice();
		$dest = ( true === $this->save_as_pdf ) ? 'D' : 'I';
//		$invoice->get_pdf( $this->order_id, $this->invoice_type_id, '', $dest );//, $this->invoice_type_id );
		$the_invoice->get_pdf( $dest );
//		echo $invoice_html;
	}
	/**
	 * get_settings.
	 *
	 * @version 2.3.10
	 */
	function get_settings() {

		$settings = array(
			array( 'title' => __( 'PDF Invoicing General Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_pdf_invoicing_options' ),
		);

		// Hooks Array
		$create_on_array = array();
		$create_on_array['disabled'] = __( 'Disabled', 'woocommerce-jetpack' );
		$create_on_array['woocommerce_new_order'] = __( 'Create on New Order', 'woocommerce-jetpack' );
		$order_statuses = wcj_get_order_statuses( true );
		foreach ( $order_statuses as $status => $desc ) {
			$create_on_array[ 'woocommerce_order_status_' . $status ] = __( 'Create on Order Status', 'woocommerce-jetpack' ) . ' ' . $desc;
		}
		$create_on_array['manual'] = __( 'Manual Only', 'woocommerce-jetpack' );

		// Settings
		$invoice_types = wcj_get_invoice_types();
		foreach ( $invoice_types as $k => $invoice_type ) {
			$settings[] = array(
				'title'    => $invoice_type['title'],
				'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_create_on',
				'default'  => 'disabled',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => $create_on_array,
				'desc'     => ( 0 === $k ) ? '' : apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'
				           => ( 0 === $k ) ? '' : apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),
			);
		}

		$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_pdf_invoicing_options' );

		return $this->add_standard_settings( $settings );
	}
}

endif;

return new WCJ_PDF_Invoicing();
