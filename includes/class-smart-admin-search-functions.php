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
			'icon_class'  => '',
			'style'       => '',
		);

		return $search_results;
	}

	// ----------------------
	// SEARCH INTO ADMIN MENU
	// ----------------------

	/**
	 * Saves the admin menu to the database.
	 *
	 * @since    1.0.0
	 */
	public function get_admin_menu() {

		global $menu, $submenu, $current_user;

		// Get menus from transient.
		$transient_menu    = get_transient( 'sas_admin_menu_user_' . $current_user->ID );
		$transient_submenu = get_transient( 'sas_admin_submenu_user_' . $current_user->ID );

		// Set the transient if it is false or different from the corresponding global menu.

		// -- menu.
		$global_menu = $menu;

		// Menu items to be skipped.
		$menu_items_to_skip = array(
			'wp-menu-separator',
			'menu-top menu-icon-links',
		);

		// Parse all menu items.
		array_walk(
			$global_menu,
			function( $item, $key ) use ( &$global_menu, $menu_items_to_skip ) {

				if ( ! in_array( $item[4], $menu_items_to_skip, true ) ) {
					// Remove any HTML code from item name.
					$global_menu[ $key ][0] = trim( sanitize_text_field( ( strpos( $item[0], '<' ) > 0 ) ? strstr( $item[0], '<', true ) : $item[0] ) );
				} else {
					// Remove the menu item.
					unset( $global_menu[ $key ] );
				}

			}
		);

		if ( false === $transient_menu || $global_menu !== $transient_menu ) {
			set_transient( 'sas_admin_menu_user_' . $current_user->ID, $global_menu );
		}

		// -- submenu.
		$global_submenu = $submenu;

		// Parse all menu items.
		array_walk(
			$global_submenu,
			function( $item, $key ) use ( &$global_submenu ) {

				foreach ( $item as $item_key => $menu_item ) {
					// Remove any HTML code from item name.
					$global_submenu[ $key ][ $item_key ][0] = trim( sanitize_text_field( ( strpos( $menu_item[0], '<' ) > 0 ) ? strstr( $menu_item[0], '<', true ) : $menu_item[0] ) );

					// Remove any 'return' parameter from file name.
					$global_submenu[ $key ][ $item_key ][2] = remove_query_arg( 'return', wp_kses_decode_entities( $menu_item[2] ) );
				}

			}
		);

		if ( false === $transient_submenu || $global_submenu !== $transient_submenu ) {
			set_transient( 'sas_admin_submenu_user_' . $current_user->ID, $global_submenu );
		}

	}

	/**
	 * Registers the function that looks for an admin menu item containing the search query.
	 *
	 * @since    1.0.0
	 * @param    array $registered_functions    The list of registered search functions.
	 */
	public function register_search_admin_menu( $registered_functions ) {

		// Register the function.
		$registered_functions[] = array(
			'name'         => 'search_admin_menu',
			'display_name' => esc_html__( 'Admin Menu', 'smart-admin-search' ),
			'description'  => esc_html__( 'Searches for links in the admin menu.', 'smart-admin-search' ),
		);

		return $registered_functions;

	}

	/**
	 * Looks for an admin menu item containing the search query.
	 *
	 * @since    1.0.0
	 * @param    array  $search_results    The global search results.
	 * @param    string $query             The search query.
	 */
	public function search_admin_menu( $search_results, $query ) {

		global $current_user;

		// Get menus from transient.
		$admin_menu    = get_transient( 'sas_admin_menu_user_' . $current_user->ID );
		$admin_submenu = get_transient( 'sas_admin_submenu_user_' . $current_user->ID );

		if ( ! empty( $admin_menu ) && ! empty( $admin_submenu ) ) {

			// Search in the first level menu items.
			foreach ( $admin_menu as $menu_item ) {

				// Get item name.
				$name = $menu_item[0];

				// Get item icon.
				$icon       = $menu_item[6];
				$icon_class = '';
				$style      = '';
				if ( substr( $icon, 0, strlen( 'dashicons' ) ) === 'dashicons' ) { // -- if it's a dashicons class
					$icon_class = $icon;
				} elseif ( substr( $icon, 0, strlen( 'data:image' ) ) === 'data:image' ) { // -- if it's an image
					$style = 'background-image: url(\'' . $icon . '\');';
				}

				// Check if the item name contains the query.
				if ( ! empty( $name ) && strpos( strtolower( $name ), strtolower( $query ) ) !== false ) {

					// Generate item url:
					// if the item has not a file name, use /admin.php?page=[name].
					if ( strpos( $menu_item[2], '.php' ) === false ) {
						$url = wp_specialchars_decode( admin_url( '/admin.php?page=' . $menu_item[2] ) );
					} else {
						$url = wp_specialchars_decode( admin_url( $menu_item[2] ) );
					}

					// Add the item to search results.
					$search_results[] = array(
						'text'        => $name,
						'description' => esc_html__( 'Admin menu item.', 'smart-admin-search' ),
						'link_url'    => $url,
						'icon_class'  => $icon_class,
						'style'       => $style,
					);

				}
			}

			// Search in the sub menu items.
			array_walk(
				$admin_submenu,
				function( $item, $key ) use ( &$search_results, $query, $admin_menu ) {

					foreach ( $item as $item_key => $menu_item ) {

						// Get parent item.
						$parent_item = $this->get_admin_menu_item_by_key( $admin_menu, $key );

						// Get item name.
						$name = $menu_item[0];

						// Set full item name.
						$full_name = $parent_item['name'] . ' / ' . $name;

						// Get item icon.
						$icon       = $parent_item['icon'];
						$icon_class = '';
						$style      = '';
						if ( substr( $icon, 0, strlen( 'dashicons' ) ) === 'dashicons' ) { // -- if it's a dashicons class
							$icon_class = $icon;
						} elseif ( substr( $icon, 0, strlen( 'data:image' ) ) === 'data:image' ) { // -- if it's an image
							$style = 'background-image: url(\'' . $icon . '\');';
						}

						// Check if the item full name contains the query.
						if ( ! empty( $full_name ) && strpos( strtolower( $full_name ), strtolower( $query ) ) !== false ) {

							// Generate item url.
							// If the item has not a file name, use /admin.php?page=[slug].
							if ( strpos( $menu_item[2], '.php' ) === false ) {
								$url = wp_specialchars_decode( admin_url( '/admin.php?page=' . $menu_item[2] ) );
							} else {
								$url = wp_specialchars_decode( admin_url( $menu_item[2] ) );
							}

							// Add the item to search results.
							$search_results[] = array(
								'text'        => $full_name,
								'description' => esc_html__( 'Admin menu item.', 'smart-admin-search' ),
								'link_url'    => $url,
								'icon_class'  => $icon_class,
								'style'       => $style,
							);

						}
					}

				}
			);

		}

		return $search_results;

	}

	/**
	 * Gets data of a first level admin menu item by the item key.
	 *
	 * @since    1.0.0
	 * @param    array  $admin_menu    The first level admin menu.
	 * @param    string $key           Menu item key.
	 */
	private function get_admin_menu_item_by_key( $admin_menu, $key ) {

		foreach ( $admin_menu as $menu_item ) {

			if ( $menu_item[2] === $key ) {

				return array(
					'name' => $menu_item[0],
					'icon' => $menu_item[6],
				);

			}
		}

		return '--';

	}

}
