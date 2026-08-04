<?php

declare(strict_types=1);

namespace StoreStackAttributeFeesForWooCommerce;

defined('ABSPATH') || exit;


enum FeeType: int
{
    case FLAT = 0;
    case PERCENTAGE = 1;
    case COMPOUND_PERCENTAGE = 2;

    public function getName(): ?string
    {
        return match ($this) {
            self::FLAT                 => __('Flat', 'storestack-attribute-fees-for-woocommerce'),
            self::PERCENTAGE           => __('Percentage', 'storestack-attribute-fees-for-woocommerce'),
            self::COMPOUND_PERCENTAGE  => __('Compound Percentage', 'storestack-attribute-fees-for-woocommerce'),
            default                    => null
        };
    }
}
