<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.wplauncher.com
 * @since             1.0.0
 * @package           Wplauncher
 *
 * @wordpress-plugin
 * Plugin Name:       WPLauncher
 * Plugin URI:        https://www.wplauncher.com
 * Description:       Simple way to request development work for your website.
 * Version:           1.0.0
 * Author:            Ben Shadle
 * Author URI:        https://www.wplauncher.com/team
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wplauncher
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently pligin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WPLAUNCHER_PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wplauncher-activator.php
 */
function activate_wplauncher() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wplauncher-activator.php';
	Wplauncher_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wplauncher-deactivator.php
 */
function deactivate_wplauncher() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wplauncher-deactivator.php';
	Wplauncher_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wplauncher' );
register_deactivation_hook( __FILE__, 'deactivate_wplauncher' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wplauncher.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wplauncher() {

	$plugin = new Wplauncher();
	$plugin->run();

}
run_wplauncher();
