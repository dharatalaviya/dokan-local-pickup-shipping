<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Admin Class
 * 
 * Handles all the different features and functions
 * for the front end pages.
 * 
 * @package DokanLocalPickup
 * @since 1.0.0
 */
class DokanLocalPickupScripts{
    // Register and Enqeue Jquery
            
	function pickup_date_picker() {

    // Only on front-end and checkout page
        //if( is_admin() || !is_checkout() || !is_cart()) return;

    	// Load the datepicker jQuery-ui plugin script
    	wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_style( 'jquery-ui-datepicker-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
        wp_register_script( 'pickup_general_js', DOKANLOCALPICKUP_URL. 'includes/js/general.js',array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'pickup_general_js' );
            // Ajax Path locallize
        wp_localize_script('pickup_general_js', 'ajax_custom', array('ajaxurl' => admin_url('admin-ajax.php') ));
        wp_enqueue_style( 'pickup_style_css' , DOKANLOCALPICKUP_URL.'includes/css/style.css' );
	}
	function init_hooks(){
    	add_action( 'wp_enqueue_scripts', array($this, 'pickup_date_picker') );
	   
    }
}