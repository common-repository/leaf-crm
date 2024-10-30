<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.leaf.my
 * @since             1.0.0
 * @package           Leaf_Crm
 *
 * @wordpress-plugin
 * Plugin Name:       Leaf CRM
 * Plugin URI:        https://wordpress.org/plugins/leaf-crm
 * Description:       Leaf CRM streamlines lead capture and management from Facebook, TikTok, Instagram, Google, enhancing sales team productivity and boosting conversion rates.
 * Version:           1.0.1
 * Author:            Leaf CRM Team
 * Author URI:        https://www.leaf.my/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       leaf-crm
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
define( 'LEAF_CRM_VERSION', '1.0.1' );

global $wpdb;

/**
 * This is where all configuration, constants and static value being stored 
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-leaf-crm-constants.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-leaf-crm-integration-status.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-leaf-crm-activator.php
 */
function activate_leaf_crm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-leaf-crm-activator.php';
	Leaf_Crm_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-leaf-crm-deactivator.php
 */
function deactivate_leaf_crm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-leaf-crm-deactivator.php';
	Leaf_Crm_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_leaf_crm' );
register_deactivation_hook( __FILE__, 'deactivate_leaf_crm' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-leaf-crm.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_leaf_crm() {

	$plugin = new Leaf_Crm();
	$plugin->run();

}
run_leaf_crm();
