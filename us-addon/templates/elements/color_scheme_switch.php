<?php defined('ABSPATH') OR die('This script cannot be accessed directly.');

/**
 * Output Color Scheme Switch element
 *
 * Added in addon:
 * - text_before_svg (attachment ID, SVG)
 * - text_after_svg (attachment ID, SVG)
 */

$_atts = array(
    'class' => 'w-color-switch',
    'style' => '',
);

$_atts['class'] .= $classes ?? '';

if (!empty($el_id)) {
    $_atts['id'] = $el_id;
}

$before_svg_id = (int) us_replace_dynamic_value($text_before_svg ?? '', FALSE);
$after_svg_id = (int) us_replace_dynamic_value($text_after_svg ?? '', FALSE);
$has_before_svg = $before_svg_id > 0;
$has_after_svg = $after_svg_id > 0;

if (!empty($icon_visibility_by_switch_state) && ($has_before_svg || $has_after_svg)) {
    $_atts['class'] .= ' has_icon_state_visibility';
    if ($has_before_svg) {
        $_atts['class'] .= ' has_before_svg';
    }
    if ($has_after_svg) {
        $_atts['class'] .= ' has_after_svg';
    }
}

if (!empty($hide_switch_box)) {
    $_atts['class'] .= ' no_switch_box';
}

if ($inactive_switch_bg) {
    $_atts['style'] .= '--color-inactive-switch-bg:' . us_get_color($inactive_switch_bg, TRUE) . ';';
}
if ($active_switch_bg) {
    $_atts['style'] .= '--color-active-switch-bg:' . us_get_color($active_switch_bg, TRUE) . ';';
}

$scheme_output = '';
global $us_color_scheme_switch_is_used;
global $himuon_color_switch_icon_state_css_output;
global $himuon_color_switch_base_css_output;

// Output JS and CSS only once if multiple switches are shown
if (!$us_color_scheme_switch_is_used && $color_scheme) {
    $us_color_scheme_switch_is_used = TRUE;

    $color_schemes = us_get_color_schemes();

    $scheme_output .= '<style id="us-color-scheme-switch-css">';
    $scheme_output .= 'html.us-color-scheme-on {';
    if (isset($color_schemes[$color_scheme]['values'])) {
        foreach ($color_schemes[$color_scheme]['values'] as $color_schemes_option => $color_value) {
            if (!empty($color_value)) {
                $scheme_output .= '--' . str_replace('_', '-', $color_schemes_option) . ': ' . us_get_color($color_value, FALSE, FALSE) . ';';

                // Add separate values from color pickers that support gradients.
                foreach (us_config('theme-options.colors.fields') as $color_option => $color_option_params) {
                    if (!empty($color_option_params['with_gradient']) && $color_option === $color_schemes_option) {
                        $scheme_output .= '--' . str_replace('_', '-', $color_schemes_option) . '-grad: ' . us_get_color($color_value, TRUE, FALSE) . ';';
                    }
                }

                if ($color_schemes_option === 'color_content_primary') {
                    $scheme_output .= '--color-content-primary-faded:' . us_hex2rgba(us_get_color($color_value, FALSE, FALSE), 0.15) . ';';
                }
            }
        }
    }
    $scheme_output .= '}';
    $scheme_output .= '</style>';
}

if (empty($himuon_color_switch_icon_state_css_output)) {
    $himuon_color_switch_icon_state_css_output = TRUE;
    $scheme_output .= '<style id="himuon-color-switch-icon-state-css">';
    $scheme_output .= '.w-color-switch.has_icon_state_visibility.has_after_svg .w-color-switch-after-icon{display:none;}';
    $scheme_output .= 'html.us-color-scheme-on .w-color-switch.has_icon_state_visibility.has_after_svg .w-color-switch-after-icon{display:inline-flex;}';
    $scheme_output .= 'html.us-color-scheme-on .w-color-switch.has_icon_state_visibility.has_before_svg .w-color-switch-before-icon{display:none;}';
    $scheme_output .= '</style>';
}

if (empty($himuon_color_switch_base_css_output)) {
    $himuon_color_switch_base_css_output = TRUE;
    $scheme_output .= '<style id="himuon-color-switch-base-css">';
    $scheme_output .= '.w-color-switch-before,.w-color-switch-after{margin:0!important;}';
    $scheme_output .= '.w-color-switch-box{margin-inline:.5rem;}';
    $scheme_output .= '</style>';
}

/**
 * Sanitize raw SVG markup to a safe subset for inline output.
 */
