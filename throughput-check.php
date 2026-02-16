<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wpsani.store
 * @since             1.0.0
 * @package           Throughput_Check
 *
 * @wordpress-plugin
 * Plugin Name:       Throughput Check
 * Description:       A simple and easy-to-use throughput check plugin for WordPress.
 * Version:           1.0.0
 * Plugin URI:        https://wpsani.store/downloads/throughput-check-free/
 * Author:            sani060913
 * Author URI:        https://wpsani.store/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       throughput-check
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'THROUGHPUT_CHECK_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-throughput-check-activator.php
 */
function throughput_check_activate() {
	$activator_path = plugin_dir_path( __FILE__ ) . 'includes/class-throughput-check-activator.php';
	if ( file_exists( $activator_path ) ) {
		require_once $activator_path;
		Throughput_Check_Activator::activate();
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-league-standings-widget-deactivator.php
 */
function throughput_check_deactivate() {
	$deactivator_path = plugin_dir_path( __FILE__ ) . 'includes/class-throughput-check-deactivator.php';
	if ( file_exists( $deactivator_path ) ) {
		require_once $deactivator_path;
		Throughput_Check_Deactivator::deactivate();
	}
}

register_activation_hook( __FILE__, 'throughput_check_activate' );
register_deactivation_hook( __FILE__, 'throughput_check_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-throughput-check.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function throughput_check_run() {

	$plugin = new Throughput_Check();
	$plugin->run();
}
throughput_check_run();