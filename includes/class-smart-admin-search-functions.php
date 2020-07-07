<?php
/**
 * The custom search functions.
 *
 * @since      1.0.0
 * @package    Smart_Admin_Search
 * @subpackage Smart_Admin_Search/includes
 */

/**
 * The custom search functions.
 *
 * @package    Smart_Admin_Search
 * @subpackage Smart_Admin_Search/includes
 * @author     Andrea Porotti
 */
class Smart_Admin_Search_Functions {

	public function register_demo_search_function() {
		global $smart_admin_search_registered_functions;

		// Register function.
		$smart_admin_search_registered_functions[] = array(
			'name'         => 'demo_search_function',
			'display_name' => 'Demo Search Function',
			'description'  => 'A function used to test plugin functionality.',
		);
	}

	public function demo_search_function( $query ) {
		global $smart_admin_search_results;

		// Add function results.
		$smart_admin_search_results[] = array(
			'text'        => 'Demo result',
			'description' => 'demo result from demo function...',
			'link_url'    => '',
		);
	}

}
