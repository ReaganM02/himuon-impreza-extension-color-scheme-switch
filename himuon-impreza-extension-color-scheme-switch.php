<?php

/**
 * Plugin Name:       Himuon Impreza Color Scheme Switch SVG Extension
 * Description:       Adds SVG icon support to the Impreza Color Scheme Switch, with custom before/after labels, state-based icon visibility, and optional switch box hiding.
 * Version:           1.0.0
 * Author:            Reagan Mahinay
 * Author URI:        https://reagandev.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       himuon-impreza-extensions
 * Requires at least: 6.9
 * Requires PHP: 7.4
 * Domain Path:       /languages
 * Tested up to: 6.9
 * @package himuon-impreza-extensions
 */


add_action('plugins_loaded', static function () {
    // Let us_locate_file find addon templates and let this addon override us-core files.
    add_filter('us_files_search_paths', static function ($paths) {
        $addon_path = plugin_dir_path(__FILE__) . 'us-addon/';

        if (!in_array($addon_path, $paths, true)) {
            array_unshift($paths, $addon_path);
        }

        return $paths;
    });
});
