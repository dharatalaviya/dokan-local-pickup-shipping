<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Public Collection Pages Class
 * 
 * Handles all the different features and functions
 * for the front end pages.
 * 
 * @package DokanLocalPickup
 * @since 1.0.0
 */
class DokanLocalPickupPublic{

	function pickup_validate_order(){
		
		if ( ( is_cart() || is_checkout() ) && ! WC()->cart->is_empty() ) {

			$cart_contents = WC()->cart->cart_contents;
			$seller_list = [];
			$count = 0;
			foreach ( array_keys( WC()->cart->cart_contents ) as $cart_item_key ) {
				if ( isset( $cart_contents[ $cart_item_key ]['product_id'] ) ) {
					$seller_id=  get_post_field( 'post_author', $cart_contents[ $cart_item_key ]['product_id'] );
					$seller_list[$seller_id][] = $cart_contents[ $cart_item_key ]; 

				}
				$count++;
			}
		}	
	//	$this -> get_html($seller_list);
	}

	
	function dokan_shipping_package_name_address( $shipping_label, $i, $package ) {
		$user_id = $package['seller_id'];
		$store_info   = dokan_get_store_info( $user_id );
		$address .= ($store_info['address']['street_1'])? $store_info['address']['street_1'] .", " : "";
		$address .= ($store_info['address']['street_2'])? $store_info['address']['street_2'] .", " : "";
		$address .= ($store_info['address']['city'])? $store_info['address']['city'] .", " : "";
		$address .= ($store_info['address']['state']) ? $store_info['address']['state'] .", " : "";
		$address .= ($store_info['address']['country']) ? $store_info['address']['country'].", ": "";
		$address .= ($store_info['address']['zip'])? $store_info['address']['zip'] ."." : "";
				
    	return '<div class="pickup_shipping_title">'.$shipping_label." <br/>Address: <a href='http://maps.google.com/?q=".$address."' title='".$address."' target='_blank'>".$address."</a></div>"; 
	}

