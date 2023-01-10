<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://https://cloud1.me/
 * @since      1.0.0
 *
 * @package    Woo_Way_Express_Shipping
 * @subpackage Woo_Way_Express_Shipping/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woo_Way_Express_Shipping
 * @subpackage Woo_Way_Express_Shipping/public
 * @author     Gaurav Garg <gauravgargcs1991@gmail.com>
 */
class Woo_Way_Express_Shipping_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        $woowes_options = get_option( 'woowes_setting_options' );

        if(isset($woowes_options['auth_access_token']) && $woowes_options['auth_access_token'] != ''){
            add_action('woocommerce_thankyou', array($this, 'woowes_add_delivery_request'), 10, 1);

            add_action('woocommerce_order_details_before_order_table', array($this, 'woowes_order_tracking_status'));

            //add_action( 'woocommerce_before_order_notes', array( $this, 'woowes_add_city_district_field') );
            add_action( 'woocommerce_checkout_fields', array( $this, 'woowes_add_city_district_field_checkout') );
        }
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Way_Express_Shipping_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Way_Express_Shipping_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-way-express-shipping-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Way_Express_Shipping_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Way_Express_Shipping_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        $woowes_options = get_option( 'woowes_setting_options' );

        if(isset($woowes_options['auth_access_token']) && $woowes_options['auth_access_token'] != ''){
		    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-way-express-shipping-public.js', array( 'jquery' ), $this->version, false );
        }
	}

    public function woowes_add_city_district_field($checkout){
        
        $wes_api = new Woo_Way_Express_Shipping_API();
        $cities = $wes_api->woowes_get_cities();
        $cities = json_decode($cities, true);
        $cities_select_options = array();
        if( !empty($cities['Data']['List'])){
            foreach($cities['Data']['List'] as $city__){
                // single destination array structure
                // {
                //     "CityID": 6,
                //     "CityNameAR": "سكاكا",
                //     "CityNameEN": "Sakaka",
                //     "Code": "SKK"
                // }
                
                $cities_select_options[$city__['CityNameEN']] = $city__['CityNameEN'];
            }
        }
        $class__ = '';
        
        if($checkout->get_value( 'billing_country' ) != 'SA'){
            $class__ = 'woowes_city_field_hide';
            
        }
        echo '<div class="woowes_city_field_div '.$class__.'">';
        woocommerce_form_field( 'woowes_city', array(        
            'type' => 'select',        
            'class' => array( 'form-row-wide' ),        
            'label' => __( 'Select City', 'woo-way-express-shipping' ),
            'options' => $cities_select_options,   
         ), $checkout->get_value( 'woowes_city' ) );
        echo '</div>';
    }

    public function woowes_add_city_district_field_checkout( $fields ){
        $language__ = get_locale();
        $wes_api = new Woo_Way_Express_Shipping_API();
        $cities = $wes_api->woowes_get_cities();
        
        $cities = json_decode($cities, true);
        $cities_select_options = array();
        if( !empty($cities['Data']['List'])){
            foreach($cities['Data']['List'] as $city__){
                // single destination array structure
                // {
                //     "CityID": 6,
                //     "CityNameAR": "سكاكا",
                //     "CityNameEN": "Sakaka",
                //     "Code": "SKK"
                // }
                if($language__ == 'ar'){
                    $cities_select_options[$city__['CityNameAR']] = $city__['CityNameAR'];
                }
                else{
                    $cities_select_options[$city__['CityNameEN']] = $city__['CityNameEN'];
                }
            }
        }

        // Adding 2 custom select fields
        $fields['billing']['billing_city_woowes'] = $fields['shipping']['shipping_city_woowes'] = array(
            'type'         => 'select',
            'required'     => true,
            'options'      => $cities_select_options,
            'autocomplete' => 'address-level2',
        );

        // Copying data from WooCommerce city fields
        $fields['billing']['billing_city_woowes']['class'] = array_merge($fields['billing']['billing_city']['class'], array('hidden') );
        $fields['shipping']['shipping_city_woowes']['class'] = array_merge($fields['shipping']['shipping_city']['class'], array('hidden') );
        $fields['billing']['billing_city_woowes']['label'] = $fields['billing']['billing_city']['label'];
        $fields['shipping']['shipping_city_woowes']['label'] = $fields['shipping']['shipping_city']['label'];
        $fields['billing']['billing_city_woowes']['priority'] = $fields['billing']['billing_city']['priority'] + 5;
        $fields['shipping']['shipping_city_woowes']['priority'] = $fields['shipping']['shipping_city']['priority'] + 5;

        return $fields;
    }

    public function woowes_add_delivery_request( $order_id ){
        if ( ! $order_id )
        return;

        $order = wc_get_order( $order_id );
       // echo $order->get_shipping_country(); die;
        if($order->get_shipping_country() != 'SA'){
            return;
        }

        $wes_api = new Woo_Way_Express_Shipping_API();
        $selected_woowes_city = $wes_api->woowes_get_cities( $order->get_shipping_city() );
        $selected_woowes_city = json_decode($selected_woowes_city, true);
        if(empty($selected_woowes_city['Data']['List'])){
            return;
        }

         // Allow code execution only once 
        if( ! get_post_meta( $order_id, '_thankyou_woowes_action_done', true ) ) {
            // Get an instance of the WC_Order object

            // Get the order number
            $order_number = $order->get_id();

            $order_status = $order->get_status();

            $request_items = array();
             // Loop through order items
            foreach ( $order->get_items() as $item_id => $item ) {

                // Get the product object
                $product = $item->get_product();

                // Get the product Id
                $product_id = $product->get_id();

                $request_items[] = array(
                    "DRNameEN"=> $item->get_name(),
                    "DRNameAR"=> $item->get_name(),
                    "Length"=> $product->get_length(),
                    "Width"=> $product->get_width(),
                    "Height"=> $product->get_height(),
                    "Weight"=> $product->get_weight()
                );
            }

            $address__ = array();
            if($order->get_shipping_address_1() != ''){
                $address__[] = $order->get_shipping_address_1();
            }
            if($order->get_shipping_address_2() != ''){
                $address__[] = $order->get_shipping_address_2();
            }
            if($order->get_shipping_city() != ''){
                $address__[] = $order->get_shipping_city();
            }
            if($order->get_shipping_state() != ''){
                $address__[] = $order->get_shipping_state();
            }

            if($order->get_shipping_postcode() != ''){
                $address__[] = $order->get_shipping_postcode();
            }

            if($order->get_shipping_country() != ''){
                $address__[] = $order->get_shipping_country();
            }

            if($order->get_payment_method() == 'cod'){
                $request_total = $order->get_total();
            }
            else{
                $request_total = 0;
            }

            $deliver_request_data = array(
                "DestinationCityId"=> $selected_woowes_city['Data']['List'][0]['CityID'],
                "ReferenceNo"=> $order_number,
                "BlockID"=> "",
                "Consignee"=> $order->get_shipping_first_name()." ".$order->get_shipping_last_name(),
                "ConsigneePhone"=> $order->get_billing_phone(),
                "ConsigneeMainAddress"=> implode(', ', $address__),
                "ConsigneeAlternateAddress"=> "",
                "PickupLocationCode"=> "",
                "COD"=> $request_total,
                "DeliveryRequestItem"=> $request_items
            );
            
//echo '<pre>'; print_r($deliver_request_data); echo json_encode($deliver_request_data); die;
            
            $requested__ = $wes_api->woowes_add_delivery_request($deliver_request_data);
            $order->update_meta_data( 'woowes_delivery_request_response', $requested__ );
            $order->update_meta_data( '_thankyou_woowes_action_done', true );

            $order->save();

            $woowes_response = get_post_meta( $order->get_id(), 'woowes_delivery_request_response', true );
            if(!empty($woowes_response)){
                $woowes_response = json_decode($woowes_response, true);

                if(!empty($woowes_response['Data']) && isset($woowes_response['Data']['DeliveryRequestAwb'])){
                    //echo '<h2 class="woocommerce-order-details__title">Tracking Number</h2>';
                    //echo '<p>'.$woowes_response['Data']['DeliveryRequestAwb'].'</p>';
                }
            }
        }
    }

    public function woowes_order_tracking_status($order){
        $woowes_response = get_post_meta( $order->get_id(), 'woowes_delivery_request_response', true );
        if(!empty($woowes_response)){
            $woowes_response = json_decode($woowes_response, true);

            if(!empty($woowes_response['Data']) && isset($woowes_response['Data']['DeliveryRequestAwb'])){

                $wes_api = new Woo_Way_Express_Shipping_API();
				$wes_tracking_ = $wes_api->woowes_order_tracking_data($woowes_response['Data']['DeliveryRequestAwb']);

				$wes_tracking_ = json_decode($wes_tracking_, true);

                if( !empty($wes_tracking_['Data']['List'])){
                    echo '<h2 class="woocommerce-order-details__title">'.__("Tracking Status", "woo-way-express-shipping").'</h2>';

                    foreach($wes_tracking_['Data']['List'] as $item_ ){
                        echo '<p><b>'.__("Tracking Number","woo-way-express-shipping").': </b>'.$item_['TrackNo'].'</p>';
                        if(!empty($item_['Tracking'])){
                            echo '<ul class="woowes_tracking_status_order" style="padding: 0 25px;">';
                            foreach( $item_['Tracking'] as $tracking_____){
                                echo '<li>
                                    <b>'.$tracking_____['Date'].' : </b>'.__($tracking_____['StatusName'], 'woo-way-express-shipping').'
                                </li>';
                            }
                            echo '</ul>';
                        }

                        echo '<a class="wp-element-button button" href="'.$item_['TrackingLink'].'" target="_blank">'.__("Track", "woo-way-express-shipping").'</a>';
                    }
                }

            }
        }
    }

}
