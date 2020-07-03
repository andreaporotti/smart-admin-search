<?php
/**
 * Plugin Name:       Smart Admin Search
 * Plugin URI:        https://github.com/andreaporotti/smart-admin-search
 * Description:       ......
 * Version:           1.0.0
 * Requires at least: 4.9
 * Requires PHP:      5.6
 * Author:            Andrea Porotti
 * Author URI:        https://www.andreaporotti.it/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smart-admin-search
 * Domain Path:       /languages
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, see http://www.gnu.org/licenses/gpl-2.0.txt.
 *
 * @since             1.0.0
 * @package           Smart_Admin_Search
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Based on SemVer ( https://semver.org ).
 */
define( 'SMART_ADMIN_SEARCH_VERSION', '1.0.0' );

/*
 * Global search results added by the search functions.
 *
 * @since    1.0.0
 */
global $smart_admin_search_results;

/**
 * The code that runs during plugin activation.
 */
function activate_smart_admin_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smart-admin-search-activator.php';
	Smart_Admin_Search_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_smart_admin_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smart-admin-search-deactivator.php';
	Smart_Admin_Search_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_smart_admin_search' );
register_deactivation_hook( __FILE__, 'deactivate_smart_admin_search' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-smart-admin-search.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_smart_admin_search() {

	$plugin = new Smart_Admin_Search();
	$plugin->run();

}
run_smart_admin_search();
