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
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'smart_admin_search' ),
			)
		);

	}

	/**
	 * Creates a list of functions added by the plugin hook.
	 *
	 * @since    1.0.0
	 */
	public function get_added_functions() {

		global $wp_filter;

		$added_functions = array();

		array_walk(
			$wp_filter['smart_admin_search_add_function']->callbacks,
			function( $item, $key ) use ( &$added_functions ) {
				foreach ( $item as $callback ) {
					if ( is_string( $callback['function'] ) ) {
						$added_functions[] = $callback['function'];
					} elseif ( is_array( $callback['function'] ) ) {
						$added_functions[] = $callback['function'][1];
					}
				}
			}
		);

		return $added_functions;

	}

	/**
	 * The main search function called from the REST-API.
	 *
	 * @since    1.0.0
	 * @param    array $data Request data.
	 */
	public function smart_admin_search( $data ) {

		if ( is_user_logged_in() ) {

			// Get the search query.
			$query = ( isset( $data['query'] ) ) ? sanitize_text_field( $data['query'] ) : '';

			// Register search functions.
			$this->registered_functions = apply_filters( 'smart_admin_search_register_function', $this->registered_functions );

			// Get functions added to the "add" hook.
			$added_functions = $this->get_added_functions();

			// Remove functions added but not registered.
			foreach ( $added_functions as $function ) {
				$key = array_search( $function, array_column( $this->registered_functions, 'name' ), true );

				if ( false === $key ) {
					remove_filter( 'smart_admin_search_add_function', $function );
				}
			}

			// Run search functions.
			$this->search_results = apply_filters( 'smart_admin_search_add_function', $this->search_results, $query );

			// Add numeric IDs to the results.
			if ( ! empty( $this->search_results ) ) {
				$id = 1;

				foreach ( $this->search_results as $key => $result ) {
					$this->search_results[ $key ]['id'] = $id;
					$id++;
				}
			}

			return $this->search_results;

		} else {

			return 'Access denied.';

		}

	}

}
