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

	public function register_demo_search_function( $registered_functions ) {
		// Register function.
		$registered_functions[] = array(
			'name'         => 'demo_search_function',
			'display_name' => 'Demo Search Function',
			'description'  => 'A function used to test plugin functionality.',
		);
		
		return $registered_functions;
	}

	public function demo_search_function( $search_results, $query ) {
		// Add function results.
		$search_results[] = array(
			'text'        => 'Demo result',
			'description' => 'demo result from demo function...',
			'link_url'    => '',
		);

		return $search_results;
	}

}
