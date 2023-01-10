<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://https://cloud1.me/
 * @since      1.0.0
 *
 * @package    Woo_Way_Express_Shipping
 * @subpackage Woo_Way_Express_Shipping/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woo_Way_Express_Shipping
 * @subpackage Woo_Way_Express_Shipping/includes
 * @author     Gaurav Garg <gauravgargcs1991@gmail.com>
 */
class Woo_Way_Express_Shipping_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woo-way-express-shipping',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
