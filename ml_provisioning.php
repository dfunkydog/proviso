<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              nlsltd.com
 * @since             1.0.0
 * @package           Ml_provisioning
 *
 * @wordpress-plugin
 * Plugin Name:       Me Learning Provisioning
 * Plugin URI:        nlsltd.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Michael Dyer
 * Author URI:        nlsltd.com
 * Licence:           GPL-2.0+
 * Licence URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ml_provisioning
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
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ml_provisioning-activator.php
 */
function activate_ml_provisioning() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ml_provisioning-activator.php';
	Ml_provisioning_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ml_provisioning-deactivator.php
 */
function deactivate_ml_provisioning() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ml_provisioning-deactivator.php';
	Ml_provisioning_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ml_provisioning' );
register_deactivation_hook( __FILE__, 'deactivate_ml_provisioning' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ml_provisioning.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ml_provisioning() {

	$plugin = new Ml_provisioning();
	$plugin->run();

}
run_ml_provisioning();
