
<?php
/**
 * Plugin Name: Dokan Local Pickup Shipping
 * Plugin URI:  
 * Description: Dokan vendor store allow to add local pickup Shipping in cart and checkout 
 * Version: 1.0.0
 * Author: Dhara Talaviya
 * Author URI: 
 * Text Domain: dokan-local-pickup
 * Domain Path: languages
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Basic plugin definitions
 * 
 * @package DokanLocalPickup
 * @since 1.0.0
 */

if( !defined( 'DOKANLOCALPICKUP_DIR' ) ) {
	define( 'DOKANLOCALPICKUP_DIR', dirname( __FILE__ ) ); // plugin dir
}
if( !defined( 'DOKANLOCALPICKUP_URL' ) ) {
	define( 'DOKANLOCALPICKUP_URL', plugin_dir_url( __FILE__ ) ); // plugin url
}
if( !defined( 'DOKANLOCALPICKUP_ADMIN' ) ) {
	define( 'DOKANLOCALPICKUP_ADMIN', DOKANLOCALPICKUP_DIR . '/includes/admin' ); // plugin admin dir
}

if( !defined( 'DOKANLOCALPICKUP_BASENAME' ) ) {
	define( 'DOKANLOCALPICKUP_BASENAME', basename( DOKANLOCALPICKUP_DIR ) ); // base name
}


//add action to load plugin
add_action( 'plugins_loaded', 'dlpickup_plugin_loaded' );



/**
 * Load Plugin
 * 
 * Handles to load plugin after
 * dependent plugin is loaded
 * successfully
 * 
 * @package DokanLocalPickup
 * @since 1.0.0
 */
function dlpickup_plugin_loaded() {
	
		//global variables
		 global  $dlpickup_admin,$dlpickup_scripts, $dlpickup_public;
		
		
		// Script Class to manage all scripts and styles
		include_once( DOKANLOCALPICKUP_DIR . '/includes/class_dlpickup_script.php' );
		$dlpickup_scripts = new DokanLocalPickupScripts();
		$dlpickup_scripts->init_hooks();
    
		
		//collection public class for handling
		require_once( DOKANLOCALPICKUP_DIR . '/includes/class_dlpickup_public.php' );
		$dlpickup_public = new DokanLocalPickupPublic();
		$dlpickup_public->init_hooks();
		
		//Admin Pages Class for admin side
		require_once( DOKANLOCALPICKUP_DIR . '/includes/admin/class_dlpickup_admin.php' );
		$dlpickup_admin = new DokanLocalPikupAdmin();
		$dlpickup_admin->init_hooks();
				
		
}//end if to check plugin loaded is called or not
