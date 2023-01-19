<?php
/**
 * astra child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package astra child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function enqueue_child_theme_scripts() {
    wp_enqueue_script( 'child-style', get_stylesheet_directory_uri() . '/assets/css/docs.css', array(), '1.0.0', true );
    wp_enqueue_script( 'child-script', get_stylesheet_directory_uri() . '/assets/js/main.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_scripts' );


function your_callback_function() {
    if ( ! isset( $_POST['nonce_field_name'] ) || ! wp_verify_nonce( $_POST['nonce_field_name'], 'your_nonce_action' ) ) {
        wp_die( 'Unauthorized access' );
    }
    $title = $_POST['title'];
    $description = $_POST['description'];
    // Prepare data
    $data = array(
        'title' => $title,
        'description' => $description,
    );
    $response = wp_remote_post( 'http://localhost/WPDEV/wp-json/wp/v2/', array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array( 'Content-Type' => 'application/json' ),
        'body' => json_encode($data),
    ) );
    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        echo "Something went wrong: $error_message";
    } else {
        echo json_encode($response['body']);
    }
    wp_die();
}

add_action( 'wp_ajax_your_form_action', 'your_callback_function' );
add_action( 'wp_ajax_nopriv_your_form_action', 'your_callback_function' );
