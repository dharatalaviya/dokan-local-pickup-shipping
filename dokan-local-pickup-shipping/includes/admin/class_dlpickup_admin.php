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
class DokanLocalPikupAdmin{

	function add_shipping_develiery_detials($item_id,$item){
		$orderid = $item['order_id'];
		$metadata = $item-> get_formatted_meta_data('');
		$shipping_meta = get_post_meta( $orderid, '_pickup_shipping_data', true);
		if(!empty($metadata)){
			foreach($metadata as $meta_id => $meta ){
				if($meta->key == 'seller_id'){
					$index = array_search($meta->value, array_column($shipping_meta, 'seller_id'));
					$store_info = dokan_get_store_info( $meta->value );
					$address .= ($store_info['address']['street_1'])? $store_info['address']['street_1'] .", " : "";
						$address .= ($store_info['address']['street_2'])? $store_info['address']['street_2'] .", " : "";
						$address .= ($store_info['address']['city'])? $store_info['address']['city'] .", " : "";
						$address .= ($store_info['address']['state']) ? $store_info['address']['state'] .", " : "";
						$address .= ($store_info['address']['country']) ? $store_info['address']['country'].", ": "";
						$address .= ($store_info['address']['zip'])? $store_info['address']['zip'] ."." : "";

					if($index > -1){
						echo '<div class="view">';
						echo "<strong>Address: </strong><a href='http://maps.google.com/?q=<?php echo $address?>' title='".$address."' target='_blank'>".$address."</a></br>";
						echo '<strong>Delivery Date: </strong>'.$shipping_meta[$index]["pickup_date"].'<br/>';
						echo '<strong>Delivery Time: </strong>'.$shipping_meta[$index]["pickup_time"].'</div>';
					}
		
				}
			}
		}
	
	}
	function register_my_new_order_statuses() {
	    register_post_status( 'wc-ready-for-pickup', array(
	        'label'                     => _x( 'Ready For Pickup', 'Order status', 'woocommerce' ),
	        'public'                    => true,
	        'exclude_from_search'       => false,
	        'show_in_admin_all_list'    => true,
	        'show_in_admin_status_list' => true,
	        'label_count'               => _n_noop( 'Ready For Pickup <span class="count">(%s)</span>', 'Ready For Pickup<span class="count">(%s)</span>', 'woocommerce' )
	    ) );
	}

	// Register in wc_order_statuses.
	function my_new_wc_order_statuses( $order_statuses ) {
	    $order_statuses['wc-ready-for-pickup'] = _x( 'Ready For Pickup', 'Order status', 'woocommerce' );

	    return $order_statuses;
	}
	function rename_or_reorder_bulk_actions( $actions ) {
	    $actions['mark_invoiced'] = __( 'Mark Ready For Pickup', 'textdomain' );
	    return $actions;
	}

	function add_pickup_delivery_details_email($order){
		$shipping_details = get_post_meta( $order->get_id(), '_pickup_shipping_data', true);
		if(!empty($shipping_details)){ ?>
		<div class="pickup-thankyou-shipping-details">
			<h2><strong>Pickup Delivery Details</strong></h2>
			<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
				<tbody>
					<?php foreach($shipping_details as $pickup_store):
						$store_info = dokan_get_store_info( $pickup_store['seller_id'] );
						$address .= ($store_info['address']['street_1'])? $store_info['address']['street_1'] .", " : "";
						$address .= ($store_info['address']['street_2'])? $store_info['address']['street_2'] .", " : "";
						$address .= ($store_info['address']['city'])? $store_info['address']['city'] .", " : "";
						$address .= ($store_info['address']['state']) ? $store_info['address']['state'] .", " : "";
						$address .= ($store_info['address']['country']) ? $store_info['address']['country'].", ": "";
						$address .= ($store_info['address']['zip'])? $store_info['address']['zip'] ."." : "";
					?>
					<tr>
						<td>
							<p><strong>Store Name: </strong><?php echo $store_info['store_name'];?></p>
							<p><strong>Address: </strong><a href='http://maps.google.com/?q=<?php echo $address?>' title='<?php echo $address; ?>' target='_blank'><?php echo $address; ?></p>
							<p><strong>Pick Up Date: </strong><?php echo $pickup_store['pickup_date']; ?>&nbsp; <strong>Pick UP time: </strong>	 <?php echo $pickup_store['pickup_time']; ?>
						</td>	
					</tr>
					<?php endforeach; ?>	
				</tbody>	
			</table>	
		</div>	
		<?php
		}
	}
	function init_hooks(){
    	add_action('woocommerce_after_order_itemmeta', array($this,'add_shipping_develiery_detials'), 20, 2);
    	add_action( 'init', array($this, 'register_my_new_order_statuses') );
    	add_filter( 'wc_order_statuses', array($this, 'my_new_wc_order_statuses') );
    	add_filter( 'bulk_actions-edit-shop_order', array($this, 'rename_or_reorder_bulk_actions'), 20 );
    	add_action('woocommerce_email_after_order_table', array($this,'add_pickup_delivery_details_email'), 20 );

    }
}