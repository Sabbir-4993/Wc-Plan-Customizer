jQuery(document).ready(function($) {
    'use strict';

    // Cache DOM elements
    var $planCustomizer = $('.wc-plan-customizer');
    var $planOptions = $('.plan-options-container');
    var $addToCartBtn = $('button.single_add_to_cart_button');
    var $priceElement = $('.price');

    // Handle plan option selection
    $planOptions.on('change', 'input[type="radio"], select', function(e) {
        var $this = $(this);
        var optionPrice = parseFloat($this.data('price')) || 0;
        var optionId = $this.val();

        // Update UI
        updateSelectedPlan(optionId);
        
        // Update price if needed
        updatePrice(optionPrice);

        // Trigger custom event for other scripts
        $planCustomizer.trigger('planOptionChanged', {
            optionId: optionId,
            price: optionPrice
        });
    });

    // Handle quantity changes
    $('.quantity input').on('change', function() {
        updateTotalPrice();
    });

    // Update the selected plan UI
    function updateSelectedPlan(optionId) {
        $('.plan-option').removeClass('selected');
        $('.plan-option[data-option-id="' + optionId + '"]').addClass('selected');
    }

    // Update the displayed price
    function updatePrice(newPrice) {
        if (newPrice !== undefined) {
            // Format price according to WooCommerce settings
            var formattedPrice = formatPrice(newPrice);
            
            // Update price display
            if ($priceElement.length) {
                $priceElement.html(formattedPrice);
            }
        }
    }

    // Calculate and update total price
    function updateTotalPrice() {
        var quantity = parseInt($('.quantity input').val()) || 1;
        var basePrice = parseFloat($('.plan-option.selected').data('price')) || 0;
        var total = quantity * basePrice;

        updatePrice(total);
    }

    // Format price according to WooCommerce settings
    function formatPrice(price) {
        // You might want to use WooCommerce's price format settings here
        return '<span class="woocommerce-Price-amount amount">' + 
               '<span class="woocommerce-Price-currencySymbol">$</span>' + 
               price.toFixed(2) + 
               '</span>';
    }

    // Add to cart validation
    $addToCartBtn.on('click', function(e) {
        if (!$('.plan-option.selected').length) {
            e.preventDefault();
            alert('Please select a plan option before adding to cart.');
        }
    });

    // Initialize tooltips if using Bootstrap
    if (typeof $().tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
    }

    // Handle custom plan configurations
    $('.plan-customizer-control').on('change', 'input, select', function() {
        var $control = $(this);
        var value = $control.val();
        
        // Trigger price recalculation if needed
        if ($control.data('affects-price')) {
            calculateCustomPrice();
        }
    });

    // Calculate custom plan price based on selected options
    function calculateCustomPrice() {
        var basePrice = parseFloat($('.plan-option.selected').data('base-price')) || 0;
        var additionalCost = 0;

        // Calculate additional costs based on custom options
        $('.plan-customizer-control').each(function() {
            var $control = $(this);
            var value = $control.find('input, select').val();
            var priceModifier = parseFloat($control.data('price-modifier')) || 0;

            if (value && priceModifier) {
                additionalCost += priceModifier;
            }
        });

        updatePrice(basePrice + additionalCost);
    }
});