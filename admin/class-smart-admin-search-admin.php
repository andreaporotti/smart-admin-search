<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Smart_Admin_Search
 * @subpackage Smart_Admin_Search/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Smart_Admin_Search
 * @subpackage Smart_Admin_Search/admin
 * @author     Andrea Porotti
 */
class Smart_Admin_Search_Admin {

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
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The list of registered search functions.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $registered_functions    The list of registered search functions.
	 */
	private $registered_functions = array();

	/**
	 * The results from the executed search functions.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $search_results    The results from the executed search functions.
	 */
	private $search_results = array();

	/**
	 * The name of the class containing the search functions.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $search_functions_class    The name of the class containing the search functions.
	 */
	private $search_functions_class = 'Smart_Admin_Search_Functions';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $plugin_slug The slug of this plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_slug, $version ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_slug = $plugin_slug;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_slug . '-select2', plugin_dir_url( __DIR__ ) . 'assets/select2/select2.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'css/smart-admin-search-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		global $locale;
		$short_locale = substr( $locale, 0, 2 );

		wp_enqueue_script( $this->plugin_slug . '-select2', plugin_dir_url( __DIR__ ) . 'assets/select2/select2.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_slug . '-select2-lang', plugin_dir_url( __DIR__ ) . 'assets/select2/i18n/' . $short_locale . '.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_slug . '-admin', plugin_dir_url( __FILE__ ) . 'js/smart-admin-search-admin.js', array( 'jquery' ), $this->version, true );

		wp_localize_script(
			$this->plugin_slug . '-admin',
			'sas_ajax',
			array(
				'search_url' => esc_url_raw( rest_url() ) . $this->plugin_slug . '/v1/search',
				'nonce'      => wp_create_nonce( 'wp_rest' ),
			)
		);

		wp_localize_script(
			$this->plugin_slug . '-admin',
			'sas_strings',
			array(
				'search_select_placeholder' => esc_html__( 'what are you looking for...?', 'smart-admin-search' ),
			)
		);

		// Get plugin options.
		$option_search_keys_shortcut  = get_option( 'sas_search_keys_shortcut', '' );
		$current_search_keys_shortcut = array();
		if ( ! empty( $option_search_keys_shortcut ) && 'none' !== $option_search_keys_shortcut ) {
			$option_search_keys_shortcut_array = explode( ',', $option_search_keys_shortcut );
			foreach ( $option_search_keys_shortcut_array as $key ) {
				$key_data                       = explode( '|', $key );
				$current_search_keys_shortcut[] = $key_data[0];
			}
		}

		wp_localize_script(
			$this->plugin_slug . '-admin',
			'sas_options',
			array(
				'search_keys_shortcut' => $current_search_keys_shortcut,
			)
		);

	}

	/**
	 * Adds content to admin footer.
	 *
	 * @since    1.0.0
	 */
	public function admin_footer() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/smart-admin-search-admin-search-modal.php';

	}

	/**
	 * Registers a REST-API custom endpoint for the main search function.
	 *
	 * @since    1.0.0
	 */
	public function rest_api_register_search() {

		register_rest_route(
			$this->plugin_slug . '/v1',
			'search',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'smart_admin_search' ),
				'permission_callback' => array( $this, 'smart_admin_search_permission_callback' ),
			)
		);

	}

	/**
	 * The permission callback for the main search function.
	 *
	 * @since    1.0.0
	 */
	public function smart_admin_search_permission_callback() {
		return is_user_logged_in();
	}

	/**
	 * The main search function called from the REST-API.
	 *
	 * @since    1.0.0
	 * @param    array $data Request data.
	 */
	public function smart_admin_search( $data ) {

		// Get the search query.
		$query = ( isset( $data['query'] ) ) ? sanitize_text_field( $data['query'] ) : '';

		// Get disabled functions.
		$disabled_functions = get_option( 'sas_disabled_search_functions', array() );

		// Register search functions.
		$this->register_functions();

		// Get the search functions class.
		$search_functions_class = new $this->search_functions_class();

		// Run search functions.
		foreach ( $this->registered_functions as $function ) {

			// Skip disabled functions.
			if ( ! in_array( $function['name'], $disabled_functions, true ) ) {
				$this->search_results = $search_functions_class->{ $function['name'] }( $this->search_results, $query );
			}
		}

		// Add numeric IDs to the results.
		if ( ! empty( $this->search_results ) ) {
			$id = 1;

			foreach ( $this->search_results as $key => $result ) {
				$this->search_results[ $key ]['id'] = $id;
				$id++;
			}
		}

		return $this->search_results;

	}

	/**
	 * Registers the search functions.
	 *
	 * @since    1.0.0
	 */
	public function register_functions() {

		// Get all the search functions class methods.
		$search_functions_class_methods = get_class_methods( $this->search_functions_class );

		// Get the search functions class.
		$search_functions_class = new $this->search_functions_class();

		// Run search functions class methods that register the search functions.
		$register_method_prefix = 'register_';

		foreach ( $search_functions_class_methods as $method ) {

			// If the method name starts with the correct prefix, run it.
			if ( substr( $method, 0, strlen( $register_method_prefix ) ) === $register_method_prefix ) {
				$this->registered_functions = $search_functions_class->{ $method }( $this->registered_functions );
			}
		}

	}

	/**
	 * Retrieve the registered search functions.
	 *
	 * @since     1.0.0
	 * @param     bool $run_registration    If true, runs the functions registration.
	 *
	 * @return    array    The registered functions.
	 */
	public function get_registered_functions( $run_registration = false ) {
		if ( $run_registration ) {
			$this->register_functions();
		}

		return $this->registered_functions;
	}

}