$sanitize_inline_svg = static function ($svg_raw) {
    if (!is_string($svg_raw) || trim($svg_raw) === '') {
        return '';
    }

    // Remove XML declarations / doctypes before kses.
    $svg_raw = preg_replace('/<\?xml[^>]*\?>/i', '', $svg_raw);
    $svg_raw = preg_replace('/<!DOCTYPE[^>]*>/i', '', $svg_raw);
    $svg_raw = trim($svg_raw);

    if (stripos($svg_raw, '<svg') === FALSE) {
        return '';
    }

    $allowed_svg = array(
        'svg' => array(
            'class' => TRUE,
            'style' => TRUE,
            'width' => TRUE,
            'height' => TRUE,
            'viewbox' => TRUE,
            'xmlns' => TRUE,
            'xmlns:xlink' => TRUE,
            'role' => TRUE,
            'aria-hidden' => TRUE,
            'focusable' => TRUE,
            'fill' => TRUE,
            'stroke' => TRUE,
            'stroke-width' => TRUE,
            'stroke-linecap' => TRUE,
            'stroke-linejoin' => TRUE,
            'stroke-miterlimit' => TRUE,
            'stroke-dasharray' => TRUE,
            'stroke-dashoffset' => TRUE,
            'fill-rule' => TRUE,
            'clip-rule' => TRUE,
            'opacity' => TRUE,
            'transform' => TRUE,
            'preserveaspectratio' => TRUE,
            'id' => TRUE,
        ),
        'g' => array(
            'class' => TRUE,
            'style' => TRUE,
            'fill' => TRUE,
            'stroke' => TRUE,
            'stroke-width' => TRUE,
            'stroke-linecap' => TRUE,
            'stroke-linejoin' => TRUE,
            'fill-rule' => TRUE,
            'clip-rule' => TRUE,
            'opacity' => TRUE,
            'transform' => TRUE,
            'id' => TRUE,
            'clip-path' => TRUE,
            'mask' => TRUE,
        ),
        'path' => array(
            'd' => TRUE,
            'class' => TRUE,
            'style' => TRUE,
            'fill' => TRUE,
            'stroke' => TRUE,
            'stroke-width' => TRUE,
            'stroke-linecap' => TRUE,
            'stroke-linejoin' => TRUE,
            'stroke-miterlimit' => TRUE,
            'stroke-dasharray' => TRUE,
            'stroke-dashoffset' => TRUE,
            'fill-rule' => TRUE,
            'clip-rule' => TRUE,
            'opacity' => TRUE,
            'transform' => TRUE,
            'id' => TRUE,
        ),
        'circle' => array(
            'cx' => TRUE,
            'cy' => TRUE,
            'r' => TRUE,
            'class' => TRUE,
            'style' => TRUE,
            'fill' => TRUE,
            'stroke' => TRUE,
            'stroke-width' => TRUE,
            'opacity' => TRUE,
            'transform' => TRUE,
            'id' => TRUE,
        ),
        'ellipse' => array(
            'cx' => TRUE,
            'cy' => TRUE,
            'rx' => TRUE,
            'ry' => TRUE,
            'class' => TRUE,
            'style' => TRUE,
            'fill' => TRUE,
            'stroke' => TRUE,
            'stroke-width' => TRUE,
            'opacity' => TRUE,
            'transform' => TRUE,
            'id' => TRUE,
        ),
        'rect' => array(
            'x' => TRUE,
            'y' => TRUE,
            'width' => TRUE,
            'height' => TRUE,
            'rx' => TRUE,
            'ry' => TRUE,
            'class' => TRUE,
            'style' => TRUE,
            'fill' => TRUE,
            'stroke' => TRUE,
            'stroke-width' => TRUE,
            'opacity' => TRUE,
            'transform' => TRUE,
            'id' => TRUE,
        ),
        'line' => array(
            'x1' => TRUE,
            'y1' => TRUE,
            'x2' => TRUE,
            'y2' => TRUE,
            'class' => TRUE,
            'style' => TRUE,
            'fill' => TRUE,
            'stroke' => TRUE,
            'stroke-width' => TRUE,
            'opacity' => TRUE,
            'transform' => TRUE,
            'id' => TRUE,
        ),
        'polyline' => array(
            'points' => TRUE,
            'class' => TRUE,
            'style' => TRUE,
            'fill' => TRUE,
            'stroke' => TRUE,
            'stroke-width' => TRUE,
            'opacity' => TRUE,
            'transform' => TRUE,
            'id' => TRUE,
        ),
        'polygon' => array(
            'points' => TRUE,
            'class' => TRUE,
            'style' => TRUE,
            'fill' => TRUE,
            'stroke' => TRUE,
            'stroke-width' => TRUE,
            'opacity' => TRUE,
            'transform' => TRUE,
            'id' => TRUE,
        ),
        'defs' => array(),
        'clippath' => array(
            'id' => TRUE,
            'clippathunits' => TRUE,
            'transform' => TRUE,
        ),
        'mask' => array(
            'id' => TRUE,
            'x' => TRUE,
            'y' => TRUE,
            'width' => TRUE,
            'height' => TRUE,
            'maskunits' => TRUE,
            'maskcontentunits' => TRUE,
            'transform' => TRUE,
        ),
        'lineargradient' => array(
            'id' => TRUE,
            'x1' => TRUE,
            'y1' => TRUE,
            'x2' => TRUE,
            'y2' => TRUE,
            'gradientunits' => TRUE,
            'gradienttransform' => TRUE,
            'href' => TRUE,
            'xlink:href' => TRUE,
            'spreadmethod' => TRUE,
        ),
        'radialgradient' => array(
            'id' => TRUE,
            'cx' => TRUE,
            'cy' => TRUE,
            'r' => TRUE,
            'fx' => TRUE,
            'fy' => TRUE,
            'fr' => TRUE,
            'gradientunits' => TRUE,
            'gradienttransform' => TRUE,
            'href' => TRUE,
            'xlink:href' => TRUE,
            'spreadmethod' => TRUE,
        ),
        'stop' => array(
            'offset' => TRUE,
            'stop-color' => TRUE,
            'stop-opacity' => TRUE,
        ),
        'symbol' => array(
            'id' => TRUE,
            'viewbox' => TRUE,
            'preserveaspectratio' => TRUE,
        ),
        'use' => array(
            'href' => TRUE,
            'xlink:href' => TRUE,
            'x' => TRUE,
            'y' => TRUE,
            'width' => TRUE,
            'height' => TRUE,
            'transform' => TRUE,
        ),
        'title' => array(),
        'desc' => array(),
    );

    $sanitized_svg = wp_kses($svg_raw, $allowed_svg);

    if ($sanitized_svg === '' || stripos($sanitized_svg, '<svg') === FALSE) {
        return '';
    }

    return $sanitized_svg;
};

