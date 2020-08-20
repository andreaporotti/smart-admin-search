<?php
/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 * @package    Smart_Admin_Search
 * @subpackage Smart_Admin_Search/includes
 */

/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    Smart_Admin_Search
 * @subpackage Smart_Admin_Search/includes
 * @author     Andrea Porotti
 */
class Smart_Admin_Search_Activator {

	/**
	 * Performs tasks on plugin activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// --------------------------
		// Initialize plugin options.
		// --------------------------

		// Keys shortcut to open search modal.
		if ( false === get_option( 'sas_search_keys_shortcut' ) ) {
			add_option( 'sas_search_keys_shortcut', '', '', 'no' );
		}

		// Disabled functions.
		if ( false === get_option( 'sas_disabled_search_functions' ) ) {
			add_option( 'sas_disabled_search_functions', array( 'none' ), '', 'no' );
		}

	}

}
