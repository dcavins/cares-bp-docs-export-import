<?php
/**
 *
 * @package   Cares_BPDocs_Export_Import
 * @author    dcavins
 * @license   GPL-2.0+
 * @link      https://engagementnetwork.org
 * @copyright 2016 CARES Network
 *
 * @wordpress-plugin
 * Plugin Name:       Cares BP Docs Export Import
 * Plugin URI:        @TODO
 * Description:       Export and import BP Docs.
 * Version:           1.0.0
 * Author:            dcavins
 * Text Domain:       cares-bp-docs-export-import
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/careshub/cares-bp-docs-export-import
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

function cbpdimport_init() {

	$base_path = plugin_dir_path( __FILE__ );

	// Functions
	require_once( $base_path . 'includes/functions.php' );

	// Template output functions
	// require_once( $base_path . 'public/views/template-tags.php' );
	// require_once( $base_path . 'public/views/shortcodes.php' );

	// The main class
	require_once( $base_path . 'public/class-cares-bp-docs-export-import.php' );
	$cbpdimport = new cbpdimport();
	// Add the action and filter hooks.
	$cbpdimport->hook_actions();

	// Admin and dashboard functionality
	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		require_once( $base_path . 'admin/class-cares-bp-docs-export-import-admin.php' );
		$cbpdimport_admin = new cbpdimport_Admin();
		// Add the action and filter hooks.
		$cbpdimport_admin->hook_actions();
	}

}
add_action( 'init', 'cbpdimport_init' );

/*
 * Helper function.
 * @return Fully-qualified URI to the root of the plugin.
 */
function cbpdimport_get_plugin_base_uri(){
	return plugin_dir_url( __FILE__ );
}

/*
 * Helper function.
 * @TODO: Update this when you update the plugin's version above.
 *
 * @return string Current version of plugin.
 */
function cbpdimport_get_plugin_version(){
	return '1.0.0';
}
function cbpdimport_get_plugin_slug(){
	return 'cares-bp-docs-export-import';
}
