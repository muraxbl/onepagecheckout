<?php
/**
 * OrderController Override
 * 
 * @author muraxbl
 * @version 1.0.0
 * 
 * This override replaces the default PrestaShop checkout
 * with the One Page Checkout template
 */

class OrderController extends OrderControllerCore
{
    /**
     * Set template for checkout page
     */
    public function initContent()
    {
        // Check if One Page Checkout module is active
        $module = Module::getInstanceByName('onepagecheckout');
        
        if (Validate::isLoadedObject($module) && $module->active) {
            // Use One Page Checkout template
            $this->setTemplate('module:onepagecheckout/views/templates/front/checkout.tpl');
            $this->assignCustomCheckoutData();
        } else {
            // Use default PrestaShop checkout
            parent::initContent();
        }
    }
    
    /**
     * Assign custom data to the checkout template
     */
    protected function assignCustomCheckoutData()
    {
        $context = Context::getContext();
        $cart = $context->cart;
        $customer = $context->customer;
        
        // Get customer addresses
        $deliveryAddresses = [];
        if ($customer->isLogged()) {
            $addresses = $customer->getAddresses($context->language->id);
            foreach ($addresses as $address) {
                $addressObj = new Address($address['id_address']);
                $deliveryAddresses[] = [
                    'id' => $addressObj->id,
                    'alias' => $addressObj->alias,
                    'firstname' => $addressObj->firstname,
                    'lastname' => $addressObj->lastname,
                    'company' => $addressObj->company,
                    'address1' => $addressObj->address1,
                    'address2' => $addressObj->address2,
                    'postcode' => $addressObj->postcode,
                    'city' => $addressObj->city,
                    'phone' => $addressObj->phone,
                    'phone_mobile' => $addressObj->phone_mobile,
                    'country' => Country::getNameById($context->language->id, $addressObj->id_country),
                    'state' => State::getNameById($addressObj->id_state)
                ];
            }
        }
        
        // Get payment options
        $paymentOptions = [];
        $paymentModules = PaymentModule::getInstalledPaymentModules();
        foreach ($paymentModules as $paymentModule) {
            $module = Module::getInstanceByName($paymentModule['name']);
            if ($module && $module->active) {
                $paymentOptions[] = [
                    'module_name' => $module->name,
                    'call_to_action_text' => $module->displayName,
                    'logo' => $module->getPathUri() . 'logo.png',
                    'action' => $context->link->getModuleLink($module->name, 'validation', [], true)
                ];
            }
        }
        
        // Assign variables to template
        $this->context->smarty->assign([
            'customer' => [
                'is_logged' => $customer->isLogged(),
                'firstname' => $customer->firstname,
                'lastname' => $customer->lastname,
                'email' => $customer->email
            ],
            'cart' => [
                'id' => $cart->id,
                'id_address_delivery' => $cart->id_address_delivery,
                'id_address_invoice' => $cart->id_address_invoice,
                'products' => $cart->getProducts(true),
                'subtotals' => [
                    'products' => [
                        'value' => Tools::displayPrice($cart->getOrderTotal(false, Cart::ONLY_PRODUCTS))
                    ],
                    'shipping' => [
                        'value' => Tools::displayPrice($cart->getTotalShippingCost())
                    ],
                    'tax' => [
                        'value' => Tools::displayPrice($cart->getOrderTotal(true, Cart::ONLY_PRODUCTS) - $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS))
                    ],
                    'discount' => [
                        'value' => Tools::displayPrice($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS))
                    ]
                ],
                'totals' => [
                    'total' => [
                        'value' => Tools::displayPrice($cart->getOrderTotal(true, Cart::BOTH))
                    ]
                ]
            ],
            'delivery_addresses' => $deliveryAddresses,
            'payment_options' => $paymentOptions,
            'urls' => [
                'base_url' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__,
                'pages' => [
                    'authentication' => $context->link->getPageLink('authentication'),
                    'cms' => $context->link->getPageLink('cms')
                ]
            ],
            'shop' => [
                'name' => Configuration::get('PS_SHOP_NAME'),
                'logo' => $context->link->getMediaLink(_PS_IMG_.'logo.jpg')
            ],
            'language' => [
                'iso_code' => $context->language->iso_code
            ],
            'static_token' => Tools::getToken(false),
            'token' => Tools::getToken(true)
        ]);
    }
}
