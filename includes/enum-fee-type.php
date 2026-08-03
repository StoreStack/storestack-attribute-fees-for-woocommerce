<?php

declare(strict_types=1);

namespace StoreStackAttributeFeesForWooCommerce;

defined('ABSPATH') || exit;


enum FeeType: int
{
    case FLAT = 0;
    case PERCENTAGE = 1;
    case PERCENTAGE_COMPOUNDED = 2;

    public function getName(): ?string
    {
        return match ($this) {
            self::FLAT                   => __('Flat', 'storestack-attribute-fees-for-woocommerce'),
            self::PERCENTAGE             => __('Percentage', 'storestack-attribute-fees-for-woocommerce'),
            self::PERCENTAGE_COMPOUNDED  => __('Percentage Compounded', 'storestack-attribute-fees-for-woocommerce'),
            default                      => null
        };
    }
}
