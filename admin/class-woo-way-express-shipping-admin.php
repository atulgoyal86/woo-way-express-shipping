<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://cloud1.me/
 * @since      1.0.0
 *
 * @package    Woo_Way_Express_Shipping
 * @subpackage Woo_Way_Express_Shipping/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Way_Express_Shipping
 * @subpackage Woo_Way_Express_Shipping/admin
 * @author     Gaurav Garg <gauravgargcs1991@gmail.com>
 */
class Woo_Way_Express_Shipping_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        $woowes_options = get_option( 'woowes_setting_options' );

        add_action('admin_menu', array($this, 'woowes_settings_submenu_page'));

        //Add Setting Page fields
		add_action( 'admin_init', array($this, 'woowes_settings_init') );

        if(isset($woowes_options['auth_access_token']) && $woowes_options['auth_access_token'] != ''){
            add_filter( 'manage_edit-shop_order_columns', array($this, 'woowes_wc_order_sipping_label_column') );
            add_filter( 'manage_shop_order_posts_custom_column', array($this, 'woowes_wc_order_sipping_label_column_content'), 20, 2 );

            //add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'woowes_add_shipping_label_button'));
            
            //add_action( 'woocommerce_order_status_cancelled', array($this, 'woowes_cancel_request'), 21, 1 );

            add_action( 'add_meta_boxes', array($this, 'woowes_register_meta_boxes') );
        }

		
    }

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-way-express-shipping-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-way-express-shipping-admin.js', array( 'jquery' ), $this->version, false );
        wp_localize_script( $this->plugin_name, 'woowes_ajax_object',
            array( 
                'ajaxurl' => admin_url( 'admin-ajax.php' )
            )
        );
	}

    public function woowes_settings_submenu_page(){
        add_submenu_page(
		'woocommerce',
		'Way Express Settings',
		'Way Express Settings',
		'manage_options',
		'woowes-settings',
		array( $this, 'woowes_settings_submenu_page_callback') );
    }

    public function woowes_settings_submenu_page_callback(){

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $woowes_options = get_option( 'woowes_setting_options' );
        
        if ( isset( $_GET['settings-updated'] ) ) {
            add_settings_error( 'woowes_setting_messages', 'woowes_setting_message', __( 'Settings Saved' ), 'updated' );
        }
        settings_errors( 'woowes_setting_messages' );

        require_once WOOWES_PLUGIN_DIR . 'admin/partials/woowes-admin-settings.php';
    }

    public function woowes_settings_init(){
        register_setting( 'woowes_setting', 'woowes_setting_options' );
    }

	public function woowes_auth_generate_token(){
		
		$wes_login_ = array();
		
		if(isset($_POST['username_']) && isset($_POST['password_'])){
			$wes_api = new Woo_Way_Express_Shipping_API();
			$wes_login_ = $wes_api->woowes_login($_POST['username_'], $_POST['password_']);
			$wes_login_ = json_decode($wes_login_, true);
			
		}
		
		wp_send_json($wes_login_); die;
	}

	public function woowes_wc_order_sipping_label_column( $columns ){
		$columns['woowes_shipping_label'] = __('Shipping Label');
        $columns['woowes_tracking'] = __('Track');
    	return $columns;
	}

	public function woowes_wc_order_sipping_label_column_content( $column, $post_id ){
		switch ( $column )
		{
			case 'woowes_shipping_label' :
				// Get custom post meta data
				$woowes_response = get_post_meta( $post_id, 'woowes_delivery_request_response', true );
				if(!empty($woowes_response)){
					$woowes_response = json_decode($woowes_response, true);

					if(!empty($woowes_response['Data']) && isset($woowes_response['Data']['DeliveryRequestAwb'])){
						$wes_api = new Woo_Way_Express_Shipping_API();
						$wes_label_ = $wes_api->woowes_print_label($woowes_response['Data']['DeliveryRequestAwb']);

						$wes_label_ = json_decode($wes_label_, true);

						if(!empty($wes_label_) && isset($wes_label_['LabelLink']) && $wes_label_['LabelLink'] != 'TrackNo is not exists'){
							echo '<a href="'.$wes_label_['LabelLink'].'" target="_blank">Shipping Label</a>';
						}
					}
				}
				
				break;
            
            case 'woowes_tracking' :
                // Get custom post meta data
                $woowes_response = get_post_meta( $post_id, 'woowes_delivery_request_response', true );
                if(!empty($woowes_response)){
                    $woowes_response = json_decode($woowes_response, true);

                    if(!empty($woowes_response['Data']) && isset($woowes_response['Data']['DeliveryRequestAwb'])){
                        $wes_api = new Woo_Way_Express_Shipping_API();
                        $wes_tracking_ = $wes_api->woowes_order_tracking_data($woowes_response['Data']['DeliveryRequestAwb']);

				        $wes_tracking_ = json_decode($wes_tracking_, true);

                        if( !empty($wes_tracking_['Data']['List'])){
                            foreach($wes_tracking_['Data']['List'] as $item_ ){
                                echo '<a href="'.$item_['TrackingLink'].'" target="_blank">Track</a>';
                               
                            }
                        }
                        
                    }
                }
                
                break;
		}
	}

	public function woowes_add_shipping_label_button($order){
		$woowes_response = get_post_meta( $order->get_id(), 'woowes_delivery_request_response', true );
		if(!empty($woowes_response)){
			$woowes_response = json_decode($woowes_response, true);

			if(!empty($woowes_response['Data']) && isset($woowes_response['Data']['DeliveryRequestAwb'])){
				echo '<p><b>Tracking Number: </b>'.$woowes_response['Data']['DeliveryRequestAwb'].'</p>';
				$wes_api = new Woo_Way_Express_Shipping_API();
				$wes_label_ = $wes_api->woowes_print_label($woowes_response['Data']['DeliveryRequestAwb']);

				
				$wes_label_ = json_decode($wes_label_, true);

				if(!empty($wes_label_) && isset($wes_label_['LabelLink']) && $wes_label_['LabelLink'] != 'TrackNo is not exists'){
					echo '<a href="'.$wes_label_['LabelLink'].'" target="_blank">Shipping Label</a>';
				}
			}
		}
	}

    public function woowes_register_meta_boxes(){
        add_meta_box( 
            'woowes-order-box', 
            __( 'Way Express Shipping' ), 
            array($this, 'woowes_order_box_display_callback'), 
            'shop_order',
            'side' 
        );
    }

    public function woowes_order_box_display_callback( $post ){

        $woowes_response = get_post_meta( $post->ID, 'woowes_delivery_request_response', true );

		if(!empty($woowes_response)){
            
			$woowes_response = json_decode($woowes_response, true);

			if(!empty($woowes_response['Data']) && isset($woowes_response['Data']['DeliveryRequestAwb'])){
                $wes_api = new Woo_Way_Express_Shipping_API();

                if(isset($_GET['woowes_action']) && $_GET['woowes_action'] == 'cancel-shipment'){
                    $woowes_is_shipment_cancelled = get_post_meta( $post->ID, 'woowes_is_shipment_cancelled', true );
                    if(!isset($woowes_is_shipment_cancelled) || $woowes_is_shipment_cancelled != 'yes'){
                        $order = wc_get_order( $post->ID );

                        $wes_cancel_request_ = $wes_api->woowes_cancel_request($woowes_response['Data']['DeliveryRequestAwb']);

                        $order->add_order_note( $wes_cancel_request_ );

                        $wes_cancel_request_ = json_decode($wes_cancel_request_, true);

                        if(!empty($wes_cancel_request_) && isset($wes_cancel_request_['Status']) && $wes_cancel_request_['Status'] == true){
                            update_post_meta( $post->ID, 'woowes_is_shipment_cancelled', 'yes' );
                        }
                    }
                    //echo '<script>location.replace("'.$_GET['woowes_redirect'].'");</script>';
                }

                $woowes_is_shipment_cancelled = get_post_meta( $post->ID, 'woowes_is_shipment_cancelled', true );

				echo '<p><b>Tracking Number: </b>'.$woowes_response['Data']['DeliveryRequestAwb'].'</p>';
				
				$wes_label_ = $wes_api->woowes_print_label($woowes_response['Data']['DeliveryRequestAwb']);

				
				$wes_label_ = json_decode($wes_label_, true);

				if(!empty($wes_label_) && isset($wes_label_['LabelLink']) && $wes_label_['LabelLink'] != 'TrackNo is not exists'){
					echo '<a style=" background: #000; text-decoration: none; color: #fff; padding: 5px 15px; display: block; width: fit-content; " href="'.$wes_label_['LabelLink'].'" target="_blank"><span class="dashicons dashicons-media-document"></span> Shipping Label</a>';
				}

                if(!isset($woowes_is_shipment_cancelled) || $woowes_is_shipment_cancelled != 'yes'){
                    echo '<a style=" background: #e73f3f;margin-top: 10px; text-decoration: none; color: #fff; padding: 5px 15px; display: block; width: fit-content; " href="'.get_edit_post_link($post->ID).'&woowes_action=cancel-shipment"><span class="dashicons dashicons-no-alt"></span> Cancel Shipment</a>';
                }
                else if($woowes_is_shipment_cancelled == 'yes'){
                    echo '<a style=" background: #e73f3f;margin-top: 10px; text-decoration: none; color: #fff; padding: 5px 15px; display: block; width: fit-content; " href="#">Shipment Cancelled</a>';
                }
			}
		}
    }

    public function woowes_cancel_request( $order_id ){
        $woowes_response = get_post_meta( $order_id, 'woowes_delivery_request_response', true );
        if(!empty($woowes_response)){
			$woowes_response = json_decode($woowes_response, true);

			if(!empty($woowes_response['Data']) && isset($woowes_response['Data']['DeliveryRequestAwb'])){
                $order = wc_get_order( $order_id );

                $wes_api = new Woo_Way_Express_Shipping_API();
				$wes_cancel_request_ = $wes_api->woowes_cancel_request($woowes_response['Data']['DeliveryRequestAwb']);

                $order->add_order_note( $wes_cancel_request_ );
            }
        }
        
    }

}
