<?php
/**
 * One Page Checkout Module
 * 
 * @author muraxbl
 * @version 1.0.0
 * @license MIT
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class OnePageCheckout extends Module
{
    public function __construct()
    {
        $this->name = 'onepagecheckout';
        $this->tab = 'checkout';
        $this->version = '1.0.0';
        $this->author = 'muraxbl';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.6', 'max' => '1.7.9'];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('One Page Checkout');
        $this->description = $this->l('Checkout personalizado con diseño moderno y funcionalidades avanzadas');
        $this->confirmUninstall = $this->l('¿Estás seguro de que deseas desinstalar este módulo?');
    }

    /**
     * Install module
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            $this->copyOverride();
    }

    /**
     * Uninstall module
     */
    public function uninstall()
    {
        $this->removeOverride();
        return parent::uninstall();
    }

    /**
     * Hook: Display Header
     * Load CSS files in checkout page
     */
    public function hookDisplayHeader($params)
    {
        // Only load on checkout page
        if ($this->context->controller->php_self == 'order') {
            $this->context->controller->addCSS($this->_path.'views/css/checkout.css');
            
            // Add Google Fonts
            $this->context->controller->addCSS('https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap', 'all', null, false);
            
            // Add Material Icons
            $this->context->controller->addCSS('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0', 'all', null, false);
        }
    }

    /**
     * Hook: Action Front Controller Set Media
     * Load JavaScript files in checkout page
     */
    public function hookActionFrontControllerSetMedia($params)
    {
        // Only load on checkout page
        if ($this->context->controller->php_self == 'order') {
            $this->context->controller->addJS($this->_path.'views/js/checkout.js');
        }
    }

    /**
     * Copy OrderController override
     */
    private function copyOverride()
    {
        $source = $this->local_path.'override/controllers/front/OrderController.php';
        $destination = _PS_OVERRIDE_DIR_.'controllers/front/OrderController.php';
        
        // Create directories if they don't exist
        $dir = dirname($destination);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Copy override file if it doesn't exist
        if (!file_exists($destination)) {
            if (file_exists($source)) {
                copy($source, $destination);
            }
        }
        
        return true;
    }

    /**
     * Remove OrderController override
     */
    private function removeOverride()
    {
        $override = _PS_OVERRIDE_DIR_.'controllers/front/OrderController.php';
        if (file_exists($override)) {
            // Check if it's our override before deleting
            $content = file_get_contents($override);
            if (strpos($content, 'OnePageCheckout') !== false) {
                unlink($override);
            }
        }
        return true;
    }

    /**
     * Get module configuration page
     */
    public function getContent()
    {
        $output = '';
        
        if (Tools::isSubmit('submit'.$this->name)) {
            $output .= $this->displayConfirmation($this->l('Configuración guardada'));
        }
        
        return $output.$this->displayForm();
    }

    /**
     * Display configuration form
     */
    public function displayForm()
    {
        $output = '<div class="panel">
            <div class="panel-heading">'.$this->l('One Page Checkout').'</div>
            <div class="panel-body">
                <p>'.$this->l('Este módulo reemplaza el checkout nativo de PrestaShop con un diseño moderno y funcionalidades avanzadas.').'</p>
                <h4>'.$this->l('Características:').'</h4>
                <ul>
                    <li>'.$this->l('✅ Diseño moderno y responsive').'</li>
                    <li>'.$this->l('✅ Sin recargas de página (AJAX)').'</li>
                    <li>'.$this->l('✅ Auto-selección de dirección de entrega').'</li>
                    <li>'.$this->l('✅ Auto-selección del transportista más barato (no gratuito)').'</li>
                    <li>'.$this->l('✅ Scroll automático al paso de pago').'</li>
                </ul>
            </div>
        </div>';
        
        return $output;
    }
}
