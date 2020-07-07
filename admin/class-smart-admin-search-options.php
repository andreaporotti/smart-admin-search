<?php
/**
 * The options-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Smart_Admin_Search
 * @subpackage Smart_Admin_Search/admin
 */

/**
 * The options-specific functionality of the plugin.
 *
 * Configures the options page and registers the settings.
 *
 * @package    Smart_Admin_Search
 * @subpackage Smart_Admin_Search/admin
 * @author     Andrea Porotti
 */
class Smart_Admin_Search_Options {
	
	/**
	 * The name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The name of this plugin.
	 */
	private $plugin_name;
	
	/**
	 * The slug of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The slug of this plugin.
	 */
	private $plugin_slug;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $plugin_slug The slug of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_slug ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_slug = $plugin_slug;

	}

	/**
	 * Adds the plugin options page as sub-item in the Settings menu.
	 *
	 * @since    1.0.0
	 */
	public function options_menu() {

		add_options_page(
			sprintf(
				/* translators: %s is the plugin name */
				__( '%s Settings', 'jamp' ),
				$this->plugin_name
			),
			$this->plugin_name,
			'manage_options',
			$this->plugin_slug . '-options',
			array(
				$this,
				'options_page',
			)
		);

	}

	/**
	 * Shows the options page content.
	 *
	 * @since    1.0.0
	 */
	public function options_page() {

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {

			return;

		}

		// Load page code.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/smart-admin-search-admin-options-page.php';

	}

	/**
	 * Adds the plugin options to the options page.
	 *
	 * @since    1.0.0
	 */
	public function options_init() {

	}
	
}
