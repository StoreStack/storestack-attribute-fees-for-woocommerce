"use strict";

document.addEventListener('DOMContentLoaded', () => {
    const fees = JSON.parse(document.getElementById('storestack-attribute-fees-for-woocommerce')?.dataset.fees || '{}');
    if (!Object.keys(fees).length) return;


    jQuery(document).on('show_variation', function (event, variation, purchasable) {

        const priceEls = document.querySelectorAll('.woocommerce-variation-price .amount');
        if (priceEls.length < 1) {
            return;
        }

        const selectedAttributes = {};
        document.querySelector('.variations_form').querySelectorAll('select[name^="attribute_"]').forEach((select) => {
            let attributeName = select.name.replace('attribute_', '');
            selectedAttributes[attributeName] = select.value;
        });

        for (const [index, priceHTML] of Object.entries(priceEls)) {
            if (!priceHTML) {
                continue;
            }

            const basePrice = accounting.unformat(priceHTML.textContent, currency_params.decimal_separator);
            let price = basePrice;

            for (const [attr, option] of Object.entries(selectedAttributes)) {
                if (!fees[attr] || !fees[attr][option]) {
                    continue;
                }

                const feeValue = parseFloat(fees[attr][option]['fee']);
                const feeType = parseInt(fees[attr][option]['fee_type']);

                switch (feeType) {
                    case 0: // Flat
                        price += feeValue;
                        break;
                    case 1: // Percentage
                        price += basePrice * (feeValue / 100);
                        break;
                    case 2: // Percentage Compounded
                        price += price * (feeValue / 100);
                        break;
                }

                price = price > 0 ? price : 0; // Prevent negative price

                const formattedPrice = accounting.formatMoney(price, {
                    symbol: '',
                    decimal: currency_params.decimal_separator,
                    thousand: currency_params.thousand_separator,
                    precision: currency_params.decimal_precision,
                    format: currency_params.price_format
                });

                // Replace only the numeric text, preserving <span class="woocommerce-Price-currencySymbol">
                const bdi = priceHTML.querySelector('bdi');
                if (bdi) {
                    // Update only the text nodes, preserving the currency span
                    bdi.childNodes.forEach(node => {
                        if (node.nodeType === Node.TEXT_NODE) {
                            node.textContent = formattedPrice;
                        }
                    });
                }
            }
        }
    });
});
