<?php
/**
 * @package   Cares_BPDocs_Export_Import
 * @author    dcavins
 * @license   GPL-2.0+
 * @link      https://engagementnetwork.org
 * @copyright 2016 CARES Network
 */

/**
 * Output the contents of the shortcode.
 *
 * @since   1.0.0
 *
 * @param   ... Add description of possible params here.
 *
 * @return  html
 */
function cbpdimport_shortcode( $atts ) {
	$a = shortcode_atts( array(
		'var'  => 'default_value',
		'var2' => ''
		), $atts );

	wp_enqueue_script( cbpdimport_get_plugin_slug() . '-plugin-script' );

	ob_start();
	// Create the output.
	// Shortcode output has to go into an output buffer
	return ob_get_clean();
}
add_shortcode( 'cares-bp-docs-export-import', 'cbpdimport_shortcode' );
