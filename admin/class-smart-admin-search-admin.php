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
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name . '-select2', plugin_dir_url( __DIR__ ) . 'assets/select2/select2.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/smart-admin-search-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		global $locale;
		$short_locale = substr( $locale, 0, 2 );
		
		wp_enqueue_script( $this->plugin_name . '-select2', plugin_dir_url( __DIR__ ) . 'assets/select2/select2.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name . '-select2-lang', plugin_dir_url( __DIR__ ) . 'assets/select2/i18n/' . $short_locale . '.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smart-admin-search-admin.js', array( 'jquery' ), $this->version, true );

	}
	
	/**
	 * Adds content to admin footer.
	 *
	 * @since    1.0.0
	 */
	public function admin_footer() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/smart-admin-search-admin-search-modal.php';

	}

}