	function add_dokan_pickup_html($html){
		return;
	}
   	function woo_adon_plugin_template( $template, $template_name, $template_path ) {
     	global $woocommerce;
     	$_template = $template;
     	if ( ! $template_path ) 
        	$template_path = $woocommerce->template_url;
 
     		$plugin_path  =  DOKANLOCALPICKUP_DIR   . '/templates/woocommerce/';
 
    	// Look within passed path within the theme - this is priority
    	$template = locate_template(
	    	array(
	      		$template_path . $template_name,
	      		$template_name
	    	)
   		);
 
	   if( ! $template && file_exists( $plugin_path . $template_name ) )
	    $template = $plugin_path . $template_name;
	 
	   if ( ! $template )
	   $template = $_template;

	   return $template;
	}
	function add_vendor_local_pickup_datetime($package){
		ob_start();
		$seller_id = $package['seller_id'];
		$vendorshipping = WC()->session->get('vendorshipping');
		$index = array_search($seller_id, array_column($vendorshipping, 'seller_id'));	
		if(array_search($seller_id, array_column($vendorshipping, 'seller_id'))  !== False){
			$sellershippingdata = $vendorshipping[$index];  	
		}
		//WC()->session->__unset( 'vendorshipping' );
		$seller_info = dokan_get_store_info( $seller_id );
		$show_store_open_close = dokan_get_option( 'store_open_close', 'dokan_appearance', 'on' );
    	$dokan_days = array( 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' );
    	$all_times  = isset( $seller_info['dokan_store_time'] ) ? $seller_info['dokan_store_time'] : '';
    	 $dayofweek = date("w", strtotime($sellershippingdata['pickup_date']));
    	echo '<div id="pickup_shipping_container">';
		echo "<label>Pickup Date & Time: </label>";
		echo "<div class='pickup_shipping_fields'><input type='text' name='sellerdate[".$seller_id."]' class='datepicker input-text' id='datepicker_".$seller_id."' placeholder='Select Date' data-storedata='".json_encode($all_times)."' data-sellerid='".$seller_id."' value='".$sellershippingdata['pickup_date']."'>";
		if(isset($sellershippingdata['pickup_date']) && isset($sellershippingdata['pickup_date']) != ''){
			$store_opentime = ($all_times[$dokan_days[$dayofweek]]['opening_time'] != '') ? $all_times[$dokan_days[$dayofweek]]['opening_time'] : '10:00 am';
			$store_closetime = ($all_times[$dokan_days[$dayofweek]]['closing_time'] != '') ? $all_times[$dokan_days[$dayofweek]]['closing_time']:'6:00 pm';
		 	$timerange	= $this->hoursRange($store_opentime,$store_closetime );
		
		}
			 $style = (!isset($sellershippingdata['pickup_time']) && empty($timerange)) ? "style='display:none'" : "";  ?>
		 <select name="timedur[<?php echo $seller_id ?>]" id="timedur_<?php echo $seller_id?>" <?php echo $style; ?> class='select input-text selecttime' data-pickupdate="<?php echo $sellershippingdata['pickup_date']; ?>" data-opentime="<?php echo $all_times[$dokan_days[$dayofweek]]['opening_time'] ?>" data-closetime="<?php echo $all_times[$dokan_days[$dayofweek]]['closing_time'] ?>" data-sellerid='<?php echo $seller_id;?>'>
		 		<?php if(!empty($timerange)){ 
		 			foreach ($timerange as $time) { ?>
		 				<option <?php if($time == $sellershippingdata['pickup_time']) echo "selected='selected'"; ?> > <?php echo $time; ?></option> 		
		 		<?php	}	
		 		} ?>
		    </select>
		    </div> 
		</div>	
		<?php     
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
	}
	function hoursRange( $startTime, $endTime ) {
	  	$startTime = strtotime($startTime);   /* Find the timestamp of start time */
		$endTime = strtotime($endTime);       /* Find the timestamp of end time */
		$input = array();
		/* Run a loop from start timestamp to end timestamp */
		for ($i = $startTime; $i < $endTime; $i+=3600) {
		    $input[] = date("g:i A", $i);
		}
		return $input;
	}
	function update_pickup_datetime_session(){
		global $woocommerce;
		$data = array();
		$vendorshipping = array();
		$data['seller_id'] = $_POST['sellerid'];
		$data['pickup_date'] = $_POST['pickupdate'];
		$data['pickup_time'] = $_POST['pickuptime'];
		$vendorshipping =  WC()->session->get('vendorshipping');
		$index = array_search($_POST['sellerid'], array_column($vendorshipping, 'seller_id'));
		if(!empty($vendorshipping)){
			if(array_search($_POST['sellerid'], array_column($vendorshipping, 'seller_id'))  !== False) {
    			$vendorshipping[$index] = $data;
    		 	WC()->session->set('vendorshipping', $vendorshipping);		
	    	}else{
	    		array_push($vendorshipping, $data);
	    		WC()->session->set('vendorshipping', $vendorshipping);
			}	
		}else{
			$vendorshipping[] = $data;
			WC()->session->set('vendorshipping', $vendorshipping);
		}
		
		  
		wp_die();	
	}

	function create_order_pickup_delivery( $order, $data ){
	 	$order->update_meta_data('_pickup_shipping_data', WC()->session->get('vendorshipping'));
	}

	function create_sub_order_pickup_delivery($order_id, $seller_id){
		// $order = new /WC_Order($order_id);
		 $vendorshipping = WC()->session->get('vendorshipping');
		 $index = array_search($seller_id, array_column($vendorshipping, 'seller_id'));
		 if(isset($index) && $index >= 0 ){
			$sellershippingdata[] = $vendorshipping[$index];  	
		// 	 $order->update_meta_data($order_id, 'seller_shipping_data', $sellershippingdata);
		//  $order_id = $order->save();
			update_post_meta($order_id, '_pickup_shipping_data', $sellershippingdata);
		 }
		
		//$order_id = $order->save();
	}
	function wc_destroy_pickup_delivery_session() {
		WC()->session->__unset( 'vendorshipping' );
	}

	function pickup_validation_error($posted) {
	    $sellerdate = $_POST['sellerdate'];
	    $sellertime = $_POST['timedur'];
	    $valid = true;
	    if (!empty($sellerdate)) {
	        foreach ($sellerdate as $value){ if($value == '') $valid = false; } 
	    }
	    else{ $valid = false; } 
	    if(!empty($sellertime)){
	     	foreach ($sellertime as $value){ if($value == '') $valid = false; }
	    }else{ $valid = false; } 
	    if($valid == false){
	    	wc_add_notice( __( "Select Pickup Date & time", 'pickup_error' ), 'error');
	    }
	}
	
	function init_hooks(){
		add_action('woocommerce_after_shipping_calculator', array($this,'pickup_validate_order'), 10);    
		add_filter( 'dokan_shipping_package_name', array($this, 'dokan_shipping_package_name_address'), 20,3 );
    	add_filter('woocommerce_shipping_estimate_html', array($this,'add_dokan_pickup_html'),20);
    	add_filter( 'woocommerce_locate_template', array($this, 'woo_adon_plugin_template'), 10, 3 );
    	add_action('vendor_local_pickup_datetime', array($this, 'add_vendor_local_pickup_datetime'),10);
    	add_action('wp_ajax_nopriv_update_pickup_datetime_session', array($this,'update_pickup_datetime_session'),10);
        add_action('wp_ajax_update_pickup_datetime_session', array($this,'update_pickup_datetime_session'),10);
        add_action( 'woocommerce_checkout_create_order', array($this, 'create_order_pickup_delivery'),  10, 2 );
        add_action('dokan_checkout_update_order_meta', array($this, 'create_sub_order_pickup_delivery'),10,2);
        add_action('woocommerce_thankyou', array($this, 'wc_destroy_pickup_delivery_session'), 10, 1);
        add_action('woocommerce_after_checkout_validation', array($this, 'pickup_validation_error'));
       
    }
}