/**
 * Read and sanitize an SVG attachment for safe inline rendering.
 */
$get_safe_inline_svg = static function ($svg_attachment_id) use ($sanitize_inline_svg) {
    $svg_attachment_id = (int) $svg_attachment_id;
    if ($svg_attachment_id <= 0) {
        return '';
    }

    $svg_filepath = get_attached_file($svg_attachment_id);
    if (empty($svg_filepath)) {
        return '';
    }

    $svg_filepath = realpath($svg_filepath);
    if (!$svg_filepath || !is_readable($svg_filepath)) {
        return '';
    }

    $upload_dir = wp_get_upload_dir();
    $upload_base_dir = !empty($upload_dir['basedir']) ? realpath($upload_dir['basedir']) : '';
    if (!$upload_base_dir) {
        return '';
    }

    $normalized_svg_filepath = wp_normalize_path($svg_filepath);
    $normalized_upload_dir = trailingslashit(wp_normalize_path($upload_base_dir));
    if (strpos($normalized_svg_filepath, $normalized_upload_dir) !== 0) {
        return '';
    }

    if (strtolower((string) pathinfo($svg_filepath, PATHINFO_EXTENSION)) !== 'svg') {
        return '';
    }

    $mime_type = (string) get_post_mime_type($svg_attachment_id);
    if ($mime_type !== '' && stripos($mime_type, 'svg') === FALSE) {
        return '';
    }

    $max_svg_bytes = 262144; // 256 KB hard cap for inline SVG payload.
    $svg_filesize = filesize($svg_filepath);
    if ($svg_filesize === FALSE || $svg_filesize > $max_svg_bytes) {
        return '';
    }

    $svg_content = file_get_contents($svg_filepath);
    if ($svg_content === FALSE || $svg_content === '') {
        return '';
    }

    return $sanitize_inline_svg($svg_content);
};

/**
 * Build label HTML for before/after switch side.
 * If SVG attachment ID is provided and points to an SVG file, output inline SVG.
 * Otherwise output original text content.
 */
$build_switch_label = static function ($class_name, $text_value, $svg_attachment_id = '') use ($get_safe_inline_svg) {
    $text_content = trim((string) $text_value);
    $svg_attachment_id = (int) us_replace_dynamic_value($svg_attachment_id, FALSE);
    $icon_content = '';

    if ($svg_attachment_id > 0) {
        $icon_content = $get_safe_inline_svg($svg_attachment_id);
    }

    if ($text_content !== '' || $icon_content !== '' || usb_is_preview()) {
        $output = '<span class="' . esc_attr($class_name) . '">';
        if ($icon_content !== '') {
            $output .= '<span class="' . esc_attr($class_name . '-icon') . '">' . $icon_content . '</span>';
        }
        if ($text_content !== '' || usb_is_preview()) {
            $output .= '<span class="' . esc_attr($class_name . '-text') . '">' . esc_html($text_content) . '</span>';
        }
        $output .= '</span>';
        return $output;
    }

    return '';
};

$text_before = $build_switch_label('w-color-switch-before', $text_before, $before_svg_id);
$text_after = $build_switch_label('w-color-switch-after', $text_after, $after_svg_id);

// Output the element
$output = '<div' . us_implode_atts($_atts) . '>';
$output .= $scheme_output;
$output .= '<label>';
$output .= '<input class="screen-reader-text" type="checkbox" name="us-color-scheme-switch"' . checked(!empty($_COOKIE['us_color_scheme_switch_is_on']), TRUE, FALSE) . '>';
$output .= $text_before;
if (empty($hide_switch_box)) {
    $output .= '<span class="w-color-switch-box"><i></i></span>';
}
$output .= $text_after;
$output .= '</label>';
$output .= '</div>';

echo $output;
