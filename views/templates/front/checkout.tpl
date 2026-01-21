{**
 * One Page Checkout Template
 * 
 * @author muraxbl
 * @version 1.0.0
 *}

<!DOCTYPE html>
<html lang="{$language.iso_code}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{l s='Checkout' mod='onepagecheckout'} - {$shop.name}</title>
    
    <script type="text/javascript">
        var baseDir = '{$urls.base_url}/';
        var static_token = '{$static_token}';
        var token = '{$token}';
    </script>
</head>
<body id="checkout" class="lang-{$language.iso_code}">

<div class="checkout-container">
    {* Sticky Header *}
    <header class="checkout-header">
        <div class="header-content">
            <div class="logo">
                <a href="{$urls.base_url}">
                    <img src="{$shop.logo}" alt="{$shop.name}" />
                </a>
            </div>
            <div class="secure-checkout">
                <span class="material-symbols-outlined">lock</span>
                <span>{l s='Secure Checkout' mod='onepagecheckout'}</span>
            </div>
        </div>
    </header>

    <div class="checkout-content">
        <div class="checkout-main">
            {* Progress Navigation *}
            <nav class="progress-nav">
                <div class="progress-dot active" data-section="addresses">
                    <div class="dot"></div>
                    <span>{l s='Address' mod='onepagecheckout'}</span>
                </div>
                <div class="progress-dot" data-section="shipping">
                    <div class="dot"></div>
                    <span>{l s='Shipping' mod='onepagecheckout'}</span>
                </div>
                <div class="progress-dot" data-section="payment">
                    <div class="dot"></div>
                    <span>{l s='Payment' mod='onepagecheckout'}</span>
                </div>
            </nav>

            {* Customer Info *}
            {if $customer.is_logged}
            <div class="customer-info">
                <div class="customer-avatar">
                    <span class="material-symbols-outlined">account_circle</span>
                </div>
                <div class="customer-details">
                    <div class="customer-name">{$customer.firstname} {$customer.lastname}</div>
                    <div class="customer-email">{$customer.email}</div>
                </div>
                <a href="{$urls.pages.authentication}?back=order" class="btn-logout">
                    {l s='Logout' mod='onepagecheckout'}
                </a>
            </div>
            {/if}

            {* Section 1: Addresses *}
            <section id="section-addresses" class="checkout-section">
                <div class="section-header expanded">
                    <div class="section-number">1</div>
                    <h2 class="section-title">{l s='Delivery Address' mod='onepagecheckout'}</h2>
                    <button class="section-toggle">
                        <span class="material-symbols-outlined">expand_more</span>
                    </button>
                </div>
                <div class="section-content active">
                    <div id="delivery-addresses" class="address-list">
                        {if isset($delivery_addresses) && $delivery_addresses}
                            {foreach from=$delivery_addresses item=address}
                            <div class="address-item {if $address.id == $cart.id_address_delivery}selected{/if}" 
                                 data-id-address="{$address.id}">
                                <div class="address-radio">
                                    <input type="radio" name="id_address_delivery" 
                                           value="{$address.id}" 
                                           id="address_{$address.id}"
                                           {if $address.id == $cart.id_address_delivery}checked{/if}>
                                    <label for="address_{$address.id}"></label>
                                </div>
                                <div class="address-content">
                                    <div class="address-alias">{$address.alias}</div>
                                    <div class="address-details">
                                        {$address.firstname} {$address.lastname}<br>
                                        {$address.address1}
                                        {if $address.address2}<br>{$address.address2}{/if}<br>
                                        {$address.postcode} {$address.city}<br>
                                        {$address.country}
                                        {if $address.phone}<br>{l s='Phone' mod='onepagecheckout'}: {$address.phone}{/if}
                                    </div>
                                </div>
                                <div class="address-actions">
                                    <button class="btn-icon" title="{l s='Edit' mod='onepagecheckout'}">
                                        <span class="material-symbols-outlined">edit</span>
                                    </button>
                                </div>
                            </div>
                            {/foreach}
                        {else}
                            <div class="no-addresses">
                                <p>{l s='You have no saved addresses' mod='onepagecheckout'}</p>
                            </div>
                        {/if}
                        
                        <button class="btn-secondary" id="btn-add-address">
                            <span class="material-symbols-outlined">add</span>
                            {l s='Add New Address' mod='onepagecheckout'}
                        </button>
                    </div>
                </div>
            </section>

            {* Section 2: Shipping *}
            <section id="section-shipping" class="checkout-section">
                <div class="section-header">
                    <div class="section-number">2</div>
                    <h2 class="section-title">{l s='Shipping Method' mod='onepagecheckout'}</h2>
                    <button class="section-toggle">
                        <span class="material-symbols-outlined">expand_more</span>
                    </button>
                </div>
                <div class="section-content">
                    <div id="delivery-options" class="shipping-list">
                        <div class="loading">{l s='Loading shipping options...' mod='onepagecheckout'}</div>
                    </div>
                </div>
            </section>

            {* Section 3: Payment *}
            <section id="section-payment" class="checkout-section">
                <div class="section-header">
                    <div class="section-number">3</div>
                    <h2 class="section-title">{l s='Payment Method' mod='onepagecheckout'}</h2>
                    <button class="section-toggle">
                        <span class="material-symbols-outlined">expand_more</span>
                    </button>
                </div>
                <div class="section-content">
                    <div id="payment-options" class="payment-list">
                        {if isset($payment_options) && $payment_options}
                            {foreach from=$payment_options item=payment_option}
                            <div class="payment-option" data-payment="{$payment_option.module_name}">
                                <div class="payment-radio">
                                    <input type="radio" name="payment_method" 
                                           value="{$payment_option.module_name}" 
                                           id="payment_{$payment_option.module_name}">
                                    <label for="payment_{$payment_option.module_name}"></label>
                                </div>
                                <div class="payment-content">
                                    <div class="payment-name">{$payment_option.call_to_action_text}</div>
                                    {if isset($payment_option.logo)}
                                    <img src="{$payment_option.logo}" alt="{$payment_option.call_to_action_text}" class="payment-logo">
                                    {/if}
                                </div>
                            </div>
                            {/foreach}
                        {else}
                            <div class="no-payment">
                                <p>{l s='No payment methods available' mod='onepagecheckout'}</p>
                            </div>
                        {/if}
                    </div>

                    {* Terms and Conditions *}
                    <div class="terms-conditions">
                        <label class="checkbox-label">
                            <input type="checkbox" id="terms" name="terms" required>
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">
                                {l s='I agree to the' mod='onepagecheckout'} 
                                <a href="{$urls.pages.cms}?id_cms=3" target="_blank">
                                    {l s='terms and conditions' mod='onepagecheckout'}
                                </a>
                            </span>
                        </label>
                    </div>

                    {* Place Order Button *}
                    <button type="button" id="btn-place-order" class="btn-primary">
                        <span class="material-symbols-outlined">shopping_cart_checkout</span>
                        {l s='Place Order' mod='onepagecheckout'}
                    </button>
                </div>
            </section>
        </div>

        {* Sidebar Summary *}
        <aside class="checkout-sidebar">
            <div class="summary-sticky">
                <h3 class="summary-title">{l s='Order Summary' mod='onepagecheckout'}</h3>
                
                {* Cart Items *}
                <div class="summary-items">
                    {if isset($cart.products) && $cart.products}
                        {foreach from=$cart.products item=product}
                        <div class="summary-item">
                            <div class="item-image">
                                <img src="{$product.cover.bySize.cart_default.url}" alt="{$product.name}">
                                <span class="item-quantity">{$product.quantity}</span>
                            </div>
                            <div class="item-details">
                                <div class="item-name">{$product.name}</div>
                                {if isset($product.attributes)}
                                    <div class="item-attributes">
                                        {foreach from=$product.attributes item=attribute}
                                            {$attribute.group}: {$attribute.value}
                                        {/foreach}
                                    </div>
                                {/if}
                            </div>
                            <div class="item-price">{$product.total}</div>
                        </div>
                        {/foreach}
                    {/if}
                </div>

                {* Promo Code *}
                <div class="promo-code">
                    <input type="text" id="promo-input" placeholder="{l s='Promo code' mod='onepagecheckout'}">
                    <button type="button" id="btn-apply-promo" class="btn-secondary">
                        {l s='Apply' mod='onepagecheckout'}
                    </button>
                </div>

                {* Summary Lines *}
                <div class="summary-lines">
                    <div class="summary-line">
                        <span class="summary-line-label">{l s='Subtotal' mod='onepagecheckout'}</span>
                        <span class="summary-line-value" id="subtotal">{$cart.subtotals.products.value}</span>
                    </div>
                    <div class="summary-line">
                        <span class="summary-line-label">{l s='Shipping' mod='onepagecheckout'}</span>
                        <span class="summary-line-value" id="shipping">{$cart.subtotals.shipping.value}</span>
                    </div>
                    {if isset($cart.subtotals.tax) && $cart.subtotals.tax.value}
                    <div class="summary-line">
                        <span class="summary-line-label">{l s='Tax' mod='onepagecheckout'}</span>
                        <span class="summary-line-value" id="tax">{$cart.subtotals.tax.value}</span>
                    </div>
                    {/if}
                    {if isset($cart.subtotals.discount) && $cart.subtotals.discount.value}
                    <div class="summary-line discount">
                        <span class="summary-line-label">{l s='Discount' mod='onepagecheckout'}</span>
                        <span class="summary-line-value" id="discount">-{$cart.subtotals.discount.value}</span>
                    </div>
                    {/if}
                </div>

                {* Total *}
                <div class="summary-total">
                    <span class="summary-total-label">{l s='Total' mod='onepagecheckout'}</span>
                    <span class="summary-total-value" id="total">{$cart.totals.total.value}</span>
                </div>

                {* Secure badges *}
                <div class="secure-badges">
                    <div class="badge">
                        <span class="material-symbols-outlined">verified_user</span>
                        <span>{l s='Secure Payment' mod='onepagecheckout'}</span>
                    </div>
                    <div class="badge">
                        <span class="material-symbols-outlined">local_shipping</span>
                        <span>{l s='Fast Delivery' mod='onepagecheckout'}</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

</body>
</html>
