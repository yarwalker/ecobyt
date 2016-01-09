<?php
/**
 * WooCommerce Jetpack Product Images
 *
 * The WooCommerce Jetpack Product Images class.
 *
 * @version 2.3.9
 * @since   2.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Product_Images' ) ) :

class WCJ_Product_Images extends WCJ_Module {

	/**
	 * Constructor.
	 *
	 * @version 2.2.6
	 */
	function __construct() {

		$this->id         = 'product_images';
		$this->short_desc = __( 'Product Images', 'woocommerce-jetpack' );
		$this->desc       = __( 'Customize WooCommerce products images, thumbnails and sale flashes.', 'woocommerce-jetpack' );
		parent::__construct();

		if ( $this->is_enabled() ) {

			// Product Image & Thumbnails
			if ( 'yes' === get_option( 'wcj_product_images_and_thumbnails_enabled', 'no' ) ) {

				// Single
				if ( 'yes' === get_option( 'wcj_product_images_and_thumbnails_hide_on_single', 'no' ) ) {
					add_action( 'init', array( $this, 'product_images_and_thumbnails_hide_on_single' ), PHP_INT_MAX );
				} else {
					add_filter( 'woocommerce_single_product_image_html',           array( $this, 'customize_single_product_image_html' ), PHP_INT_MAX, 2 );
					add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'customize_single_product_image_thumbnail_html' ) );
				}

				// Archives
				add_action( 'woocommerce_before_shop_loop_item',       array( $this, 'product_images_hide_on_archive' ) );
				add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'customize_archive_product_image_html' ), 10 );

				// Single Product Thumbnails Columns Number
				add_filter( 'woocommerce_product_thumbnails_columns', array( $this, 'change_product_thumbnails_columns_number' ) );
			}

			add_action( 'add_meta_boxes',    array( $this, 'add_meta_box' ) );
			add_action( 'save_post_product', array( $this, 'save_meta_box' ), PHP_INT_MAX, 2 );

			// Sale flash
			if ( 'yes' === get_option( 'wcj_product_images_sale_flash_enabled', 'no' ) ) {
				add_filter( 'woocommerce_sale_flash', array( $this, 'customize_sale_flash' ), PHP_INT_MAX, 3 );
			}
		}
	}

	/**
	 * customize_archive_product_image_html.
	 *
	 * @since 2.2.6
	 */
	function customize_archive_product_image_html() {
		if ( ( $post_id = get_the_ID() ) > 0 && '' != get_post_meta( $post_id, '_' . 'wcj_product_images_meta_custom_on_archives', true ) ) {
			echo get_post_meta( $post_id, '_' . 'wcj_product_images_meta_custom_on_archives', true );
		} elseif ( '' != get_option( 'wcj_product_images_custom_on_archives', '' ) ) {
			echo get_option( 'wcj_product_images_custom_on_archives' );
		}
	}

	/**
	 * get_meta_box_options.
	 *
	 * @since 2.2.6
	 */
	function get_meta_box_options() {
		return array(
			array(
				'name'    => 'wcj_product_images_meta_custom_on_single',
				'default' => '',
				'type'    => 'textarea',
				'title'   => __( 'Replace image with custom HTML on single product page', 'woocommerce-jetpack' ),
			),
			array(
				'name'    => 'wcj_product_images_meta_custom_on_archives',
				'default' => '',
				'type'    => 'textarea',
				'title'   => __( 'Replace image with custom HTML on archives', 'woocommerce-jetpack' ),
			),
		);
	}

	/**
	 * product_images_and_thumbnails_hide_on_single.
	 */
	function product_images_and_thumbnails_hide_on_single() {
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
	}

	/**
	 * product_images_hide_on_archive.
	 *
	 * @version 2.2.6
	 */
	function product_images_hide_on_archive() {
		if (
			'yes' === get_option( 'wcj_product_images_hide_on_archive', 'no' ) ||
			'' != get_option( 'wcj_product_images_custom_on_archives', '' ) ||
			( ( $post_id = get_the_ID() ) > 0 && '' != get_post_meta( $post_id, '_' . 'wcj_product_images_meta_custom_on_archives', true ) )
		) {

			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

		}
	}

	/**
	 * customize_sale_flash.
	 */
	function customize_sale_flash( $sale_flash_html, $post, $product ) {

		// Hiding
		if ( 'yes' === get_option( 'wcj_product_images_sale_flash_hide_on_archives', 'no' ) && is_archive() ) return '';
		if ( 'yes' === get_option( 'wcj_product_images_sale_flash_hide_on_single', 'no' )   && is_single() && get_the_ID() === $product->id ) return '';

		// Content
		return do_shortcode(
			get_option( 'wcj_product_images_sale_flash_html' ,
			'<span class="onsale">' . __( 'Sale!', 'woocommerce' ) . '</span>' )
		);

	}

	/**
	 * customize_single_product_image_html.
	 *
	 * @version 2.2.6
	 */
	function customize_single_product_image_html( $image_link, $post_id ) {

		if ( '' != get_post_meta( $post_id, '_' . 'wcj_product_images_meta_custom_on_single', true ) ) {
			return get_post_meta( $post_id, '_' . 'wcj_product_images_meta_custom_on_single', true );
		} elseif ( '' != get_option( 'wcj_product_images_custom_on_single', '' ) ) {
			return get_option( 'wcj_product_images_custom_on_single' );
		}
		return ( 'yes' === get_option( 'wcj_product_images_hide_on_single', 'no' ) ) ? '' : $image_link;
	}

	/**
	 * customize_single_product_image_thumbnail_html.
	 *
	 * @version 2.2.6
	 */
	function customize_single_product_image_thumbnail_html( $image_link ) {
		if ( '' != get_option( 'wcj_product_images_thumbnails_custom_on_single', '' ) ) {
			return get_option( 'wcj_product_images_thumbnails_custom_on_single' );
		}
		return ( 'yes' === get_option( 'wcj_product_images_thumbnails_hide_on_single', 'no' ) ) ? '' : $image_link;
	}

	/**
	 * change_product_thumbnails_columns.
	 */
	function change_product_thumbnails_columns_number( $columns_number ) {
		return get_option( 'wcj_product_images_thumbnails_columns', 3 );
	}

	/**
	 * get_settings.
	 *
	 * @version 2.3.9
	 */
	function get_settings() {

		$settings = array(

			array( 'title' => __( 'Product Image and Thumbnails', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_product_images_and_thumbnails_options' ),

			array(
				'title'   => __( 'Enable Section', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_and_thumbnails_enabled',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Image and Thumbnails on Single', 'woocommerce-jetpack' ),
				'desc'    => __( 'Hide', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_and_thumbnails_hide_on_single',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Replace Image on Single', 'woocommerce-jetpack' ),
				'desc'    => __( 'Replace image on single product page with custom HTML. Leave blank to disable.', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_custom_on_single',
				'default' => '',
				'type'    => 'textarea',
				'css'     => 'width:66%; min-width:300px;',
			),

			array(
				'title'   => __( 'Replace Thumbnails on Single', 'woocommerce-jetpack' ),
				'desc'    => __( 'Replace thumbnails on single product page with custom HTML. Leave blank to disable.', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_thumbnails_custom_on_single',
				'default' => '',
				'type'    => 'textarea',
				'css'     => 'width:66%; min-width:300px;',
			),

			array(
				'title'   => __( 'Image on Single', 'woocommerce-jetpack' ),
				'desc'    => __( 'Hide', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_hide_on_single',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Thumbnails on Single', 'woocommerce-jetpack' ),
				'desc'    => __( 'Hide', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_thumbnails_hide_on_single',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Replace Image on Archive', 'woocommerce-jetpack' ),
				'desc'    => __( 'Replace image on archive pages with custom HTML. Leave blank to disable.', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_custom_on_archives',
				'default' => '',
				'type'    => 'textarea',
				'css'     => 'width:66%; min-width:300px;',
			),

			array(
				'title'   => __( 'Image on Archives', 'woocommerce-jetpack' ),
				'desc'    => __( 'Hide', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_hide_on_archive',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Single Product Thumbnails Columns', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_thumbnails_columns',
				'default' => 3,
				'type'    => 'number',
			),

			array( 'type' => 'sectionend', 'id' => 'wcj_product_images_and_thumbnails_options' ),

			array( 'title' => __( 'Product Images Sale Flash', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_product_images_sale_flash_options' ),

			array(
				'title'   => __( 'Enable Section', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_sale_flash_enabled',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'HTML', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_sale_flash_html',
				'default' => '<span class="onsale">' . __( 'Sale!', 'woocommerce' ) . '</span>',
				'type'    => 'textarea',
				'css'     => 'width:300px;height:100px;',
			),

			array(
				'title'   => __( 'Hide on Archives (Categories)', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_sale_flash_hide_on_archives',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Hide on Single', 'woocommerce-jetpack' ),
				'id'      => 'wcj_product_images_sale_flash_hide_on_single',
				'default' => 'no',
				'type'    => 'checkbox',
			),

			array( 'type' => 'sectionend', 'id' => 'wcj_product_images_sale_flash_options' ),
		);

		return $this->add_enable_module_setting( $settings );
	}
}

endif;

return new WCJ_Product_Images();
