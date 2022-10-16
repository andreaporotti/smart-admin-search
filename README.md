# Smart Admin Search - WordPress Plugin

**NOTE:** this is a development repository. Please download latest stable release from the WordPress.org directory: https://wordpress.org/plugins/smart-admin-search/.

## Description

With Smart Admin Search you can quickly find contents in the WordPress dashboard without going up and down on the admin menu.

Just click on the top-right link or press a keyboard shortcut to open the search box, then type what you are looking for. Easy!

Currently Smart Admin Search can find:

- admin menu items
- posts
- pages
- users
- media
- custom post types contents

## Configuration

Settings for the plugin are available on the *Settings* -> *Smart Admin Search* page.

Some things you can do in the settings page:

- configure the keyboard shortcut to open the search box.
- disable search functions you don't need.
- change layout of the search link on the admin bar

## Permissions

The search box can be used by users of any role. Each user will get results available for the assigned role.

For pages and posts results, these actions will be triggered on result selection, according to the user permissions:

- edit item
- view item
- show a message if no access to the item is allowed

## Support

If you find any errors or compatibility issues with other plugins, please create a new topic in the [plugin support forum](https://wordpress.org/support/plugin/smart-admin-search/). Thanks!

## Privacy

This plugin does not collect any user data.
It does not set any cookies and it does not connect to any third-party services.
All the plugin data is stored only on the WordPress database.

## Installation

### Installing (from the dashboard)

1. Go to the *Plugins* -> *Add New* page.
2. Type the plugin name in the *Search plugins* field.
3. Click the *Install now* button on the correct search result.
4. Click the *Activate* button.

### Installing (manually)

1. Download the plugin zip file.
2. Go to *Plugins* -> *Add New* in the WordPress dashboard.
3. Click on the *Upload Plugin* button.
4. Browse for the plugin zip file and click on *Install Now*.
5. Activate the plugin.

### Uninstalling

1. Go to the *Plugins* page in the WordPress dashboard.
2. Look for the plugin in the list.
3. Click on *Deactivate*.
4. Click on *Delete*.

Please note: by default the plugin data is kept after uninstall. You can choose to delete all data going to *Settings* -> *Smart Admin Search* and enabling data removal on uninstall.

## Changelog

**1.4.0 [2022-10-16]**

- Added search for custom post types contents. I cannot assure this will work for all existing plugins, so please let me know if you have any problems in the support forum.
- In the plugin settings, clicking the keyboard shortcut "Clear" button will clear the option value AND set the focus on the textbox.
- Tested on WordPress 6.1.

**1.3.0 [2022-01-01]**

- Added search for Media with image preview.
- Added an option to display the url of each result, which was always visible. Starting with this version, the url is hidden by default.
- For pages and posts results, these actions will be triggered on result selection, according to the user permissions: "edit item" or "view item" or "show a message if no access to the item is allowed".
- Small changes to the results style.

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

## License
[GPLv2 or later](http://www.gnu.org/licenses/gpl-2.0.html)