# Testing Checklist for One Page Checkout Module

## Pre-Installation Tests

- [ ] Verify PrestaShop version is 1.7.6 or higher
- [ ] Verify PHP version is 7.1 or higher
- [ ] Verify MySQL version is 5.6 or higher
- [ ] Check that jQuery is loaded in PrestaShop

## Installation Tests

- [ ] Upload module to /modules/onepagecheckout/
- [ ] Verify all files are present
- [ ] Verify file permissions (755 for directories, 644 for files)
- [ ] Go to Module Manager in BackOffice
- [ ] Verify module appears in list
- [ ] Install the module
- [ ] Verify no PHP errors during installation
- [ ] Verify hooks are registered (displayHeader, actionFrontControllerSetMedia)
- [ ] Verify override is copied to /override/controllers/front/OrderController.php
- [ ] Clear PrestaShop cache

## Frontend Tests

### Visual Tests

- [ ] Navigate to checkout page (/order)
- [ ] Verify custom template loads (not native PrestaShop checkout)
- [ ] Verify header is sticky and visible
- [ ] Verify logo displays correctly
- [ ] Verify "Secure Checkout" badge is visible
- [ ] Verify customer info section displays (if logged in)
- [ ] Verify progress navigation dots are visible
- [ ] Verify all three sections are visible: Address, Shipping, Payment
- [ ] Verify sidebar summary is visible and sticky
- [ ] Verify product list displays with images
- [ ] Verify totals display correctly

### Responsive Tests

- [ ] Test on desktop (1920x1080)
- [ ] Test on laptop (1366x768)
- [ ] Test on tablet (768x1024)
- [ ] Test on mobile (375x667)
- [ ] Verify all elements resize properly
- [ ] Verify sidebar moves on mobile
- [ ] Verify fonts and spacing adjust

### Functional Tests - Address Selection

- [ ] Verify customer has at least one saved address
- [ ] Verify first address is auto-selected on load
- [ ] Click on different address
- [ ] Verify address is selected
- [ ] Verify radio button is checked
- [ ] Verify carriers reload automatically

### Functional Tests - Carrier Selection

- [ ] After address auto-selection, verify carriers load
- [ ] Verify loading indicator appears
- [ ] Verify carriers display correctly
- [ ] Verify carrier names, delays, and prices are visible
- [ ] Verify cheapest PAID carrier is auto-selected (not free)
- [ ] Verify free carriers are excluded from auto-selection
- [ ] If only free carriers exist, verify first one is selected
- [ ] Click on different carrier manually
- [ ] Verify carrier selection updates
- [ ] Verify totals update automatically

### Functional Tests - Auto Scroll

- [ ] After carrier auto-selection completes
- [ ] Verify page scrolls to payment section
- [ ] Verify payment section expands automatically
- [ ] Verify address section collapses
- [ ] Verify shipping section collapses
- [ ] Verify address and shipping sections marked as completed
- [ ] Verify progress dots update correctly

### Functional Tests - Payment

- [ ] Verify payment options display
- [ ] Click on a payment method
- [ ] Verify payment method is selected
- [ ] Verify terms checkbox is present
- [ ] Check terms checkbox
- [ ] Verify "Place Order" button is enabled

### Functional Tests - Promo Code

- [ ] Enter a valid promo code
- [ ] Click "Apply"
- [ ] Verify discount is applied
- [ ] Verify totals update
- [ ] Try invalid promo code
- [ ] Verify error message displays

### Functional Tests - Place Order

- [ ] Complete all required fields
- [ ] Check terms and conditions
- [ ] Click "Place Order" button
- [ ] Verify button shows loading state
- [ ] Verify redirect to payment or confirmation page
- [ ] Verify order is created in database

## AJAX Tests

### Console Tests

- [ ] Open browser console (F12)
- [ ] Navigate to checkout
- [ ] Verify no JavaScript errors
- [ ] Verify AJAX calls complete successfully
- [ ] Check network tab for AJAX requests
- [ ] Verify all requests return success: true

### API Endpoint Tests

Test each AJAX endpoint manually:

- [ ] updateAddress: POST to ajax.php with action=updateAddress, id_address=X
- [ ] getCarriers: POST to ajax.php with action=getCarriers
- [ ] updateCarrier: POST to ajax.php with action=updateCarrier, id_carrier=X
- [ ] updateTotals: POST to ajax.php with action=updateTotals
- [ ] applyPromoCode: POST to ajax.php with action=applyPromoCode, code=XXX
- [ ] placeOrder: POST to ajax.php with action=placeOrder, payment_method=XXX

## Security Tests

- [ ] Verify all inputs use Tools::getValue()
- [ ] Test SQL injection attempts
- [ ] Test XSS attempts
- [ ] Verify CSRF tokens are used
- [ ] Verify user authentication is checked
- [ ] Verify address ownership is validated
- [ ] Verify carrier availability is validated
- [ ] Test direct access to ajax.php without auth

## Compatibility Tests

### Module Compatibility

- [ ] Test with PayPal module
- [ ] Test with Stripe module
- [ ] Test with bank transfer module
- [ ] Test with cash on delivery module
- [ ] Test with custom carrier modules
- [ ] Test with discount/voucher modules

### Browser Compatibility

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

## Performance Tests

- [ ] Measure page load time
- [ ] Measure AJAX response times
- [ ] Test with 10 products in cart
- [ ] Test with 50 products in cart
- [ ] Test with slow network (3G simulation)
- [ ] Verify smooth scrolling animation

## Edge Cases

- [ ] Test with no saved addresses
- [ ] Test with no available carriers
- [ ] Test with no payment methods
- [ ] Test with empty cart
- [ ] Test with out-of-stock products
- [ ] Test with customer not logged in
- [ ] Test with all free carriers
- [ ] Test with all paid carriers of same price

## Uninstallation Tests

- [ ] Uninstall module from Module Manager
- [ ] Verify override is removed
- [ ] Verify checkout returns to native PrestaShop
- [ ] Verify no errors on checkout page
- [ ] Verify database is clean

## Documentation Tests

- [ ] Verify README.md is complete
- [ ] Verify installation instructions work
- [ ] Verify troubleshooting section is helpful
- [ ] Verify all features are documented

## Summary

Total Tests: ~100+
Passed: ___
Failed: ___
Skipped: ___

## Notes

- Add any observations or issues here
- Document any bugs found
- Note performance metrics
- Add suggestions for improvements
