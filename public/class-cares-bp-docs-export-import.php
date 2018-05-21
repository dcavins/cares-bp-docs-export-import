<?php
/**
 * CARES Network Data Tools
 *
 * @package   Cares_BPDocs_Export_Import
 * @author    dcavins
 * @license   GPL-2.0+
 * @link      https://engagementnetwork.org
 * @copyright 2016 CARES Network
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `admin/class-group-namespace-admin.php`
 *
 *
 * @package CARES_Network_Data_Tools
 * @author  dcavins
 */
class cbpdimport {

	/**
	 *
	 * The current version of the plugin.
	 *
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $version = '1.0.0';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'cares-bp-docs-export-import';

	/**
	 * Initialize the plugin by setting class properties.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
		$this->version = cbpdimport_get_plugin_version();
		$this->plugin_slug = cbpdimport_get_plugin_slug();
	}

	/**
	 * Hook WordPress filters and actions here.
	 *
	 * @since     1.0.0
	 */
	public function hook_actions() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		// $domain = $this->plugin_slug;
		// $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		// // @TODO: use load_plugin_textdomain instead?
		// load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles_scripts() {
		// Enqueue plugin public styles
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );

		// IE specific
		// global $wp_styles;
		// wp_enqueue_style( $this->plugin_slug . '-ie-plugin-styles', plugins_url( 'css/public-ie.css', __FILE__ ), array(), $this->version );
		// $wp_styles->add_data( $this->plugin_slug . '-ie-plugin-styles', 'conditional', 'lte IE 9' );

		/*
		 * Scripts can be registered now and then enqueued when the shortcode is used/when they're needed.
		 */

		// Use un-minified versions when Debug is true, else use source files.
		// if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		// 	wp_register_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/src/public.js', __FILE__ ), array( 'jquery' ), $this->version, true );
		// } else {
		// 	wp_register_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.min.js', __FILE__ ), array( 'jquery' ), $this->version, true );
		// }

	}
}
