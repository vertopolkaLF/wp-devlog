=== WP DevLog ===
Contributors: vertopolkalf
Tags: devlog, development log, dashboard widget, communication
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.7.2
Requires PHP: 7.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin for communication between developers and editors to streamline website maintenance.

== Description ==

WP DevLog helps improve communication between developers and content editors by providing a simple system to log and display development changes.

### Key Features

* Custom post type "DevLog" for logging development changes
* Dashboard widget showing latest development logs
* Settings page to configure the plugin behavior
* Fully compatible with WordPress localization system (English and Russian included)
* Thickbox modal integration for detailed change logs

### How It Works

Developers can create DevLog entries with information about site changes, updates, or important notes. These entries appear in a widget on the WordPress dashboard, ensuring editors and site managers stay informed about technical changes.

The plugin is especially useful for:
* Documenting website changes
* Notifying editors about new features
* Explaining technical updates
* Providing usage instructions

### Translations Included

* English (default)
* Russian

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-devlog` directory, or install the plugin through the WordPress plugins screen
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->DevLog screen to configure the plugin

== Frequently Asked Questions ==

= How do I create a new DevLog entry? =

Navigate to DevLog in your WordPress admin menu and click "Add New". Enter a title and content for your log entry, then publish.

= Can I customize how many entries appear in the dashboard widget? =

Yes. Go to DevLog -> Settings and adjust the "Number of entries per page" setting.

= How do I add a new translation? =

1. Copy the `languages/wp-devlog.pot` file
2. Rename it to `wp-devlog-{locale}.po` (e.g., `wp-devlog-fr_FR.po` for French)
3. Translate the strings in the file
4. Create an MO file using a tool like Poedit or the WordPress Loco Translate plugin

== Screenshots ==

1. Dashboard widget showing DevLog entries
2. DevLog entry creation screen
3. Settings page

== Changelog ==

= 1.7.2 =
* Bug fixes and minor improvements

= 1.7.0 =
* Added full localization support
* Added English as default language
* Added Russian translation

= 1.6.0 =
* Initial release on WordPress.org

== Upgrade Notice ==

= 1.7.0 =
This version adds full localization support with English and Russian languages. 