/**
 * One Page Checkout - JavaScript
 * 
 * @author muraxbl
 * @version 1.0.0
 * 
 * Main functionalities:
 * - Auto-selection of default address
 * - Auto-selection of cheapest paid carrier
 * - Auto-scroll to payment section
 * - AJAX updates without page reload
 * - Dynamic totals update
 */

var OnePageCheckout = {
    
    /**
     * Initialize the checkout
     */
    init: function() {
        console.log('OnePageCheckout: Initializing...');
        this.bindEvents();
        this.autoSelectAddress();
    },
    
    /**
     * Auto-select the default delivery address
     */
    autoSelectAddress: function() {
        console.log('OnePageCheckout: Auto-selecting address...');
        
        // Find the first address (should be the default one)
        var $firstAddress = $('#delivery-addresses .address-item').first();
        
        if ($firstAddress.length > 0) {
            var addressId = $firstAddress.data('id-address');
            console.log('OnePageCheckout: Found default address ID:', addressId);
            
            // Select it
            $firstAddress.addClass('selected');
            $firstAddress.find('input[type="radio"]').prop('checked', true);
            
            // Load carriers for this address
            this.selectAddress(addressId);
        } else {
            console.log('OnePageCheckout: No addresses found');
        }
    },
    
    /**
     * Select an address and load carriers
     */
    selectAddress: function(addressId) {
        console.log('OnePageCheckout: Selecting address:', addressId);
        
        var self = this;
        
        $.ajax({
            url: baseDir + 'modules/onepagecheckout/ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'updateAddress',
                id_address: addressId,
                ajax: true
            },
            success: function(response) {
                console.log('OnePageCheckout: Address updated', response);
                
                if (response.success) {
                    // Load carriers
                    self.loadCarriers();
                } else {
                    console.error('OnePageCheckout: Failed to update address');
                }
            },
            error: function(xhr, status, error) {
                console.error('OnePageCheckout: AJAX error updating address:', error);
            }
        });
    },
    
    /**
     * Load carriers for the selected address
     */
    loadCarriers: function() {
        console.log('OnePageCheckout: Loading carriers...');
        
        var self = this;
        var $deliveryOptions = $('#delivery-options');
        
        // Show loading state
        $deliveryOptions.html('<div class="loading">Loading shipping options...</div>');
        
        $.ajax({
            url: baseDir + 'modules/onepagecheckout/ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'getCarriers',
                ajax: true
            },
            success: function(response) {
                console.log('OnePageCheckout: Carriers loaded', response);
                
                if (response.success && response.carriers && response.carriers.length > 0) {
                    // Display carriers
                    self.displayCarriers(response.carriers);
                    
                    // Auto-select cheapest paid carrier
                    self.autoSelectCheapestCarrier(response.carriers);
                } else {
                    $deliveryOptions.html('<div class="no-carriers">No shipping methods available</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('OnePageCheckout: AJAX error loading carriers:', error);
                $deliveryOptions.html('<div class="error">Error loading shipping methods</div>');
            }
        });
    },
    
    /**
     * Display carriers in the UI
     */
    displayCarriers: function(carriers) {
        console.log('OnePageCheckout: Displaying carriers');
        
        var $deliveryOptions = $('#delivery-options');
        var html = '';
        
        carriers.forEach(function(carrier) {
            var priceClass = parseFloat(carrier.price) === 0 ? 'free' : '';
            var priceText = parseFloat(carrier.price) === 0 ? 'Free' : carrier.price_formatted;
            
            html += '<div class="shipping-option selection-option" data-carrier-id="' + carrier.id + '">';
            html += '  <div class="shipping-radio">';
            html += '    <input type="radio" name="id_carrier" value="' + carrier.id + '" id="carrier_' + carrier.id + '">';
            html += '    <label for="carrier_' + carrier.id + '"></label>';
            html += '  </div>';
            html += '  <div class="shipping-content">';
            html += '    <div class="shipping-name">' + carrier.name + '</div>';
            html += '    <div class="shipping-delay">' + carrier.delay + '</div>';
            html += '  </div>';
            html += '  <div class="shipping-price ' + priceClass + '">' + priceText + '</div>';
            html += '</div>';
        });
        
        $deliveryOptions.html(html);
    },
    
    /**
     * Auto-select cheapest paid carrier (exclude free carriers)
     */
    autoSelectCheapestCarrier: function(carriers) {
        console.log('OnePageCheckout: Auto-selecting cheapest paid carrier...');
        
        // Filter out free carriers
        var paidCarriers = carriers.filter(function(carrier) {
            return parseFloat(carrier.price) > 0;
        });
        
        var selectedCarrier = null;
        
        if (paidCarriers.length > 0) {
            // Select cheapest paid carrier
            selectedCarrier = paidCarriers.reduce(function(min, carrier) {
                return parseFloat(carrier.price) < parseFloat(min.price) ? carrier : min;
            });
            console.log('OnePageCheckout: Selected cheapest paid carrier:', selectedCarrier.name, selectedCarrier.price);
        } else if (carriers.length > 0) {
            // If only free carriers, select the first one
            selectedCarrier = carriers[0];
            console.log('OnePageCheckout: Only free carriers available, selected:', selectedCarrier.name);
        }
        
        if (selectedCarrier) {
            this.selectCarrier(selectedCarrier.id);
        }
    },
    
    /**
     * Select a carrier and update totals
     */
    selectCarrier: function(carrierId) {
        console.log('OnePageCheckout: Selecting carrier:', carrierId);
        
        var self = this;
        
        // Update UI
        $('.shipping-option').removeClass('selected');
        $('.shipping-option[data-carrier-id="' + carrierId + '"]').addClass('selected');
        $('#carrier_' + carrierId).prop('checked', true);
        
        $.ajax({
            url: baseDir + 'modules/onepagecheckout/ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'updateCarrier',
                id_carrier: carrierId,
                ajax: true
            },
            success: function(response) {
                console.log('OnePageCheckout: Carrier updated', response);
                
                if (response.success) {
                    // Update totals
                    self.updateTotals();
                    
                    // Mark shipping section as completed
                    $('#section-shipping .section-number').addClass('completed');
                    
                    // Scroll to payment section
                    setTimeout(function() {
                        self.scrollToPayment();
                    }, 300);
                } else {
                    console.error('OnePageCheckout: Failed to update carrier');
                }
            },
            error: function(xhr, status, error) {
                console.error('OnePageCheckout: AJAX error updating carrier:', error);
            }
        });
    },
    
    /**
     * Update cart totals
     */
    updateTotals: function() {
        console.log('OnePageCheckout: Updating totals...');
        
        $.ajax({
            url: baseDir + 'modules/onepagecheckout/ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'updateTotals',
                ajax: true
            },
            success: function(response) {
                console.log('OnePageCheckout: Totals updated', response);
                
                if (response.success) {
                    // Update shipping cost
                    $('#shipping').text(response.shipping);
                    
                    // Update total
                    $('#total').text(response.total);
                    
                    // Update tax if present
                    if (response.tax) {
                        $('#tax').text(response.tax);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('OnePageCheckout: AJAX error updating totals:', error);
            }
        });
    },
    
    /**
     * Scroll to payment section
     */
    scrollToPayment: function() {
        console.log('OnePageCheckout: Scrolling to payment section...');
        
        var $paymentSection = $('#section-payment');
        
        if ($paymentSection.length > 0) {
            // Smooth scroll to payment section
            $('html, body').animate({
                scrollTop: $paymentSection.offset().top - 100
            }, 800, function() {
                console.log('OnePageCheckout: Scrolled to payment section');
            });
            
            // Expand payment section
            $paymentSection.find('.section-header').addClass('expanded');
            $paymentSection.find('.section-content').addClass('active');
            
            // Collapse previous sections but keep them marked as completed
            $('#section-addresses .section-header').removeClass('expanded');
            $('#section-addresses .section-content').removeClass('active');
            $('#section-addresses .section-number').addClass('completed');
            
            $('#section-shipping .section-header').removeClass('expanded');
            $('#section-shipping .section-content').removeClass('active');
            $('#section-shipping .section-number').addClass('completed');
            
            // Update progress dots
            $('.progress-dot').removeClass('active');
            $('.progress-dot[data-section="addresses"]').addClass('completed');
            $('.progress-dot[data-section="shipping"]').addClass('completed');
            $('.progress-dot[data-section="payment"]').addClass('active');
        }
    },
    
    /**
     * Bind UI events
     */
    bindEvents: function() {
        var self = this;
        
        // Section toggle
        $(document).on('click', '.section-header', function(e) {
            if (!$(e.target).closest('.section-toggle').length) {
                $(this).toggleClass('expanded');
                $(this).siblings('.section-content').toggleClass('active');
            }
        });
        
        $(document).on('click', '.section-toggle', function(e) {
            e.stopPropagation();
            var $header = $(this).closest('.section-header');
            $header.toggleClass('expanded');
            $header.siblings('.section-content').toggleClass('active');
        });
        
        // Address selection
        $(document).on('click', '.address-item', function() {
            var addressId = $(this).data('id-address');
            
            // Update UI
            $('.address-item').removeClass('selected');
            $(this).addClass('selected');
            $(this).find('input[type="radio"]').prop('checked', true);
            
            // Update address and reload carriers
            self.selectAddress(addressId);
        });
        
        // Carrier selection
        $(document).on('click', '.shipping-option', function() {
            var carrierId = $(this).data('carrier-id');
            self.selectCarrier(carrierId);
        });
        
        // Payment selection
        $(document).on('click', '.payment-option', function() {
            $('.payment-option').removeClass('selected');
            $(this).addClass('selected');
            $(this).find('input[type="radio"]').prop('checked', true);
        });
        
        // Progress dot navigation
        $(document).on('click', '.progress-dot', function() {
            var section = $(this).data('section');
            var $section = $('#section-' + section);
            
            if ($section.length > 0) {
                $('html, body').animate({
                    scrollTop: $section.offset().top - 100
                }, 500);
                
                $section.find('.section-header').addClass('expanded');
                $section.find('.section-content').addClass('active');
            }
        });
        
        // Place order
        $(document).on('click', '#btn-place-order', function(e) {
            e.preventDefault();
            self.placeOrder();
        });
        
        // Apply promo code
        $(document).on('click', '#btn-apply-promo', function(e) {
            e.preventDefault();
            self.applyPromoCode();
        });
    },
    
    /**
     * Apply promo code
     */
    applyPromoCode: function() {
        console.log('OnePageCheckout: Applying promo code...');
        
        var code = $('#promo-input').val().trim();
        
        if (!code) {
            alert('Please enter a promo code');
            return;
        }
        
        var self = this;
        
        $.ajax({
            url: baseDir + 'modules/onepagecheckout/ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'applyPromoCode',
                code: code,
                ajax: true
            },
            success: function(response) {
                if (response.success) {
                    alert('Promo code applied successfully!');
                    $('#promo-input').val('');
                    self.updateTotals();
                } else {
                    alert(response.error || 'Invalid promo code');
                }
            },
            error: function(xhr, status, error) {
                console.error('OnePageCheckout: AJAX error applying promo code:', error);
                alert('Error applying promo code');
            }
        });
    },
    
    /**
     * Validate checkout before placing order
     */
    validate: function() {
        console.log('OnePageCheckout: Validating checkout...');
        
        var errors = [];
        
        // Check if address is selected
        if ($('.address-item.selected').length === 0) {
            errors.push('Please select a delivery address');
        }
        
        // Check if carrier is selected
        if ($('.shipping-option.selected').length === 0) {
            errors.push('Please select a shipping method');
        }
        
        // Check if payment method is selected
        if ($('.payment-option.selected').length === 0) {
            errors.push('Please select a payment method');
        }
        
        // Check if terms are accepted
        if (!$('#terms').is(':checked')) {
            errors.push('You must accept the terms and conditions');
        }
        
        if (errors.length > 0) {
            alert(errors.join('\n'));
            return false;
        }
        
        return true;
    },
    
    /**
     * Place the order
     */
    placeOrder: function() {
        console.log('OnePageCheckout: Placing order...');
        
        // Validate first
        if (!this.validate()) {
            return false;
        }
        
        // Show loading state
        $('#btn-place-order').prop('disabled', true).html(
            '<span class="material-symbols-outlined">hourglass_empty</span> Processing...'
        );
        
        var paymentMethod = $('.payment-option.selected').data('payment');
        
        $.ajax({
            url: baseDir + 'modules/onepagecheckout/ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'placeOrder',
                payment_method: paymentMethod,
                ajax: true
            },
            success: function(response) {
                console.log('OnePageCheckout: Order response', response);
                
                if (response.success) {
                    // Redirect to confirmation or payment page
                    window.location.href = response.redirect_url;
                } else {
                    alert(response.error || 'Error processing order');
                    $('#btn-place-order').prop('disabled', false).html(
                        '<span class="material-symbols-outlined">shopping_cart_checkout</span> Place Order'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error('OnePageCheckout: AJAX error placing order:', error);
                alert('Error processing your order. Please try again.');
                $('#btn-place-order').prop('disabled', false).html(
                    '<span class="material-symbols-outlined">shopping_cart_checkout</span> Place Order'
                );
            }
        });
    }
};

// Initialize when document is ready
$(document).ready(function() {
    console.log('Document ready, checking for checkout page...');
    
    // Only initialize if we're on the checkout page
    if ($('#checkout').length > 0 || $('.checkout-container').length > 0) {
        console.log('Checkout page detected, initializing OnePageCheckout');
        OnePageCheckout.init();
    } else {
        console.log('Not on checkout page, skipping initialization');
    }
});
