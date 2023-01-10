<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://cloud1.me/
 * @since      1.0.0
 *
 * @package    Woo_Way_Express_Shipping
 * @subpackage Woo_Way_Express_Shipping/includes
 */

/**
 * The core api class.
 *
 * This is used to define API Call Functions.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woo_Way_Express_Shipping
 * @subpackage Woo_Way_Express_Shipping/includes
 * @author     Gaurav Garg <gauravgargcs1991@email.com>
 */
class Woo_Way_Express_Shipping_API {
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

    public $api_url;

    public $api_token;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct(  ) {

		// $this->plugin_name = $plugin_name;
		// $this->version = $version;

        

        $woowes_options = get_option( 'woowes_setting_options' );
        if($woowes_options['test_mode'] == 1){
            $this->api_url = 'https://testb2b.freightlo.com';
        }else{
            $this->api_url = 'https://b2b.freightlo.com';
        }
        $this->api_token = $woowes_options['auth_access_token']??'';
	}

    public function woowes_login( $username, $password){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->api_url.'/api/Service/LoginClient?UserName='.$username.'&Password='.$password,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'Content-Length: 0'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    public function woowes_get_cities( $name = ''){
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->api_url.'/api/Service/FindAllCityPlugin?Name='.$name,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->api_token
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    public function woowes_add_delivery_request( $deliver_request_data ){
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->api_url.'/api/Service/AddDeliveryRequest',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode($deliver_request_data),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->api_token,
            'Content-Type: application/json'
        ),
        ));

        
        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    public function woowes_print_label( $track_number ){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->api_url.'/api/Service/PrintLabelSingle?TrackNo='.$track_number,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->api_token,
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function woowes_order_tracking_data( $track_number ){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->api_url.'/api/Service/ShipmentStatus?TrackNo='.$track_number,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->api_token,
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function woowes_cancel_request( $track_number ){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->api_url.'/api/Service/CancelByClient?TrackNo='.$track_number,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->api_token,
            'Content-Length: 0'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}