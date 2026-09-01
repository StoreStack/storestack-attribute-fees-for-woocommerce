<?php
/**
 * FeeType Enum.
 *
 * @package StoreStackAttributeFeesForWooCommerce
 */

declare(strict_types=1);

namespace StoreStackAttributeFeesForWooCommerce;

defined( 'ABSPATH' ) || exit;

/**
 * Enum FeeType.
 */
enum FeeType: int {

	case FLAT                = 0;
	case PERCENTAGE          = 1;
	case COMPOUND_PERCENTAGE = 2;

	/**
	 * Get the human-readable name of the fee type.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return match ( $this ) {
			self::FLAT                 => __( 'Flat', 'storestack-attribute-fees-for-woocommerce' ),
			self::PERCENTAGE           => __( 'Percentage', 'storestack-attribute-fees-for-woocommerce' ),
			self::COMPOUND_PERCENTAGE  => __( 'Compound Percentage', 'storestack-attribute-fees-for-woocommerce' ),
		};
	}
}
