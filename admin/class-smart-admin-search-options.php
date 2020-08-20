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
		
		// -------------------------------------------
		// Set keys shortcut to open the search modal.
		// -------------------------------------------
		
		// Add a section.
		add_settings_section(
			'sas_options_section_keys_shortcuts',
			esc_html__( 'Keyboard shortcuts', 'smart-admin-search' ),
			array(
				$this,
				'options_section_keys_shortcut',
			),
			$this->options_slug
		);
		
		// Register a setting.
		register_setting(
			$this->options_slug,
			'sas_search_keys_shortcut',
			array(
				'type'              => 'string',
				'show_in_rest'      => false,
				'default'           => '',
				'sanitize_callback' => array(
					$this,
					'option_search_keys_shortcut_sanitize',
				),
			)
		);
		
		// Add setting field to the section.
		add_settings_field(
			'sas_search_keys_shortcut',
			esc_html__( 'Open search box', 'smart-admin-search' ),
			array(
				$this,
				'option_search_keys_shortcut',
			),
			$this->options_slug,
			'sas_options_section_keys_shortcuts',
			array(
				'name' => 'sas_search_keys_shortcut',
			)
		);
		
		// -----------------------------------
		// Enable or disable search functions.
		// -----------------------------------
		
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
	 * Callback for the Keys shortcuts options section output.
	 *
	 * @since    1.0.0
	 * @param    array $args Array of section attributes.
	 */
	public function options_section_keys_shortcut( $args ) {

		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php echo esc_html__( 'Configure keyboard shortcuts to access plugin functions.', 'smart-admin-search' ); ?>
		</p>
		<?php

	}
	
	/**
	 * Callback for the search_keys_shortcut option value sanitization.
	 *
	 * @since    1.0.0
	 * @param    array $value Option value.
	 */
	public function option_search_keys_shortcut_sanitize( $value ) {

		return $value;

	}
	
	/**
	 * Callback for the search_keys_shortcut option field output.
	 *
	 * @since    1.0.0
	 * @param    array $args Array of field attributes.
	 */
	public function option_search_keys_shortcut( $args ) {
		
		// Get the option value.
		$option_search_keys_shortcut = get_option( $args['name'], array() );
		
		// Create a readable version of the current shortcut.
		$option_search_keys_shortcut_array = explode( ',', $option_search_keys_shortcut );
		$current_search_keys_shortcut = '';
		foreach ( $option_search_keys_shortcut_array as $key ) {
			$key_data = explode( '|', $key );
			
			if ( empty( $current_search_keys_shortcut ) ) {
				$current_search_keys_shortcut = $key_data[1];
			} else {
				$current_search_keys_shortcut .= ' + ' . $key_data[1];
			}
		}
		
		?>
		<fieldset>
			<input type="text" id="sas-capture-search-keys" class="regular-text sas-skip-global-keypress" value="">
			<button type="button" id="sas-capture-search-keys-reset" class="button"><?php echo esc_html__( 'Reset', 'smart-admin-search' ); ?></button>
			<input type="hidden" id="<?php echo esc_attr( $args['name'] ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" value="<?php echo esc_attr( $option_search_keys_shortcut ); ?>">
			<p class="description">
				<?php echo esc_html__( 'Click on the textbox and then press on the keyboard the keys that you will use to open the search box. Click the Reset button to clear the textbox.', 'smart-admin-search' ); ?>
				<br>
				<?php echo esc_html__( 'The current shortcut is:', 'smart-admin-search' ); ?> <strong><?php echo esc_html( $current_search_keys_shortcut ); ?></strong>.
			</p>
		</fieldset>
		<?php
		
	}
	
	// ------------------------------------------------------------------------
	
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
		
		// Sort functions by display name.
		usort(
			$registered_functions,
			function ( $item1, $item2 ) {
				if ( $item1['display_name'] == $item2['display_name'] ) {
					return 0;
				}
				return $item1['display_name'] < $item2['display_name'] ? -1 : 1;
			}
		);
		
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
