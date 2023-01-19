<?php

/**
 * Plugin Name: Typeform
 * Plugin URI:  https://www.typeform.com/
 * Description: Create beautiful online forms, surveys, quizzes, and much more.
 * Version:     2.2.6
 * Author:      Typeform
 * Author URI:  https://www.typeform.com/?utm_source=wordpressorg&utm_medium=referral&utm_campaign=wordpressorg_integration&utm_content=directory
 * License:     Apache-2.0
 * License URI: https://directory.fsf.org/wiki/License:Apache-2.0
 *
 * @package typeform
 */

defined('ABSPATH') or die('No script kiddies please!');

function typeform_shortcode_handler($attributes)
{
    $url = FALSE;
    $type = isset($attributes['type']) ? $attributes['type'] : null;
    $text = isset($attributes['button_text']) ? $attributes['button_text'] : null;
    $form_id = isset($attributes['width']) ? $attributes['form_id'] : null;

    if (isset($attributes['url'])) {
        $url = $attributes['url'];
    } elseif (isset($attributes['builder'])) {
        $url = 'https://form.typeform.com/to/Nlh6G0?' . $attributes['builder'];
    }

    if ($url != FALSE) {
        switch ($type) {

            case 'popup':
                return '<button data-tf-popup="' . $form_id . 'data-tf-medium="embed-wordpress' . $data_width . $data_height . '">' . $text . ' </button>';
            case 'embed':
            default:
                return '<div data-tf-widget="' . $form_id . 'data-tf-medium="embed-wordpress"></div>';
        }
    }
}


function typeform_plugin_scripts()
{
    $asset_file = include( plugin_dir_path( __FILE__ ) . 'dist/index.asset.php');

    wp_enqueue_script(
        'typeform-embed',
        plugins_url('dist/index.js', __FILE__),
        $asset_file['dependencies'],
        $asset_file['version']
    );
}

// For gutenberg
if (function_exists('register_block_type')) {
    add_shortcode('typeform_embed', 'typeform_shortcode_handler');
    add_action('enqueue_block_editor_assets', 'typeform_plugin_scripts');
}
