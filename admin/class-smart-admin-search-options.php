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
	 * The slug of options menu.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The slug of options menu.
	 */
	private $options_slug;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $plugin_slug The slug of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_slug ) {

		$this->plugin_name  = $plugin_name;
		$this->plugin_slug  = $plugin_slug;
		$this->options_slug = $this->plugin_slug . '_options';

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
				__( '%s Settings', 'smart-admin-search' ),
				$this->plugin_name
			),
			$this->plugin_name,
			'manage_options',
			$this->options_slug,
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

		if ( current_user_can( 'manage_options' ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/smart-admin-search-admin-options-page.php';
		}

	}

	/**
	 * Adds the plugin options to the options page.
	 *
	 * @since    1.0.0
	 */
	public function options_init() {
		
		// ----------------------------------------
		// Enable or disable search functions.
		// ----------------------------------------
		
		// Add a section.
		add_settings_section(
			'sas_options_section_search_functions',
			esc_html__( 'Search functions', 'smart-admin-search' ),
			array(
				$this,
				'options_section_search_functions',
			),
			$this->options_slug
		);
		
		// Register a setting.
		register_setting(
			$this->options_slug,
			'sas_disabled_search_functions',
			array(
				'type'              => 'array',
				'show_in_rest'      => false,
				'default'           => array(),
				'sanitize_callback' => array(
					$this,
					'option_disabled_search_functions_sanitize',
				),
			)
		);
		
		// Add setting field to the section.
		add_settings_field(
			'sas_disabled_search_functions',
			esc_html__( 'Select the fuctions to run on each search', 'smart-admin-search' ),
			array(
				$this,
				'option_disabled_search_functions',
			),
			$this->options_slug,
			'sas_options_section_search_functions',
			array(
				'name' => 'sas_disabled_search_functions',
			)
		);

	}
	
	/**
	 * Callback for the Search functions options section output.
	 *
	 * @since    1.0.0
	 * @param    array $args Array of section attributes.
	 */
	public function options_section_search_functions( $args ) {

		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php echo esc_html__( 'Settings about the available search functions.', 'smart-admin-search' ); ?>
		</p>
		<?php

	}

	/**
	 * Callback for the disabled_search_functions option value sanitization.
	 *
	 * @since    1.0.0
	 * @param    array $value Option value.
	 */
	public function option_disabled_search_functions_sanitize( $value ) {

		// Get the registered functions.
		$admin                      = new Smart_Admin_Search_Admin( '', '', '' );
		$registered_functions       = $admin->get_registered_functions( true );
		$registered_functions_names = array_column( $registered_functions, 'name' );

		// Get registered functions disabled by the user.
		if ( empty( $value ) ) {
			// All functions are disabled.
			$disabled_functions = $registered_functions_names;
		} else {
			$disabled_functions = array_diff( $registered_functions_names, $value );
		}

		return ( ! empty( $disabled_functions ) ) ? $disabled_functions : array( 'none' );

	}

	/**
	 * Callback for the disabled_search_functions option field output.
	 *
	 * @since    1.0.0
	 * @param    array $args Array of field attributes.
	 */
	public function option_disabled_search_functions( $args ) {

		// Get the option value.
		$option_disabled_search_functions = get_option( $args['name'], array() );

		// Get the registered functions.
		$admin                = new Smart_Admin_Search_Admin( '', '', '' );
		$registered_functions = $admin->get_registered_functions( true );
		
		?>
		<fieldset>
			<?php foreach ( $registered_functions as $function ) : ?>
				<?php $id_attr = $args['name'] . '_' . $function['name']; ?>
				<?php $checked_attr = ( ! in_array( $function['name'], $option_disabled_search_functions, true ) ) ? 'checked' : ''; ?>
				
				<input type="checkbox" id="<?php echo esc_attr( $id_attr ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>[]" value="<?php echo esc_attr( $function['name'] ); ?>" <?php echo esc_attr( $checked_attr ); ?>>
				<label for="<?php echo esc_attr( $id_attr ); ?>"><?php echo esc_html( $function['display_name'] ); ?></label>
				<p class="description">
					<?php echo esc_html( $function['description'] ); ?>
				</p>
				<br>
			<?php endforeach; ?>
		</fieldset>
		<?php
	}

}
