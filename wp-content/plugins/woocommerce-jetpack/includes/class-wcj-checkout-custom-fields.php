<?php
/**
 * WooCommerce Jetpack Checkout Custom Fields
 *
 * The WooCommerce Jetpack Checkout Custom Fields class.
 *
 * @version 2.3.8
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Checkout_Custom_Fields' ) ) :

class WCJ_Checkout_Custom_Fields extends WCJ_Module {

	/**
	 * Constructor.
	 *
	 * @version 2.3.0
	 */
	function __construct() {

		$this->id         = 'checkout_custom_fields';
		$this->short_desc = __( 'Checkout Custom Fields', 'woocommerce-jetpack' );
		$this->desc       = __( 'Add custom fields to WooCommerce checkout page.', 'woocommerce-jetpack' );
		parent::__construct();

		if ( $this->is_enabled() ) {

			add_filter( 'woocommerce_checkout_fields',                  array( $this, 'add_custom_checkout_fields' ), PHP_INT_MAX );

			add_action( 'woocommerce_admin_billing_fields',             array( $this, 'add_custom_billing_fields_to_admin_order_display' ), PHP_INT_MAX );
			add_action( 'woocommerce_admin_shipping_fields',            array( $this, 'add_custom_shipping_fields_to_admin_order_display' ), PHP_INT_MAX );
			add_action( 'woocommerce_admin_shipping_fields',            array( $this, 'add_custom_order_and_account_fields_to_admin_order_display' ), PHP_INT_MAX );

			add_action( 'woocommerce_order_details_after_order_table',  array( $this, 'add_custom_fields_to_order_display' ), PHP_INT_MAX );
			add_action( 'woocommerce_email_after_order_table',          array( $this, 'add_custom_fields_to_emails' ), PHP_INT_MAX, 2 );

			add_filter( 'woo_ce_order_fields',                          array( $this, 'add_custom_fields_to_store_exporter' ) );
			add_filter( 'woo_ce_order',                                 array( $this, 'add_custom_fields_to_store_exporter_order' ), PHP_INT_MAX, 2 );

			add_action( 'woocommerce_checkout_update_order_meta',       array( $this, 'update_custom_checkout_fields_order_meta' ) );

/* 			add_action( 'wp_enqueue_scripts',                           array( $this, 'enqueue_scripts' ) );
//			add_action( 'wp_head',                                      array( $this, 'add_datepicker_script' ) );
			add_action( 'init',                                         array( $this, 'register_script' ) );
 */
//			add_action( 'woocommerce_order_formatted_shipping_address', array( $this, 'add_custom_shipping_fields_to_formatted_address' ), PHP_INT_MAX, 2 );

//			add_filter( 'woocommerce_form_field_' . 'number',           array( $this, 'woocommerce_form_field_type_number' ), PHP_INT_MAX, 4 );
			add_filter( 'woocommerce_form_field_' . 'text',             array( $this, 'woocommerce_form_field_type_number' ), PHP_INT_MAX, 4 );
		}
	}

	/**
	 * woocommerce_form_field_type_number.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function woocommerce_form_field_type_number( $field, $key, $args, $value ) {
		/* $args['input_class'] = array();
		$args['maxlength'] = '';
		$custom_attributes = array();
		$field = '<input type="number" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" '.$args['maxlength'].' value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';
		return $field; */
		if ( isset( $args['custom_attributes']['display'] ) && 'number' === $args['custom_attributes']['display'] ) {
			$field = str_replace( '<input type="text" ', '<input type="number" ', $field );
		}
		return $field;
	}

	/**
	 * add_custom_fields_to_emails.
	 *
	 * @version 2.3.0
	 */
	function add_custom_fields_to_emails( $order, $sent_to_admin ) {
		if (
			(   $sent_to_admin && 'yes' === get_option( 'wcj_checkout_custom_fields_email_all_to_admin' ) ) ||
			( ! $sent_to_admin && 'yes' === get_option( 'wcj_checkout_custom_fields_email_all_to_customer' ) )
		) {
				$this->add_custom_fields_to_order_display( $order );
		}
	}

	/**
	 * add_custom_fields_to_store_exporter_order.
	 *
	 * @version 2.3.0
	 * @since   2.2.7
	 */
	function add_custom_fields_to_store_exporter_order( $order, $order_id ) {
		$post_meta = get_post_meta( $order_id );
		foreach( $post_meta as $key => $values ) {
			if ( false !== strpos( $key, 'wcj_checkout_field_' ) && isset( $values[0] ) ) {
				if ( false !== strpos( $key, '_label_' ) ) {
					continue;
				}
				$order->$key = isset( $values[0]['value'] ) ? $values[0]['value'] : $values[0];
			}
		}

		return $order;
	}

	/**
	 * add_custom_fields_to_store_exporter.
	 */
	public function add_custom_fields_to_store_exporter( $fields ) {
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_custom_fields_total_number', 1 ) ); $i++ ) {
			if ( 'yes' === get_option( 'wcj_checkout_custom_field_enabled_' . $i ) ) {
				$the_section = get_option( 'wcj_checkout_custom_field_section_' . $i );
				$the_key = 'wcj_checkout_field_' . $i;
				$fields[] = array(
					'name'  => $the_section . '_' . $the_key,
					'label' => get_option( 'wcj_checkout_custom_field_label_' . $i ),
				);
			}
		}
	    return $fields;
	}

	/**
	 * enqueue_scripts.
	 *
	 * @version 2.3.0
	 *
	function enqueue_scripts() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-timepicker' );
		wp_enqueue_script( 'wcj-datepicker', wcj_plugin_url() . '/includes/js/wcj-datepicker.js',
			array( 'jquery' ),
			false,
			true );

		wp_enqueue_style( 'jquery-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( 'wcj-timepicker', wcj_plugin_url() . '/includes/css/jquery.timepicker.min.css' );
	}

	/**
	 * register_script.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 *
	public function register_script() {
		wp_register_script(
			'jquery-ui-timepicker',
			wcj_plugin_url() . '/includes/js/jquery.timepicker.min.js',
			array( 'jquery' ),
			false,
			true
		);
	}

	/**
	 * Convert the php date format string to a js date format
	 *
	public function date_format_php_to_js( $php_date_format ) {
		$date_formats_php_to_js = array(
			'F j, Y' => 'MM dd, yy',
			'Y/m/d'  => 'yy/mm/dd',
			'm/d/Y'  => 'mm/dd/yy',
			'd/m/Y'  => 'dd/mm/yy',
		);
		return isset( $date_formats_php_to_js[ $php_date_format ] ) ? $date_formats_php_to_js[ $php_date_format ] : 'MM dd, yy';
	}

	/**
	 * add_datepicker_script.
	 *
	 * @version 2.3.0
	 *
	public function add_datepicker_script() {
		?>
		<script>
		jQuery(document).ready(function() {
		 jQuery('input[display=\'date\']').datepicker({
		 dateFormat : '<?php echo $this->date_format_php_to_js( get_option( 'date_format' ) ); ?>'
		 });
		});
		jQuery(document).ready(function() {
		 jQuery('input[display=\'time\']').timepicker({
		 timeFormat: 'h:mm:ss p'
		 });
		});
		</script>
		<?php
	}

	/**
	 * add_custom_shipping_fields_to_formatted_address.
	 *
	public function add_custom_shipping_fields_to_formatted_address( $fields, $order ) {
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_custom_fields_total_number', 1 ) ); $i++ ) {
			//if ( 'yes' === get_option( 'wcj_checkout_custom_field_enabled_' . $i ) ) {
				$the_section = get_option( 'wcj_checkout_custom_field_section_' . $i );
				if ( 'shipping' === $the_section ) {
					$option_name = $the_section . '_' . 'wcj_checkout_field_' . $i;
					$fields[ $option_name ] = get_post_meta( $order->id, '_' . $option_name, true );
				}
			//}
		}
		return $fields;
	}

	/**
	 * update_custom_checkout_fields_order_meta.
	 *
	 * @version 2.3.8
	 */
	public function update_custom_checkout_fields_order_meta( $order_id ) {
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_custom_fields_total_number', 1 ) ); $i++ ) {
			if ( 'yes' === get_option( 'wcj_checkout_custom_field_enabled_' . $i ) ) {
				$the_section = get_option( 'wcj_checkout_custom_field_section_' . $i );
				$the_type = get_option( 'wcj_checkout_custom_field_type_' . $i );
				$option_name       = $the_section . '_' . 'wcj_checkout_field_'       . $i;
				$option_name_label = $the_section . '_' . 'wcj_checkout_field_label_' . $i;
				$option_name_type  = $the_section . '_' . 'wcj_checkout_field_type_'  . $i;
				if ( ! empty( $_POST[ $option_name ] ) || 'checkbox' === $the_type ) {
					update_post_meta( $order_id, '_' . $option_name_type,  $the_type );
					update_post_meta( $order_id, '_' . $option_name_label, get_option( 'wcj_checkout_custom_field_label_' . $i ) );
					if ( 'checkbox' === $the_type ) {
						$the_value = ( isset( $_POST[ $option_name ] ) ) ? 1 : 0;
						update_post_meta( $order_id, '_' . $option_name, $the_value );
						$option_name_checkbox_value  = $the_section . '_' . 'wcj_checkout_field_checkbox_value_' . $i;
						$checkbox_value = ( 1 == $the_value ) ?
							get_option( 'wcj_checkout_custom_field_checkbox_yes_' . $i ) :
							get_option( 'wcj_checkout_custom_field_checkbox_no_' . $i );
						update_post_meta( $order_id, '_' . $option_name_checkbox_value, $checkbox_value );
					} else {
						update_post_meta( $order_id, '_' . $option_name,       wc_clean( $_POST[ $option_name ] ) );
					}
				}
			}
		}
	}

	/**
	 * add_custom_fields_to_order_display.
	 *
	 * @version 2.3.8
	 * @since   2.3.0
	 */
	function add_custom_fields_to_order_display( $order ) {
		$post_meta = get_post_meta( $order->id );
		foreach( $post_meta as $key => $values ) {

			if ( false !== strpos( $key, 'wcj_checkout_field_' ) && isset( $values[0] ) ) {

				if (
					false !== strpos( $key, '_label_' ) ||
					false !== strpos( $key, '_type_' ) ||
					false !== strpos( $key, '_checkbox_value_' )
				) {
					continue;
				}

				$output = '';

				$the_label_key = str_replace( 'wcj_checkout_field_', 'wcj_checkout_field_label_', $key );
				if ( isset( $post_meta[ $the_label_key ][0] ) ) {
					$output .= $post_meta[ $the_label_key ][0] . ': ';
				} elseif ( is_array( $values[0] ) && isset( $values[0]['label'] ) ) {
					$output .= $values[0]['label'] . ': ';
					// TODO convert from before version 2.3.0
				}

				$the_value = ( is_array( $values[0] ) && isset( $values[0]['value'] ) ) ? $values[0]['value'] : $values[0];

				$the_type_key = str_replace( 'wcj_checkout_field_', 'wcj_checkout_field_type_', $key );
				if ( isset( $post_meta[ $the_type_key ][0] ) && 'checkbox' === $post_meta[ $the_type_key ][0] ) {
					$the_checkbox_value_key = str_replace( 'wcj_checkout_field_', 'wcj_checkout_field_checkbox_value_', $key );
					$output .= ( isset( $post_meta[ $the_checkbox_value_key ][0] ) ) ? $post_meta[ $the_checkbox_value_key ][0] : $the_value;
				} else {
					$output .= $the_value;
				}

				if ( '' != $output ) echo $output . '<br>';
			}
		}
	}

	/**
	 * add_woocommerce_admin_fields.
	 *
	 * @version 2.3.8
	 */
	public function add_woocommerce_admin_fields( $fields, $section ) {
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_custom_fields_total_number', 1 ) ); $i++ ) {
			if ( 'yes' === get_option( 'wcj_checkout_custom_field_enabled_' . $i ) ) {
				$the_type = get_option( 'wcj_checkout_custom_field_type_' . $i );
				if ( 'datepicker' === $the_type || 'timepicker' === $the_type || 'number' === $the_type ) {
					$the_type = 'text';
				}
				if ( 'checkbox' === $the_type || 'select' === $the_type || 'radio' === $the_type ) {
					$the_type = 'text';
				}
				$the_section = get_option( 'wcj_checkout_custom_field_section_' . $i );
				if ( $section != $the_section ) continue;
				$the_key = 'wcj_checkout_field_' . $i;
				$the_key_label = 'wcj_checkout_field_label_' . $i;
				$the_meta = get_post_meta( get_the_ID(), '_' . $section . '_' . $the_key, true );
				if ( is_array( $the_meta ) ) {
					// Converting from before version 2.3.0
					if ( isset( $the_meta['value'] ) ) update_post_meta( get_the_ID(), '_' . $section . '_' . $the_key,       $the_meta['value'] );
					if ( isset( $the_meta['label'] ) ) update_post_meta( get_the_ID(), '_' . $section . '_' . $the_key_label, $the_meta['label'] );
					// TODO section?
				}
				$fields[ $the_key ] = array(
					'type'  => $the_type,
					'label' => ( '' != get_post_meta( get_the_ID(), '_' . $section . '_' . $the_key_label, true ) ) ?
						get_post_meta( get_the_ID(), '_' . $section . '_' . $the_key_label, true ) :
						get_option( 'wcj_checkout_custom_field_label_' . $i ),
					'show'  => true,
				);
			}
		}
		return $fields;
	}

	/**
	 * add_custom_billing_fields_to_admin_order_display.
	 */
	public function add_custom_billing_fields_to_admin_order_display( $fields ) {
		return $this->add_woocommerce_admin_fields( $fields, 'billing' );
	}

	/**
	 * add_custom_shipping_fields_to_admin_order_display.
	 */
	public function add_custom_shipping_fields_to_admin_order_display( $fields ) {
		return $this->add_woocommerce_admin_fields( $fields, 'shipping' );
	}

	/**
	 * add_custom_order_and_account_fields_to_admin_order_display
	 */
	public function add_custom_order_and_account_fields_to_admin_order_display( $fields ) {
		$fields = $this->add_woocommerce_admin_fields( $fields, 'order' );
		$fields = $this->add_woocommerce_admin_fields( $fields, 'account' );
		return $fields;
	}

	/**
	 * add_custom_checkout_fields.
	 *
	 * @version 2.3.8
	 */
	public function add_custom_checkout_fields( $fields ) {

		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_custom_fields_total_number', 1 ) ); $i++ ) {

			if ( 'yes' === get_option( 'wcj_checkout_custom_field_enabled_' . $i ) ) {

				$categories_in = get_option( 'wcj_checkout_custom_field_categories_in_' . $i );

				if ( ! empty( $categories_in ) ) {
					$do_skip = true;
					foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
						$product_categories = get_the_terms( $values['product_id'], 'product_cat' );
						if ( empty( $product_categories ) ) continue;
						foreach( $product_categories as $product_category ) {
							if ( in_array( $product_category->term_id, $categories_in ) ) {
								$do_skip = false;
								break;
							}
						}
						if ( ! $do_skip ) break;
					}
					if ( $do_skip ) continue;
				}

				$the_type = get_option( 'wcj_checkout_custom_field_type_' . $i );
				$custom_attributes = array();
				if ( 'datepicker' === $the_type || 'timepicker' === $the_type || 'number' === $the_type ) {
					if ( 'datepicker' === $the_type || 'timepicker' === $the_type ) {
						$custom_attributes['display'] = ( 'datepicker' === $the_type ) ? 'date' : 'time';
					} else/* if ( 'number' === $the_type ) */ {
						$custom_attributes['display'] = $the_type;
					}
					$the_type = 'text';
				}
				$the_section = get_option( 'wcj_checkout_custom_field_section_' . $i );
				$the_key = 'wcj_checkout_field_' . $i;

				$the_field = array(
					'type'              => $the_type,
					'label'             => get_option( 'wcj_checkout_custom_field_label_' . $i ),
					'placeholder'       => get_option( 'wcj_checkout_custom_field_placeholder_' . $i ),
					'required'          => ( 'yes' === get_option( 'wcj_checkout_custom_field_required_' . $i ) ) ? true : false,
					'custom_attributes' => $custom_attributes,
					'clear'             => ( 'yes' === get_option( 'wcj_checkout_custom_field_clear_' . $i ) ) ? true : false,
					'class'             => array( get_option( 'wcj_checkout_custom_field_class_' . $i ), ),
				);

				if ( 'select' === $the_type || 'radio' === $the_type ) {
					$select_options_raw = get_option( 'wcj_checkout_custom_field_select_options_' . $i );
					$select_options = wcj_get_select_options( $select_options_raw );
					if ( 'select' === $the_type ) {
						$placeholder = get_option( 'wcj_checkout_custom_field_placeholder_' . $i );
						if ( '' != $placeholder ) {
							$select_options = array_merge( array( '' => $placeholder ), $select_options );
						}
					}
					$the_field['options'] = $select_options;
					if ( ! empty( $select_options ) ) {
						reset( $select_options );
						$the_field['default'] = key( $select_options );
					}
				}

				if ( 'checkbox' === $the_type ) {
					$the_field['default'] = ( 'yes' === get_option( 'wcj_checkout_custom_field_checkbox_default_' . $i ) ) ? 1 : 0;
				}

				$fields[ $the_section ][ $the_section . '_' . $the_key ] = $the_field;
			}
		}
		return $fields;
	}

	/**
	 * get_settings.
	 *
	 * @version 2.3.8
	 */
	public function get_settings() {

		$settings = array(

			array(
				'title'    => __( 'Checkout Custom Fields Options', 'woocommerce-jetpack' ),
				'type'     => 'title',
				'desc'     => '',//__( 'This section lets you add custom checkout fields.', 'woocommerce-jetpack' ),
				'id'       => 'wcj_checkout_custom_fields_options',
			),

			array(
				'title'     => __( 'Add All Fields to Admin Emails', 'woocommerce-jetpack' ),
				'desc'      => __( 'Enable', 'woocommerce-jetpack' ),
				'id'        => 'wcj_checkout_custom_fields_email_all_to_admin',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),

			array(
				'title'     => __( 'Add All Fields to Customers Emails', 'woocommerce-jetpack' ),
				'desc'      => __( 'Enable', 'woocommerce-jetpack' ),
				'id'        => 'wcj_checkout_custom_fields_email_all_to_customer',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),

			array( 'type'  => 'sectionend', 'id' => 'wcj_checkout_custom_fields_options' ),

			array(
				'title'    => __( 'The Fields', 'woocommerce-jetpack' ),
				'type'     => 'title',
				'id'       => 'wcj_checkout_custom_fields_individual_options',
			),

			array(
				'title'     => __( 'Custom Fields Number', 'woocommerce-jetpack' ),
				'desc_tip'  => __( 'Click "Save changes" after you change this number.', 'woocommerce-jetpack' ),
				'id'        => 'wcj_checkout_custom_fields_total_number',
				'default'   => 1,
				'type'      => 'custom_number',
				'desc'      => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'
				            => array_merge(
								is_array( apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ) ) ? apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ) : array(),
								array(
									'step' => '1',
									'min'  => '1',
								)
							),
				'css'       => 'width:100px;',
			),
		);

		$product_cats = array();
		$product_categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
		if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ){
			foreach ( $product_categories as $product_category ) {
				$product_cats[ $product_category->term_id ] = $product_category->name;
			}
		}

		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_checkout_custom_fields_total_number', 1 ) ); $i++ ) {
			$settings = array_merge( $settings,
				array(
					array(
						'title'     => __( 'Custom Field', 'woocommerce-jetpack' ) . ' #' . $i,
						'desc'      => __( 'enabled', 'woocommerce-jetpack' ),
						'id'        => 'wcj_checkout_custom_field_enabled_' . $i,
						'default'   => 'no',
						'type'      => 'checkbox',
						'css'       => 'min-width:300px;width:50%;',
					),
					array(
						'title'     => '',
						'desc'      => __( 'type', 'woocommerce-jetpack' ),
						'id'        => 'wcj_checkout_custom_field_type_' . $i,
						'default'   => 'text',
						'type'      => 'select',
						'options'     => array(
							'text'       => __( 'Text', 'woocommerce-jetpack' ),
							'textarea'   => __( 'Textarea', 'woocommerce-jetpack' ),
							'number'     => __( 'Number', 'woocommerce-jetpack' ),
							'checkbox'   => __( 'Checkbox', 'woocommerce-jetpack' ),
//							'file'       => __( 'File', 'woocommerce-jetpack' ),
							'datepicker' => __( 'Datepicker', 'woocommerce-jetpack' ),
							'timepicker' => __( 'Timepicker', 'woocommerce-jetpack' ),
							'select'     => __( 'Select', 'woocommerce-jetpack' ),
							'radio'      => __( 'Radio', 'woocommerce-jetpack' ),
							'password'   => __( 'Password', 'woocommerce-jetpack' ),
							'country'    => __( 'Country', 'woocommerce-jetpack' ),
							'state'      => __( 'State', 'woocommerce-jetpack' ),
							'email'      => __( 'Email', 'woocommerce-jetpack' ),
							'tel'        => __( 'Phone', 'woocommerce-jetpack' ),
						),
						'css'       => 'min-width:300px;width:50%;',
					),
					array(
						'title'     => '',
						'desc'      => __( 'options (only if "select" or "radio" type is selected). One option per line', 'woocommerce-jetpack' ),
						'id'        => 'wcj_checkout_custom_field_select_options_' . $i,
						'default'   => '',
						'type'      => 'textarea',
						'css'       => 'min-width:300px;width:50%;',
					),
					array(
						'title'     => '',
						'id'        => 'wcj_checkout_custom_field_checkbox_yes_' . $i,
						'desc'      => __( 'If checkbox is selected, set value for ON here', 'woocommerce-jetpack' ),
						'type'      => 'text',
						'default'   => __( 'Yes', 'woocommerce-jetpack' ),
						'css'       => 'min-width:300px;width:50%;',
					),
					array(
						'title'     => '',
						'id'        => 'wcj_checkout_custom_field_checkbox_no_' . $i,
						'desc'      => __( 'If checkbox is selected, set value for OFF here', 'woocommerce-jetpack' ),
						'type'      => 'text',
						'default'   => __( 'No', 'woocommerce-jetpack' ),
						'css'       => 'min-width:300px;width:50%;',
					),
					array(
						'title'     => '',
						'id'        => 'wcj_checkout_custom_field_checkbox_default_' . $i,
						'desc'      => __( 'If checkbox is selected, set default value here', 'woocommerce-jetpack' ),
						'type'      => 'select',
						'default'   => 'no',
						'options'   => array(
							'no'  => __( 'Not Checked', 'woocommerce-jetpack' ),
							'yes' => __( 'Checked', 'woocommerce-jetpack' ),
						),
						'css'       => 'min-width:300px;width:50%;',
					),
					array(
						'title'     => '',
						'desc'      => __( 'required', 'woocommerce-jetpack' ),
						'id'        => 'wcj_checkout_custom_field_required_' . $i,
						'default'   => 'no',
						'type'      => 'checkbox',
						'css'       => 'min-width:300px;width:50%;',
					),
					array(
						'title'     => '',
						'desc'      => __( 'label', 'woocommerce-jetpack' ),
						'id'        => 'wcj_checkout_custom_field_label_' . $i,
						'default'   => '',
						'type'      => 'textarea',
						'css'       => 'min-width:300px;width:50%;',
					),
					/*array(
						'title'     => '',
						'desc'      => __( 'for datepicker: min days', 'woocommerce-jetpack' ),
						'id'        => 'wcj_checkout_custom_field_datepicker_mindays_' . $i,
						'default'   => 0,
						'type'      => 'number',
					),
					array(
						'title'     => '',
						'desc'      => __( 'for datepicker: max days', 'woocommerce-jetpack' ),
						'id'        => 'wcj_checkout_custom_field_datepicker_maxdays_' . $i,
						'default'   => 0,
						'type'      => 'number',
					),*/
					array(
						'title'     => '',
						'desc'      => __( 'placeholder', 'woocommerce-jetpack' ),
						'id'        => 'wcj_checkout_custom_field_placeholder_' . $i,
						'default'   => '',
						'type'      => 'textarea',
						'css'       => 'min-width:300px;width:50%;',
					),

					array(
						'title'        => '',
						'desc'        => __( 'section', 'woocommerce-jetpack' ),
						'id'           => 'wcj_checkout_custom_field_section_' . $i,
						'default'      => 'billing',
						'type'      => 'select',
						'options'     => array(
							'billing'   => __( 'Billing', 'woocommerce-jetpack' ),
							'shipping'  => __( 'Shipping', 'woocommerce-jetpack' ),
							'order'     => __( 'Order Notes', 'woocommerce-jetpack' ),
							'account'   => __( 'Account', 'woocommerce-jetpack' ),
						),
						'css'       => 'min-width:300px;width:50%;',
					),

					array(
						'title'     => '',
						'desc'      => __( 'class', 'woocommerce-jetpack' ),
						'id'        => 'wcj_checkout_custom_field_class_' . $i,
						'default'   => 'form-row-wide',
						'type'      => 'select',
						'options'     => array(
							'form-row-wide'  => __( 'Wide', 'woocommerce-jetpack' ),
							'form-row-first' => __( 'First', 'woocommerce-jetpack' ),
							'form-row-last'  => __( 'Last', 'woocommerce-jetpack' ),
						),
						'css'       => 'min-width:300px;width:50%;',
					),

					array(
						'title'     => '',
						'desc'      => __( 'clear', 'woocommerce-jetpack' ),
						'id'        => 'wcj_checkout_custom_field_clear_' . $i,
						'default'   => 'yes',
						'type'      => 'checkbox',
						'css'       => 'min-width:300px;width:50%;',
					),

					array(
						'title'     => '',
						'desc'      => __( 'categories', 'woocommerce-jetpack' ),
						'desc_tip'  => __( '', 'woocommerce-jetpack' ),
						'id'        => 'wcj_checkout_custom_field_categories_in_' . $i,
						'default'   => '',
						'type'      => 'multiselect',
						'class'     => 'chosen_select',
						'css'       => 'min-width:300px;width:50%;',
						'options'   => $product_cats,
					),

				)
			);
		}

		$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_checkout_custom_fields_individual_options' );

		return $this->add_enable_module_setting( $settings );
	}

}

endif;

return new WCJ_Checkout_Custom_Fields();
