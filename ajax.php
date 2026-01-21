<?php
/**
 * One Page Checkout - AJAX Endpoint
 * 
 * @author muraxbl
 * @version 1.0.0
 * 
 * Handles all AJAX requests:
 * - updateAddress: Update delivery address
 * - getCarriers: Get available carriers
 * - updateCarrier: Update selected carrier
 * - updateTotals: Get updated cart totals
 * - applyPromoCode: Apply discount code
 * - placeOrder: Process the order
 */

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

// Set JSON header
header('Content-Type: application/json');

// Get action
$action = Tools::getValue('action');
$context = Context::getContext();

// Check if customer is logged in
if (!$context->customer->isLogged()) {
    die(json_encode([
        'success' => false,
        'error' => 'You must be logged in to checkout'
    ]));
}

// Process action
switch ($action) {
    
    /**
     * Update delivery address
     */
    case 'updateAddress':
        try {
            $idAddress = (int)Tools::getValue('id_address');
            
            if (!$idAddress) {
                throw new Exception('Invalid address ID');
            }
            
            // Verify address belongs to customer
            $address = new Address($idAddress);
            if (!Validate::isLoadedObject($address) || $address->id_customer != $context->customer->id) {
                throw new Exception('Invalid address');
            }
            
            // Update cart
            $cart = $context->cart;
            $cart->id_address_delivery = $idAddress;
            $cart->id_address_invoice = $idAddress; // Use same for invoice
            $cart->update();
            
            die(json_encode([
                'success' => true,
                'message' => 'Address updated successfully'
            ]));
            
        } catch (Exception $e) {
            die(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
        break;
        
    /**
     * Get available carriers
     */
    case 'getCarriers':
        try {
            $cart = $context->cart;
            
            if (!$cart->id_address_delivery) {
                throw new Exception('No delivery address set');
            }
            
            $address = new Address($cart->id_address_delivery);
            
            // Get carriers
            $carriers = Carrier::getCarriers(
                $context->language->id,
                true, // active only
                false, // not deleted
                (int)$address->id_zone,
                $context->customer->getGroups(),
                (int)$cart->id
            );
            
            $carrierList = [];
            
            foreach ($carriers as $carrier) {
                // Get carrier object for price calculation
                $carrierObj = new Carrier($carrier['id_carrier']);
                
                // Skip if carrier is not valid for this cart
                if (!$carrierObj->checkCarrierZone($cart->id_address_delivery)) {
                    continue;
                }
                
                // Get shipping cost
                $shippingCost = $cart->getPackageShippingCost((int)$carrier['id_carrier'], true, null, null);
                
                $carrierList[] = [
                    'id' => (int)$carrier['id_carrier'],
                    'name' => $carrier['name'],
                    'delay' => $carrier['delay'],
                    'price' => (float)$shippingCost,
                    'price_formatted' => Tools::displayPrice($shippingCost)
                ];
            }
            
            die(json_encode([
                'success' => true,
                'carriers' => $carrierList
            ]));
            
        } catch (Exception $e) {
            die(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
        break;
        
    /**
     * Update selected carrier
     */
    case 'updateCarrier':
        try {
            $idCarrier = (int)Tools::getValue('id_carrier');
            
            if (!$idCarrier) {
                throw new Exception('Invalid carrier ID');
            }
            
            // Verify carrier is available
            $carrier = new Carrier($idCarrier);
            if (!Validate::isLoadedObject($carrier)) {
                throw new Exception('Invalid carrier');
            }
            
            // Update cart with delivery option
            $cart = $context->cart;
            $deliveryOption = [
                $cart->id_address_delivery => $idCarrier.','
            ];
            $cart->setDeliveryOption($deliveryOption);
            $cart->update();
            
            die(json_encode([
                'success' => true,
                'message' => 'Carrier updated successfully'
            ]));
            
        } catch (Exception $e) {
            die(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
        break;
        
    /**
     * Update cart totals
     */
    case 'updateTotals':
        try {
            $cart = $context->cart;
            
            // Get cart summary
            $summary = $cart->getSummaryDetails();
            
            die(json_encode([
                'success' => true,
                'subtotal' => Tools::displayPrice($summary['total_products']),
                'shipping' => Tools::displayPrice($summary['total_shipping']),
                'tax' => Tools::displayPrice($summary['total_tax']),
                'discount' => isset($summary['total_discounts']) ? Tools::displayPrice($summary['total_discounts']) : '0',
                'total' => Tools::displayPrice($summary['total_price'])
            ]));
            
        } catch (Exception $e) {
            die(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
        break;
        
    /**
     * Apply promo code
     */
    case 'applyPromoCode':
        try {
            $code = trim(Tools::getValue('code'));
            
            if (empty($code)) {
                throw new Exception('Please enter a promo code');
            }
            
            // Try to add the voucher
            $cart = $context->cart;
            
            // Check if voucher exists and is valid
            if (!CartRule::cartRuleExists($code)) {
                throw new Exception('This promo code does not exist');
            }
            
            // Get cart rule
            $cartRule = new CartRule(CartRule::getIdByCode($code));
            
            // Validate cart rule
            if (!Validate::isLoadedObject($cartRule)) {
                throw new Exception('Invalid promo code');
            }
            
            // Check if already in cart
            if ($cart->cartRuleExists($cartRule->id)) {
                throw new Exception('This promo code has already been applied');
            }
            
            // Add to cart
            $cart->addCartRule($cartRule->id);
            
            die(json_encode([
                'success' => true,
                'message' => 'Promo code applied successfully'
            ]));
            
        } catch (Exception $e) {
            die(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
        break;
        
    /**
     * Place order
     */
    case 'placeOrder':
        try {
            $cart = $context->cart;
            $customer = $context->customer;
            
            // Validate cart
            if (!Validate::isLoadedObject($cart) || $cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0) {
                throw new Exception('Invalid cart');
            }
            
            // Check if cart has products
            if (!$cart->nbProducts()) {
                throw new Exception('Your cart is empty');
            }
            
            // Check if all products are still available
            if (!$cart->checkQuantities()) {
                throw new Exception('Some products are no longer available in the requested quantities');
            }
            
            // Get payment method
            $paymentMethod = Tools::getValue('payment_method');
            if (empty($paymentMethod)) {
                throw new Exception('Please select a payment method');
            }
            
            // Get payment module
            $paymentModule = Module::getInstanceByName($paymentMethod);
            if (!Validate::isLoadedObject($paymentModule)) {
                throw new Exception('Invalid payment method');
            }
            
            // Verify payment module is active
            if (!$paymentModule->active) {
                throw new Exception('Payment method is not available');
            }
            
            // Get redirect URL for payment
            // Different payment modules may have different URLs
            $redirectUrl = $context->link->getModuleLink(
                $paymentMethod,
                'validation',
                [],
                true
            );
            
            // If module has a custom payment page, use it
            if (method_exists($paymentModule, 'getPaymentUrl')) {
                $redirectUrl = $paymentModule->getPaymentUrl();
            }
            
            die(json_encode([
                'success' => true,
                'message' => 'Redirecting to payment...',
                'redirect_url' => $redirectUrl
            ]));
            
        } catch (Exception $e) {
            die(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
        break;
        
    /**
     * Default: Invalid action
     */
    default:
        die(json_encode([
            'success' => false,
            'error' => 'Invalid action'
        ]));
        break;
}
