<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Shipping Class
 * 
 * Handles all the different features and functions
 * for the front end pages.
 * 
 * @package DokanLocalPickup
 * @since 1.0.0
 */

class DokanLocalPikupShipingMethod{

	function __construct(){
		
	} 

	function dokan_pickup_shipping_method(){
		$this->id = 'dokanpickup';
		$this->method_title = __('Dokan Local Pickup','dokan-local-pickup');
		$this->method_description = __('Shipping Method for dokan Multi-Vendor Store Pickup', 'cloudways');
		$this->availability = 'including';
		$this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
 		$this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Store Pickup', 'dokan-local-pickup');
	}	
	function init_form_fields()
	{
		 $this->form_fields = array(
			 'enabled' => array(
			 'title' => __('Enable', 'dokan-local-pickup'),
			 'type' => 'checkbox',
			 'default' => 'yes'
			 ),
			 
			 'title' => array(
			 'title' => __('Title', 'dokan-local-pickup'),
			 'type' => 'text',
			 'default' => __('Store Pickup', 'dokan-local-pickup')
			 ),
		 );
	 }
	function add_dokan_pickup_shipping_method($methods)
 	{ 
 		$methods[] = 'dokan_pickup_shipping';
 		return $methods; 
	}

	function init_hooks(){
    	add_filter('woocommerce_shipping_methods', array($this, 'add_dokan_pickup_shipping_method'),20);

    	$this->init_form_fields();
	 	//$this->init_settings();
	 	add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));	
	 	add_action('woocommerce_shipping_init', 'dokan_pickup_shipping_method');
    }
}

}