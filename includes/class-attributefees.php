<?php
/**
 * Main AttributeFees functionality class.
 *
 * @package StoreStackAttributeFeesForWooCommerce
 */

declare(strict_types=1);

namespace StoreStackAttributeFeesForWooCommerce;

defined( 'ABSPATH' ) || exit;

/**
 * Class AttributeFees.
 */
class AttributeFees {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( is_admin() && ! wp_doing_ajax() ) {
			$this->create_fees_table();
			$this->initialize_admin();
		} else {
			$this->initialize_frontend();
		}
	}

	/**
	 * Create fees database table via migrations.
	 *
	 * @return void
	 */
	private function create_fees_table(): void {
		Migrations::migrate();
	}

	/**
	 * Initialize admin hooks.
	 *
	 * @return void
	 */
	private function initialize_admin(): void {
		add_action( 'woocommerce_update_product', array( $this, 'woocommerce_update_product' ), 10, 1 );
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_attribute_fees_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'add_attribute_fees_tab_content' ) );
	}

	/**
	 * Initialize frontend hooks.
	 *
	 * @return void
	 */
	private function initialize_frontend(): void {
		add_filter( 'woocommerce_variation_option_name', array( $this, 'variation_option_name' ), 10, 4 );
		add_action( 'woocommerce_before_variations_form', array( $this, 'add_fees_to_product_form' ) );
		add_filter( 'woocommerce_get_item_data', array( $this, 'add_fees_to_cart_item' ), 10, 2 );
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'before_calculate_totals' ), 10, 1 );
		add_filter( 'woocommerce_cart_item_price', array( $this, 'adjust_cart_item_price' ), 10, 2 );
		add_filter( 'woocommerce_show_variation_price', array( $this, 'show_variation_price' ), 10, 2 );
	}

	/**
	 * Add custom attribute fees tab to product data tabs.
	 *
	 * @param array<string, array<string, mixed>> $tabs Product data tabs.
	 * @return array<string, array<string, mixed>>
	 */
	public function add_attribute_fees_tab( array $tabs ): array {
		$tabs['attribute_fees'] = array(
			'label'    => __( 'Attribute Fees', 'storestack-attribute-fees-for-woocommerce' ),
			'target'   => 'attribute_fees_data',
			'class'    => array( 'show_if_variable' ),
			'priority' => 51,
		);
		return $tabs;
	}

	/**
	 * Render content for attribute fees tab.
	 *
	 * @return void
	 */
	public function add_attribute_fees_tab_content(): void {
		$product = $this->get_product();
		if ( empty( $product ) ) {
			return; // Abort if not on product edit screen.
		}

		$product_id = absint( $product->get_id() );
		$attributes = $this->get_product_attributes( $product );
		$fees       = $this->get_all_fees( $product_id );

		if ( empty( $attributes ) ) {
			return;
		} ?>

		<div id="attribute_fees_data" class="panel wc-metaboxes-wrapper hidden">

			<?php foreach ( $attributes as $attribute_key => $attribute ) { ?>
				<?php $attribute_name = $attribute->get_name(); ?>

				<div class="wc-metaboxes">

					<?php
					if ( ! $attribute->is_taxonomy() ) {
						continue;
					}
					?>

					<div data-taxonomy="<?php echo esc_attr( $attribute_name ); ?>" class="woocommerce_attribute wc-metabox closed taxonomy">

						<h3>
							<div class="handlediv" style="pointer-events: none;" aria-label="<?php esc_attr_e( 'Click to toggle', 'woocommerce' ); ?>"></div>
							<strong><?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?></strong>
						</h3>

						<div class="wc-metabox-content" style="padding: 14px;">

							<div style="margin-bottom: 13px;">
								<select id="<?php echo esc_attr( "ssaffw_{$attribute_name}_change_all_select" ); ?>" class="select short">
									<?php foreach ( FeeType::cases() as $case ) { ?>
										<option value="<?php echo esc_attr( (string) $case->value ); ?>"><?php echo esc_html( $case->get_name() ); ?></option>
									<?php } ?>
								</select>
								<button id="<?php echo esc_attr( "ssaffw_{$attribute_name}_change_all_button" ); ?>" class="button" type="button"><?php esc_html_e( 'Change All', 'storestack-attribute-fees-for-woocommerce' ); ?></button>
								<?php echo wc_help_tip( esc_html__( 'Reset all fee types to match the selection.', 'storestack-attribute-fees-for-woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>

							<table class="ssaffw-fees-table <?php echo esc_attr( $attribute_name ); ?> widefat fixed striped">
								<thead>
									<tr>
										<th><?php esc_html_e( 'Option Name', 'storestack-attribute-fees-for-woocommerce' ); ?></th>
										<th><?php esc_html_e( 'Fee', 'storestack-attribute-fees-for-woocommerce' ); ?><?php echo wc_help_tip( esc_html__( 'Enter a fee for the option.', 'storestack-attribute-fees-for-woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
										<th><?php esc_html_e( 'Fee Type', 'storestack-attribute-fees-for-woocommerce' ); ?><?php echo wc_help_tip( esc_html__( 'Select the type of fee for the option.', 'storestack-attribute-fees-for-woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
									</tr>
								</thead>
								<tbody>
									<?php $terms = $attribute->get_terms(); ?>
									<?php if ( $terms ) { ?>
										<?php foreach ( $terms as $term ) { ?>
											<tr>
												<td><?php echo esc_html( $term->name ); ?></td>
												<td>
													<input
														id="<?php echo esc_attr( "attribute_fees[{$attribute_name}][{$term->slug}][fee]" ); ?>"
														name="<?php echo esc_attr( "attribute_fees[{$attribute_name}][{$term->slug}][fee]" ); ?>"
														value="<?php echo esc_attr( (string) ( $fees[ $attribute_name ][ $term->slug ]['fee'] ?? '' ) ); ?>"
														class="short wc_input_price"
														type="number"
														step="0.01">
												</td>
												<td>
													<select
														id="<?php echo esc_attr( "attribute_fees[{$attribute_name}][{$term->slug}][fee_type]" ); ?>"
														name="<?php echo esc_attr( "attribute_fees[{$attribute_name}][{$term->slug}][fee_type]" ); ?>"
														class="select short">
														<?php foreach ( FeeType::cases() as $case ) { ?>
															<option value="<?php echo esc_attr( (string) $case->value ); ?>" <?php selected( intval( $fees[ $attribute_name ][ $term->slug ]['fee_type'] ?? '' ), $case->value ); ?>><?php echo esc_html( $case->get_name() ); ?></option>
														<?php } ?>
													</select>
												</td>
											</tr>
										<?php } ?>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Get current product object.
	 *
	 * @return \WC_Product|null|false
	 */
	private function get_product(): \WC_Product|null|false {
		$product_id = absint( $_GET['post'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$product    = wc_get_product( $product_id );

		return $product;
	}

	/**
	 * Get product attributes.
	 *
	 * @param \WC_Product $product Product instance.
	 * @return array<string, \WC_Product_Attribute>
	 */
	private function get_product_attributes( \WC_Product $product ): array {
		$attributes = $product->get_attributes();

		return $attributes;
	}

	/**
	 * Get all fees for a product.
	 *
	 * @param int $product_id Product ID.
	 * @return array<string, array<string, array{fee: int|float, fee_type: int}>>
	 */
	private function get_all_fees( int $product_id ): array {
		$cache = wp_cache_get( "attribute_fees_{$product_id}" );
		if ( $cache ) {
			return $cache;
		}

		global $wpdb;

		$query   = $wpdb->prepare(
			'SELECT * FROM %i WHERE product_id = %d',
			$wpdb->prefix . Migrations::$table_name,
			$product_id
		);
		$results = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery

		$fees = array();
		foreach ( $results as $row ) {
			$attribute_name = wc_attribute_taxonomy_name_by_id( intval( $row->attribute_id ) );
			$term           = get_term( intval( $row->term_id ) );
			if ( ! $term || is_wp_error( $term ) ) {
				continue;
			}

			$fees[ $attribute_name ][ $term->slug ]['fee']      = $row->fee;
			$fees[ $attribute_name ][ $term->slug ]['fee_type'] = intval( $row->fee_type );
		}

		wp_cache_set( "attribute_fees_{$product_id}", $fees, '', 0 );
		return $fees;
	}

	/**
	 * Save product fees when product is updated.
	 *
	 * @param int|string $product_id Product ID.
	 * @return void
	 */
	public function woocommerce_update_product( int|string $product_id ): void {
		$attribute_fees = wc_clean( isset( $_POST['attribute_fees'] ) ? wp_unslash( $_POST['attribute_fees'] ) : array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing

		$this->save_fees( intval( $product_id ), (array) $attribute_fees );
	}

	/**
	 * Save fees to database.
	 *
	 * @param int                                                                $product_id     Product ID.
	 * @param array<string, array<string, array{fee: int|float, fee_type: int}>> $attribute_fees Attribute fees data.
	 * @return void
	 */
	private function save_fees( int $product_id, array $attribute_fees ): void {
		global $wpdb;

		// Delete existing fees for the product.
		// This is necessary to avoid duplicates and ensure data integrity.
		$wpdb->delete( $wpdb->prefix . Migrations::$table_name, array( 'product_id' => $product_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

		if ( empty( $attribute_fees ) ) {
			// Clear cache if no fees are set and return.
			wp_cache_delete( "attribute_fees_{$product_id}" );
			return;
		}

		$cache = array();
		foreach ( $attribute_fees as $attribute_name => $options ) {
			$attribute_id = wc_attribute_taxonomy_id_by_name( $attribute_name );

			// Filter out empty options.
			$filtered_options = array_filter( $options, fn( $opt ) => ! empty( $opt['fee'] ) && floatval( $opt['fee'] ) !== 0.0 );

			// Prepare data for bulk insert.
			$insert_values = array();
			$placeholders  = array();
			foreach ( $filtered_options as $option_key => $option_values ) {
				$term = get_term_by( 'slug', $option_key, $attribute_name );
				if ( ! $term ) {
					continue;
				}

				// Prepare values for bulk insert.
				$insert_values[] = $product_id;
				$insert_values[] = $attribute_id;
				$insert_values[] = $term->term_id;
				$insert_values[] = intval( $option_values['fee_type'] );
				$insert_values[] = floatval( $option_values['fee'] );
				$placeholders[]  = '(%d, %d, %d, %d, %f)';
			}

			if ( ! empty( $insert_values ) ) {
				// Perform bulk insert.
				$query = $wpdb->prepare(
					'INSERT INTO %i (product_id, attribute_id, term_id, fee_type, fee) VALUES ' . implode( ', ', $placeholders ), // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					array_merge( array( $wpdb->prefix . Migrations::$table_name ), $insert_values )
				);
				$wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
			}

			// Add data to cache.
			$cache[ $attribute_name ] = $filtered_options;
		}

		// Update cache.
		wp_cache_set( "attribute_fees_{$product_id}", $cache, '', 0 );
	}

	/**
	 * Modifies the variation option name to include the formatted fee for display.
	 * Hooked to 'woocommerce_variation_option_name' filter.
	 *
	 * @param string      $term_name      Term name.
	 * @param mixed       $term           Term object.
	 * @param string      $attribute_name Attribute taxonomy name.
	 * @param \WC_Product $product        Product instance.
	 * @return string
	 */
	public function variation_option_name( string $term_name, mixed $term, string $attribute_name, \WC_Product $product ): string {
		if ( ! $term || ! $term instanceof \WP_Term ) {
			return $term_name;
		}

		$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
		$fees       = $this->get_all_fees( $product_id );

		if ( ! isset( $fees[ $attribute_name ][ $term->slug ] ) ) {
			return $term_name;
		}

		$fee_value = $fees[ $attribute_name ][ $term->slug ]['fee'];
		$fee_type  = $fees[ $attribute_name ][ $term->slug ]['fee_type'];

		return $term_name . $this->get_formatted_fee_for_display( $fee_value, $fee_type );
	}

	/**
	 * Adds fees to the product form as a data attribute.
	 * This allows the frontend to access the fees data for calculations.
	 *
	 * @return void
	 */
	public function add_fees_to_product_form(): void {
		global $product;
		if ( empty( $product ) || ! $product->is_type( 'variable' ) ) {
			return;
		}

		$product_id = $product->get_id();
		$fees       = $this->get_all_fees( $product_id );

		if ( empty( $fees ) ) {
			return;
		}

		$html = '<div id="storestack-attribute-fees-for-woocommerce" data-fees="' . esc_js( (string) wp_json_encode( $fees ) ) . '"></div>';

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Force the display of variation price if the product has fees.
	 *
	 * @param bool                 $default_value Default value.
	 * @param \WC_Product_Variable $product       Product instance.
	 * @return bool
	 */
	public function show_variation_price( bool $default_value, \WC_Product_Variable $product ): bool {
		$fees = $this->get_all_fees( $product->get_id() );

		if ( empty( $fees ) ) {
			return $default_value;
		}

		return true;
	}

	/**
	 * Add fees to cart and checkout.
	 *
	 * @param array{key: string, value: mixed} $item_data Item data.
	 * @param array<string, mixed>             $cart_item Cart item data.
	 * @return array{key: string, value: mixed}
	 */
	public function add_fees_to_cart_item( array $item_data, array $cart_item ): array {
		$product = wc_get_product( $cart_item['product_id'] );

		foreach ( $item_data as $key => $option ) {
			$attribute_name = wc_attribute_taxonomy_name( $option['key'] );
			$term           = get_term_by( 'name', $option['value'], $attribute_name );
			if ( ! $term ) {
				continue;
			}

			$term_name = $term->name;

			$item_data[ $key ]['value'] = apply_filters( 'woocommerce_variation_option_name', $term_name, $term, $attribute_name, $product ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		}

		return $item_data;
	}

	/**
	 * Calculate and apply fees to cart items before totals are calculated.
	 * Processes each variation in the cart, applying flat fees and percentage-based fees
	 * according to the selected attribute options and updates the item price to include
	 * the calculated fees.
	 *
	 * @param \WC_Cart $cart Cart instance.
	 * @return void
	 */
	public function before_calculate_totals( \WC_Cart $cart ): void {
		if ( did_action( 'woocommerce_before_calculate_totals' ) > 1 ) {
			return;
		}

		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( ! $cart_item['data']->is_type( 'variation' ) ) {
				continue;
			}

			$product_id = $cart_item['product_id'];
			$fees       = $this->get_all_fees( $product_id );

			if ( empty( $fees ) ) {
				continue;
			}

			$base_price = floatval( $cart_item['data']->get_price( 'edit' ) ); // Stores the original price.
			$price      = $base_price; // $price should be the original price with fees added.

			foreach ( $cart_item['variation'] as $attribute_name => $attribute_value ) {
				$attribute_name = str_replace( 'attribute_', '', $attribute_name );
				$term           = get_term_by( 'slug', $attribute_value, $attribute_name );

				if ( ! $term || ! isset( $fees[ $attribute_name ][ $term->slug ] ) ) {
					continue;
				}

				$fee_value = floatval( $fees[ $attribute_name ][ $term->slug ]['fee'] );
				$fee_type  = $fees[ $attribute_name ][ $term->slug ]['fee_type'];

				if ( FeeType::FLAT->value === $fee_type ) {
					$price += $fee_value;
				} elseif ( FeeType::PERCENTAGE->value === $fee_type ) {
					$price += $base_price * ( $fee_value / 100 );
				} elseif ( FeeType::COMPOUND_PERCENTAGE->value === $fee_type ) {
					$price += $price * ( $fee_value / 100 );
				}
			}

			$final_price = $price > 0 ? $price : 0; // Prevent negative price.
			$cart_item['data']->set_price( $final_price );

			// Set ['ssaffw_price_with_fees'] so it can be accessed later on.
			$cart->cart_contents[ $cart_item_key ]['ssaffw_price_with_fees'] = $final_price;
		}
	}

	/**
	 * Format a fee value for display with appropriate prefix and suffix.
	 *
	 * @param int|float $fee_value Fee value.
	 * @param int       $fee_type  Fee type.
	 * @return string
	 */
	private function get_formatted_fee_for_display( int|float $fee_value, int $fee_type ): string {
		$prefix        = $fee_value > 0 ? '+' : '';
		$formatted_fee = '';
		$suffix        = '';

		if ( FeeType::FLAT->value === $fee_type ) {
			$formatted_fee = wc_price( $fee_value, array( 'in_span' => false ) );
		} else {
			$formatted_fee = wc_format_localized_decimal( (string) $fee_value );
			$suffix        = '%';
		}

		return ' (' . $prefix . $formatted_fee . $suffix . ')';
	}

	/**
	 * Fix inconsistent price display on the mini-cart.
	 *
	 * @param string               $price_html Price HTML.
	 * @param array<string, mixed> $cart_item  Cart item data.
	 * @return string
	 */
	public function adjust_cart_item_price( string $price_html, array $cart_item ): string {
		if ( empty( $cart_item['ssaffw_price_with_fees'] ) ) {
			return $price_html;
		}

		return wc_price( $cart_item['ssaffw_price_with_fees'] );
	}
}
