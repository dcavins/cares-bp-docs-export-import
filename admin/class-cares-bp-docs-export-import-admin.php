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
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `public/class-cares-bp-docs-export-import.php`
 *
 * @package   Cares_BPDocs_Export_Import_Admin
 * @author  dcavins
 */
class cbpdimport_Admin {

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

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
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		$this->version = cbpdimport_get_plugin_version();

	}

	/**
	 * Hook WordPress filters and actions here.
	 *
	 * @since     1.0.0
	 */
	public function hook_actions() {
		// Load admin style sheet and JavaScript.
		// add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add settings
		add_action( 'admin_init', array( $this, 'settings_init' ) );

		add_action( 'admin_init', array( $this, 'maybe_run_stats' ) );


		// Add an action link pointing to the options page.
		// $plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		// add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );

			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		// $this->plugin_screen_hook_suffix = add_options_page(
		// 	__( 'Data Tools', 'cares-bp-docs-export-import' ),
		// 	__( 'Data Tools', 'cares-bp-docs-export-import' ),
		// 	'manage_options',
		// 	$this->plugin_slug,
		// 	array( $this, 'display_plugin_admin_page' )
		// );

		// Settings
		$this->plugin_screen_hook_suffix = add_submenu_page(
			'edit.php?post_type=' . bp_docs_get_post_type_name(),
			__( 'BuddyPress Docs Export/Import', 'buddypress-docs' ),
			__( 'Export/Import', 'buddypress-docs' ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		global $wpdb;
		$bp = buddypress();
		?>
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<?php
			// settings_fields( $this->plugin_slug );
			// do_settings_sections( $this->plugin_slug );
			// submit_button();
			?>
			<form name="single-hub-docs-export" id="single-hub-docs-export" class="standard-form" action="<?php
				// URL needs to have the stat we're requesting and be nonced.
				echo wp_nonce_url( add_query_arg(
					array(
						'page' => $this->plugin_slug,
						'stat' => 'single-hub-docs-export'
					),
					admin_url( 'options.php' )
				), 'cc-stats-' . get_current_user_id() );
			?>" method="post">
				<label for="group_id"><strong>Export the docs from a single hub.</strong></label><br />
				<?php
				echo $this->plugin_screen_hook_suffix;
					$groups = $wpdb->get_results( "SELECT id, name	FROM {$bp->groups->table_name} ORDER BY	name ASC" );
					if ( $groups ) {
						?>
						<select name="group_id" id="hub-docs-export-list-select" class="chosen-select" data-placeholder="Choose a hub..." style="width:75%;">
						<!-- Include an empty option for chosen.js support-->
						<option></option>
						<?php
						foreach ( $groups as $group ) {
							?>
							<option value="<?php echo $group->id; ?>"><?php echo $group->name; ?></option>
							<?php
						}
						?>
						</select><br />
						<?php
					}
				?>
				<input type="submit" value="Create Export File">
			</form>

		<?php
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', 'cares-bp-docs-export-import' ) . '</a>'
			),
			$links
		);

	}

	/**
	 * Register the settings and set up the sections and fields for the
	 * global settings screen.
	 *
	 * @since    1.0.0
	 */
	public function settings_init() {

		// Color customizations.
		// add_settings_section(
		// 	'cdt_custom_colors',
		// 	__( 'Update the key colors in the Data Tools window to match this site\'s theme.', 'cares-bp-docs-export-import' ),
		// 	array( $this, 'cdt_custom_colors_section_callback' ),
		// 	$this->plugin_slug
		// );

		// register_setting( $this->plugin_slug, 'cdt_custom_colors', array( $this, 'sanitize_custom_colors' ) );
		// add_settings_field(
		// 	'cdt_custom_colors',
		// 	__( 'Customize the colors.', 'cares-bp-docs-export-import' ),
		// 	array( $this, 'render_color_customization' ),
		// 	$this->plugin_slug,
		// 	'cdt_custom_colors'
		// );
	}

	/**
	 * Provide a section description for the global settings screen.
	 *
	 * @since    1.0.0
	 */
	public function cdt_custom_colors_section_callback() {}


	/**
	 * Check for requests that stats be run.
	 *
	 * @since    1.0.0
	 */
	public function maybe_run_stats() {
		global $plugin_page;

	$towrite = PHP_EOL . '$this->plugin_slug' . print_r( $this->plugin_slug, TRUE );
	$towrite .= PHP_EOL . '$tplugin_page' . print_r( $plugin_page, TRUE );
	$fp = fopen('/Users/dcavins/Sites/commons-export/wp-content/docs-export.txt', 'a');
	fwrite($fp, $towrite);
	fclose($fp);

		// Has anything been requested? Is this our screen?
		if ( ! isset( $_REQUEST['stat'] ) || $this->plugin_slug != $plugin_page ) {
			return;
		}

		// Is the nonce good?
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'cc-stats-' . get_current_user_id() ) ) {
			wp_safe_redirect( add_query_arg(
				array(
					'post_type' => bp_docs_get_post_type_name(),
					'page' => $this->plugin_slug,
					'error' => 'bad-nonce'
				),
				admin_url( 'edit.php' ) ) );
			exit;
		}

		// Run the stat.
		switch ( $_REQUEST['stat'] ) {
			case 'single-hub-docs-export':
				$this->run_hub_docs_export();
				break;
			default:
				// Do nothing if we don't know what we're doing.
				wp_safe_redirect( add_query_arg(
					array(
						'post_type' => bp_docs_get_post_type_name(),
						'page' => $this->plugin_slug,
						'error' =>'unknown-stat'
					),
					admin_url( 'edit.php' ) ) );
				exit;
				break;
		}
	}

	/**
	 * Create the single hub member list CSV when requested.
	 *
	 * @since    1.3.0
	 */
	public function run_hub_docs_export() {
		global $wpdb;
		$bp = buddypress();

		// Which group?
		$group_id = isset( $_POST['group_id'] ) ? (int) $_POST['group_id'] : 0;

		// Output headers so that the file is downloaded rather than displayed.
		header('Content-Type: text/txt; charset=utf-8');
		header('Content-Disposition: attachment; filename=cc-hub-export-docs-' . $group_id . '.txt');

		// Create a file pointer connected to the output stream.
		//add BOM to fix UTF-8 in Excel
		// fputs( $output, $bom = ( chr(0xEF) . chr(0xBB) . chr(0xBF) ) );
		$data = array();
		$doc_args = array(
			// 'doc_id'         => array(),      // Array or comma-separated string
			// 'doc_slug'       => $d_doc_slug,  // String (post_name/slug)
			'group_id'       => $group_id,  // Array or comma-separated string
			// 'parent_id'      => $d_parent_id, // int
			// 'folder_id'      => $d_folder_id, // array or comma-separated string
			// 'author_id'      => $d_author_id, // Array or comma-separated string
			// 'edited_by_id'   => $d_edited_by_id, // Array or comma-separated string
			// 'tags'           => $d_tags,      // Array or comma-separated string
			// 'order'          => $d_order,        // ASC or DESC
			// 'orderby'        => $d_orderby,   // 'modified', 'title', 'author', 'created'
			// 'paged'	         => $d_paged,
			'posts_per_page' => -1,
			// 'search_terms'   => $d_search_terms,
			// 'update_attachment_cache' => false,
		);
		if ( bp_docs_has_docs( $doc_args ) ) {
			while ( bp_docs_has_docs() ) {
				bp_docs_the_doc();
				$doc_id = get_the_ID();

				$taxonomies = array('bp_docs_associated_item', 'bp_docs_access', 'bp_docs_comment_access', 'bp_docs_tag', 'bp_docs_type');
				$tax_terms = array();
				foreach ( $taxonomies as $tax_name ) {
					$tax_terms[$tax_name] = array();
					$terms = get_the_terms( $doc_id, $tax_name );
					if ( $terms ) {
						foreach ( $terms as $term ) {
							$tax_terms[$tax_name][] = array( 'term_id' => $term->term_id, 'slug' => $term->slug );
						}
					}
				}

				$meta = get_post_meta( $doc_id );
				// Add the email address of the user--it's portable.
				if ( ! empty( $meta['bp_docs_last_editor'] ) ) {
					$last_editor_obj = get_user_by( 'id', $meta['bp_docs_last_editor'][0] );
					$meta['bp_docs_last_editor_email'] = array( $last_editor_obj->user_email );
				}
				// unserialize the doc settings
				if ( ! empty( $meta['bp_docs_settings'] ) ) {
					$meta['bp_docs_settings'][0] = maybe_unserialize( $meta['bp_docs_settings'][0] );
				}

				$data[get_the_ID()] = array(
					'title'   => get_the_title(),
					'content' => get_the_content(),
					'terms'   => $tax_terms,
					'meta'    => $meta,
				);
			}
		}

		$output = fopen( 'php://output', 'w' );
		fwrite ( $output, json_encode( $data ) );
		fclose( $output );
		exit();
	}


}
