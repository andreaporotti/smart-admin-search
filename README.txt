=== Smart Admin Search ===
Contributors: andreaporotti
Tags: search, admin, dashboard
Requires at least: 5.0
Tested up to: 5.9
Requires PHP: 5.6
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds a search engine to the WordPress dashboard.

== Description ==

With Smart Admin Search you can quickly find contents in the WordPress dashboard without going up and down on the admin menu.

Just click on the top-right link or press a keyboard shortcut to open the search box, then type what you are looking for. Easy!

Currently Smart Admin Search can find:

- admin menu items (get a list of menu items matching your query, then press Enter on the most relevant result to go to that page.)
- posts
- pages
- users
- (more contents coming soon...)

**Configuration**

Settings for the plugin are available on the *Settings* -> *Smart Admin Search* page.

Some things you can do in the settings page:

- configure the keyboard shortcut to open the search box.
- disable search functions you don't need.
- change layout of the search link on the admin bar

**Permissions**

The search box can be used by users of any role. Each user will get results available for the assigned role.

**Support**

If you find any errors or compatibility issues with other plugins, please create a new topic in the support forum. Thanks!

**Privacy**

This plugin does not collect any user data.
It does not set any cookies and it does not connect to any third-party services.
All the plugin data is stored only on the WordPress database.

== Installation ==

**Installing (from the dashboard)**

1. Go to the *Plugins* -> *Add New* page.
2. Type the plugin name in the *Search plugins* field.
3. Click the *Install now* button on the correct search result.
4. Click the *Activate* button.

**Installing (manually)**

1. Download the plugin zip file.
2. Go to *Plugins* -> *Add New* in the WordPress dashboard.
3. Click on the *Upload Plugin* button.
4. Browse for the plugin zip file and click on *Install Now*.
5. Activate the plugin.

**Uninstalling**

1. Go to the *Plugins* page in the WordPress dashboard.
2. Look for the plugin in the list.
3. Click on *Deactivate*.
4. Click on *Delete*.

Please note: by default the plugin data is kept after uninstall. You can choose to delete all data going to *Settings* -> *Smart Admin Search* and enabling data removal on uninstall.

== Screenshots ==

1. The link on the admin bar to open the search box.
2. The empty search box.
3. The search box showing some results.
4. The keyboard shortcut to open the search box can be configured in the settings page.

== Changelog ==

**1.2.0 [2021-12-17]**

- Added search for Users.
- Added an option to choose layout of the search link on the admin bar.
- Changed default layout of the search link on the admin bar (moved icon to the right like the user menu).
- Fixed a bug preventing auto focus on the search field.
- Tested on WordPress 5.9.

**1.1.1 [2021-07-18]**

- Tested with WordPress 5.8.
- Fixed a bug with jQuery 3.6.0 and Select2 preventing auto focus on the search field.
- Changed style of the search field close icon.
- Updated IT translations.

**1.1.0 [2021-04-05]**

- Added search for Posts.
- Added search for Pages.
- Tested with PHP 8.0.x.

**1.0.0 [2020-12-31]**

- First release.