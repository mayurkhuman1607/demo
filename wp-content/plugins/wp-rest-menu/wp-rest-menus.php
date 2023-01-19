<?php 
/*
Plugin Name: WP REST Menu
Version: 1.0.4
Requires at least: 5.0
Tested up to: 5.9
Description: Adds menu endpoints to WP REST API, filters json fields optionaly and provides an additional nested parent-child endpoint.
Author: Kostas Charalampidis <skapator@gmail.com>
Author URI: https://noveldigital.pro
Plugin URI: https:/noveldigital.pro
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

//Get required dependencies.
require plugin_dir_path( __FILE__ ) . '/includes/class-webfiou-wp-rest-menus-controller.php';

// Init rest menus class.
function webfiou_wp_rest_menus_init() {
    \Webfiou\WP_REST_Menus_Controller::instance()
        ->register_routes();
}

// Hook in rest api init
add_action( 'rest_api_init', 'webfiou_wp_rest_menus_init' );
