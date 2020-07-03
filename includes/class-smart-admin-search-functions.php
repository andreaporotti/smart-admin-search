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

	public function demo_search_function( $query ) {
		global $smart_admin_search_results;

		$smart_admin_search_results[] = array(
			'text'        => 'Demo result',
			'description' => 'demo result from demo function...',
			'link_url'    => '',
		);
	}

}
