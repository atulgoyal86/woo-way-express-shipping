<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://cloud1.me/
 * @since             1.0.0
 * @package           Woo_Way_Express_Shipping
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Way Express Shipping
 * Plugin URI:        https://https://cloud1.me/
 * Description:       Woo Way Express Shipping
 * Version:           1.0.0
 * Author:            Gaurav Garg
 * Author URI:        https://https://cloud1.me/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-way-express-shipping
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WOO_WAY_EXPRESS_SHIPPING_VERSION', '1.0.0' );

define('WOOWES_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('WOOWES_PLUGIN_URL',plugin_dir_url( __FILE__ ));
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-way-express-shipping-activator.php
 */
function activate_woo_way_express_shipping() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-way-express-shipping-activator.php';
    // Check if Woo is active
    if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
       	wp_die( __( 'Please activate WooCommerce.', 'textdomain' ) );

    }
    else{
		Woo_Way_Express_Shipping_Activator::activate();
	}
	
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-way-express-shipping-deactivator.php
 */
function deactivate_woo_way_express_shipping() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-way-express-shipping-deactivator.php';
	Woo_Way_Express_Shipping_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_way_express_shipping' );
register_deactivation_hook( __FILE__, 'deactivate_woo_way_express_shipping' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-way-express-shipping.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_way_express_shipping() {

	$plugin = new Woo_Way_Express_Shipping();
	$plugin->run();

}
run_woo_way_express_shipping();
