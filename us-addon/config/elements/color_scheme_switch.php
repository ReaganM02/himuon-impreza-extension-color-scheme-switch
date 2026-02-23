<?php defined('ABSPATH') OR die('This script cannot be accessed directly.');

/**
 * Override config for shortcode: color_scheme_switch
 *
 * Adds SVG upload controls for the labels around the switch.
 * Rendering support is implemented separately in the template override.
 */

if (!isset($config) || !is_array($config)) {
    return array();
}

if (empty($config['params']) || !is_array($config['params'])) {
    return $config;
}

$before_icon_param = array(
    'text_before_svg' => array(
        'title' => __('Text Before Switch Icon (SVG)', 'us'),
        'type' => 'upload',
        'extension' => 'svg',
        'std' => '',
    ),
);

$after_icon_param = array(
    'text_after_svg' => array(
        'title' => __('Text After Switch Icon (SVG)', 'us'),
        'type' => 'upload',
        'extension' => 'svg',
        'std' => '',
    ),
    'icon_visibility_by_switch_state' => array(
        'title' => __('Show Only Active Side Icon', 'us'),
        'description' => __('When enabled: switch OFF hides the "after" icon, switch ON hides the "before" icon.', 'us'),
        'type' => 'switch',
        'switch_text' => __('Enable', 'us'),
        'std' => 0,
    ),
    'hide_switch_box' => array(
        'title' => __('Hide Switch Box', 'us'),
        'description' => __('When enabled, only labels/icons are visible (the toggle box is hidden).', 'us'),
        'type' => 'switch',
        'switch_text' => __('Enable', 'us'),
        'std' => 0,
    ),
);

if (function_exists('us_array_merge_insert')) {
    $config['params'] = us_array_merge_insert($config['params'], $before_icon_param, 'after', 'text_before');
    $config['params'] = us_array_merge_insert($config['params'], $after_icon_param, 'after', 'text_after');
} else {
    $config['params'] = array_merge($config['params'], $before_icon_param, $after_icon_param);
}

return $config;
