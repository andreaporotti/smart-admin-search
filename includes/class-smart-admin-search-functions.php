<?php
/**
 * The available search functions.
 *
 * @since      1.0.0
 * @package    Smart_Admin_Search
 * @subpackage Smart_Admin_Search/includes
 */

/**
 * The available search functions.
 *
 * @package    Smart_Admin_Search
 * @subpackage Smart_Admin_Search/includes
 * @author     Andrea Porotti
 */
class Smart_Admin_Search_Functions {

	// ----------------------------
	// Search the admin menu items.
	// ----------------------------

	/**
	 * Saves the admin menu to the database.
	 *
	 * @since    1.0.0
	 */
	public function get_admin_menu() {

		global $menu, $submenu, $current_user;

		// Get menus from transients.
		$transient_menu    = get_transient( 'sas_admin_menu_user_' . $current_user->ID );
		$transient_submenu = get_transient( 'sas_admin_submenu_user_' . $current_user->ID );

		// Set the transient if it is false or different from the corresponding global menu.

		// First level menu.
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
					// Remove any HTML code from item name. If the name is empty, remove the item.
					$name = trim( sanitize_text_field( ( strpos( $item[0], '<' ) > 0 ) ? strstr( $item[0], '<', true ) : $item[0] ) );

					if ( ! empty( $name ) ) {
						$global_menu[ $key ][0] = $name;
					} else {
						unset( $global_menu[ $key ] );
					}
				} else {
					// Remove the menu item.
					unset( $global_menu[ $key ] );
				}

			}
		);

		if ( false === $transient_menu || $global_menu !== $transient_menu ) {
			set_transient( 'sas_admin_menu_user_' . $current_user->ID, $global_menu );
		}

		// Submenu.
		$global_submenu = $submenu;

		// Parse all menu items.
		array_walk(
			$global_submenu,
			function( $item, $key ) use ( &$global_submenu ) {

				if ( ! empty( $key ) ) {
					foreach ( $item as $item_key => $menu_item ) {
						// Remove any HTML code from item name.
						$global_submenu[ $key ][ $item_key ][0] = trim( sanitize_text_field( ( strpos( $menu_item[0], '<' ) > 0 ) ? strstr( $menu_item[0], '<', true ) : $menu_item[0] ) );

						// Remove any 'return' parameter from file name.
						$global_submenu[ $key ][ $item_key ][2] = remove_query_arg( 'return', wp_kses_decode_entities( $menu_item[2] ) );
					}
				} else {
					// Remove the item if it has no parent (empty $key).
					unset( $global_submenu[ $key ] );
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
			'description'  => esc_html__( 'Search items in the admin menu.', 'smart-admin-search' ),
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

		// Get menus from transients.
		$admin_menu    = get_transient( 'sas_admin_menu_user_' . $current_user->ID );
		$admin_submenu = get_transient( 'sas_admin_submenu_user_' . $current_user->ID );

		// Search the first level menu items.
		if ( ! empty( $admin_menu ) ) {

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

					// Generate item url.
					if ( strpos( $menu_item[2], '.php' ) !== false ) {
						// The item contains a file name.
						$url = wp_specialchars_decode( admin_url( $menu_item[2] ) );
					} else {
						// Use admin.php if no file name has been found.
						$url = wp_specialchars_decode( add_query_arg( 'page', $menu_item[2], admin_url( '/admin.php' ) ) );
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
		}

		// Search the submenu items.
		if ( ! empty( $admin_menu ) && ! empty( $admin_submenu ) ) {

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
							if ( strpos( $menu_item[2], '.php' ) !== false ) {
								// The item contains a file name.
								$url = wp_specialchars_decode( admin_url( $menu_item[2] ) );
							} elseif ( strpos( $key, '.php' ) !== false ) {
								// The item parent contains a file name.
								$url = wp_specialchars_decode( add_query_arg( 'page', $menu_item[2], admin_url( $key ) ) );
							} else {
								// Use admin.php if no file name has been found.
								$url = wp_specialchars_decode( add_query_arg( 'page', $menu_item[2], admin_url( '/admin.php' ) ) );
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

		$item_data = array(
			'name' => '',
			'icon' => '',
		);

		if ( ! empty( $key ) ) {

			foreach ( $admin_menu as $menu_item ) {

				if ( $menu_item[2] === $key ) {

					$item_data['name'] = $menu_item[0];
					$item_data['icon'] = $menu_item[6];

					return $item_data;

				}
			}
		}

		return $item_data;

	}

	/**
	 * Registers the function that looks for posts containing the search query.
	 *
	 * @since    1.1.0
	 * @param    array $registered_functions    The list of registered search functions.
	 */
	public function register_search_posts( $registered_functions ) {

		// Register the function.
		$registered_functions[] = array(
			'name'         => 'search_posts',
			'display_name' => esc_html__( 'Posts', 'smart-admin-search' ),
			'description'  => esc_html__( 'Search posts.', 'smart-admin-search' ),
		);

		return $registered_functions;

	}

	/**
	 * Looks for posts containing the search query.
	 *
	 * @since    1.1.0
	 * @param    array  $search_results    The global search results.
	 * @param    string $query             The search query.
	 */
	public function search_posts( $search_results, $query ) {

		$args = array(
			'post_type'      => 'post',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			's'              => $query,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$posts_query = new WP_Query( $args );
		$posts       = $posts_query->posts;
		wp_reset_postdata();

		foreach ( $posts as $post ) {
			$text = ( ! empty( $post->post_title ) ) ? $post->post_title : esc_html__( '(no title)' );

			if ( 'publish' !== $post->post_status ) {
				$post_status = get_post_status_object( $post->post_status )->label;
				$text       .= ' (' . $post_status . ')';
			}

			$link_url   = get_edit_post_link( $post->ID, '' );
			$icon_class = 'dashicons-admin-post';
			$style      = '';

			// Add the item to search results.
			$search_results[] = array(
				'text'        => $text,
				'description' => esc_html__( 'Post.', 'smart-admin-search' ),
				'link_url'    => $link_url,
				'icon_class'  => $icon_class,
				'style'       => $style,
			);
		}

		return $search_results;

	}
}
