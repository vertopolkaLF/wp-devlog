# WP DevLog

Small devlog plugin for WordPress designed for communication between developers and editors.

## Features

- Custom post type "DevLog" for logging development changes
- Dashboard widget showing latest development logs
- Settings page to configure the plugin behavior
- Fully compatible with WordPress localization system

## Localization

The plugin is fully translatable and comes with the following translations:
- English (default)
- Russian

## Installation

1. Upload the plugin files to the `/wp-content/plugins/wp-devlog` directory, or install the plugin through the WordPress plugins screen
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->DevLog screen to configure the plugin

## Translation

If you want to add a new translation:
1. Copy the `languages/wp-devlog.pot` file
2. Rename it to `wp-devlog-{locale}.po` (e.g., `wp-devlog-fr_FR.po` for French)
3. Translate the strings in the file
4. Create an MO file using a tool like Poedit or the WordPress Loco Translate plugin

## License

This plugin is licensed under the GPL-2.0+
