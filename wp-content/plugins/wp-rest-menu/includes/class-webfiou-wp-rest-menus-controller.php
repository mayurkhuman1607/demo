<?php

namespace Webfiou;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

use WP_REST_Controller;

/**
 * Class WP_REST_Menus_Controller
 *
 *
 * @package Skapator
 */
class WP_REST_Menus_Controller extends WP_REST_Controller
{

    /**
     * @var string $namespace
     */
    protected $namespace = 'menus/v1';

    /**
     * @var WP_REST_Menus_Controller
     */
    protected static $instance;

    public function __construct() {}

    /**
     * Get instance of class
     *
     * @return WP_REST_Menus_Controller
     */
    public static function instance() {
        if ( !isset( self::$instance ) && !( self::$instance instanceof WP_REST_Menus_Controller ) ) {
            self::$instance = new WP_REST_Menus_Controller;
        }
        return self::$instance;
    }

    /**
     * Register the routes. 
     * 
     */
    public function register_routes() {
        // Get all menus
        register_rest_route($this->namespace, '/menus', array(
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => array($this, 'get_menus'),
            'permission_callback' => function (\WP_REST_Request $request) {
                return apply_filters('wprm/get_menus/permissions', '__return_true', $request);
            },
        ));

        // Get all menu locations
        // DEPRECATED
        register_rest_route( $this->namespace, '/menus/locations', array(
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => array($this, 'get_menu_locations'),
            'permission_callback' => function (\WP_REST_Request $request) {
                return apply_filters('wprm/get_menu_locations/permissions', '__return_true', $request);
            },
        ));
        // REPLACEMENT
        register_rest_route( $this->namespace, '/locations', array(
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => array($this, 'get_menu_locations'),
            'permission_callback' => function (\WP_REST_Request $request) {
                return apply_filters('wprm/get_menu_locations/permissions', '__return_true', $request);
            },
        ));

        // Get menu by term_id
        register_rest_route( $this->namespace, '/menus/(?P<id>[0-9(-]+)', array(
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => array($this, 'get_menu_items'),
            'permission_callback' => function (\WP_REST_Request $request) {
                return apply_filters('wprm/get_menu_items/permissions', '__return_true', $request);
            },
            'args' => array(
                'id' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return apply_filters('wprm/get_menu_items/validate/args/id', is_numeric($param), $param, $request, $key);
                    }
                ),
                'fields' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return apply_filters('wprm/get_menu_items/validate/args/fields', is_string($param), $param, $request, $key);
                    }
                ),
                'nested' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return apply_filters('wprm/get_menu_items/validate/args/nested', absint($param), $param, $request, $key);
                    }
                ),
            ),
        ));

        // Get menu by location slug
        // DEPRECATED
        register_rest_route( $this->namespace, '/menus/locations/(?P<slug>[a-zA-Z(-]+)', array(
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => array($this, 'get_location_menu_items'),
            'permission_callback' => function (\WP_REST_Request $request) {
                return apply_filters('wprm/get_location_menu_items/permissions', '__return_true', $request);
            },
            'args' => array(
                'slug' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return apply_filters('wprm/get_location_menu_items/validate/args/slug', is_string($param), $param, $request, $key);
                    }
                ),
                'fields' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return apply_filters('wprm/get_location_menu_items/validate/args/fields', is_string($param), $param, $request, $key);
                    }
                ),
                'nested' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return apply_filters('wprm/get_location_menu_items/validate/args/nested', absint($param), $param, $request, $key);
                    }
                ),
            ),
        ));
        // REPLACEMENT
        register_rest_route( $this->namespace, '/locations/(?P<slug>[a-zA-Z(-]+)', array(
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => array($this, 'get_location_menu_items'),
            'permission_callback' => function (\WP_REST_Request $request) {
                return apply_filters('wprm/get_location_menu_items/permissions', '__return_true', $request);
            },
            'args' => array(
                'slug' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return apply_filters('wprm/get_location_menu_items/validate/args/slug', is_string($param), $param, $request, $key);
                    }
                ),
                'fields' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return apply_filters('wprm/get_location_menu_items/validate/args/fields', is_string($param), $param, $request, $key);
                    }
                ),
                'nested' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return apply_filters('wprm/get_location_menu_items/validate/args/nested', absint($param), $param, $request, $key);
                    }
                ),
            ),
        ));
    }

    /**
     * Get all menus
     *
     * @todo Separate ACF and native custom_fields, separate get meta concern
     * @return WP_Error|WP_HTTP_Response|WP_REST_Response|mixed
     */
    public function get_menus() {
        $args = apply_filters(
            'wprm/get_menus/wp_get_nav_menus/args',
            array('taxonomy' => 'nav_menu', 'hide_empty' => true, 'suppress_filters' => false)
        );

        $menus = wp_get_nav_menus( $args );

        if (!$menus)
            return rest_ensure_response( null );

        $menus = $this->remove_duplicate_menus( $menus );

        foreach ($menus as $menu) {
            $menu->acf = new \stdClass();

            if ( class_exists('acf') ) {
                if ( $acf_fields = get_fields( $menu ) ) {
                    
                    foreach ($acf_fields as $key => $val) {
                        if ($key[0] !== '_') {
                            $menu->acf->$key = $val ?: null;
                        }
                    }
                }
            }
            
            $menu->meta = new \stdClass();

            if ( $term_meta = get_term_meta( $menu->term_id, null, true) ) {
                foreach ($term_meta as $key => $val) {
                    if ( $key[0] !== '_' && !isset( $menu->acf->$key ) ) {
                        $menu->meta->$key = $val;
                    }
                }
            }
        }

        $menus_data = apply_filters('wprm/get_menus', $menus);

        return rest_ensure_response($menus_data);
    }

    /**
     * WPML adds duplicate menus (for a reason)
     *
     * @see https://wpml.org/forums/topic/using-wp_get_nav_menus-wpml-duplicate-menus-in-the-array/
     * @param Array $menus
     * @return Array $unique_menus
     */
    private function remove_duplicate_menus( $menus ) {
        $duplicate_menu_ids = [];
        $unique_menus = [];

        foreach ( $menus as $menu ) {
            if ( !in_array( $menu->term_id, $duplicate_menu_ids ) ) {
                $unique_menus[] = $menu;
            }

            $duplicate_menu_ids[] = $menu->term_id;
        }

        return $unique_menus;
    }

    /**
     * Get menu locations
     *
     * @return WP_Error|WP_HTTP_Response|WP_REST_Response|mixed
     */
    public function get_menu_locations() {
        $locations = [];

        foreach ( get_registered_nav_menus() as $slug => $description ) {
            $obj = new \stdClass;
            $obj->slug = $slug;
            $obj->description = $description;
            $locations[] = $obj;
        }

        $locations_data = apply_filters( 'wprm/get_menu_locations', $locations );

        return rest_ensure_response( $locations_data );
    }

    /**
     * Get menu items
     *
     * @return WP_Error|WP_HTTP_Response|WP_REST_Response|mixed
     */
    public function get_menu_items( \WP_REST_Request $request ) {
        $menu = null;
        $id = (int) $request->get_param('id');

        // If WPML is active we can get the translated id for the current language
        // by passing any lang id
        // $id = apply_filters( 'wpml_object_id', (int) $request->get_param( 'id' ), 'nav_menu', true );

        if ($menu_items = wp_get_nav_menu_items($id)) {
            $menu = $this->get_item_fields($request, $menu_items);
        }

        $menu_data = apply_filters('wprm/get_menu_items', $menu);

        return rest_ensure_response($menu_data);
    }

    /**
     * Get menu location items
     *
     * @return WP_Error|WP_HTTP_Response|WP_REST_Response|mixed
     */
    public function get_location_menu_items( \WP_REST_Request $request ) {
        $menu = null;

        if ( ($locations = get_nav_menu_locations() ) && isset( $locations[$request->get_param('slug')] ) ) {
            $menu = get_term( $locations[$request->get_param('slug')] );
            if ( $menu_items = wp_get_nav_menu_items($menu->term_id) ) {
                $menu = $this->get_item_fields( $request, $menu_items );
            }
        }

        $menu_data = apply_filters( 'wprm/get_location_menu_items', $menu );

        return rest_ensure_response( $menu_data );
    }

    /**
     * Get menu item response fields
     *
     * @return array
     */
    private function get_item_fields( \WP_REST_Request $request, $menu_items ) {
        $menu = [];
        $nested = $this->nested_request($request);

        $menu_items = apply_filters( 'wprm/get_item_fields/menu_items', $menu_items, $request );

        foreach ( $menu_items as $item ) {
            $custom_fields = $this->get_item_meta( $item->ID );
            $item->meta = $custom_fields['meta'];
            $item->acf = $custom_fields['acf'];
            
            // Add a filter so we can filter fields programatically
            $fields = apply_filters('wprm/get_item_fields/filter_fields', $this->filter_fields( $request ), $request);

            if ( $fields ) {
                $filtered = new \stdClass;
                foreach ( $item as $key => $val ) {
                    if ( in_array( $key, $fields ) ) {
                        $filtered->$key = $item->$key;
                    }

                    $filtered->ID = $item->ID;
                    $filtered->menu_item_parent = $item->menu_item_parent;
                }
            } else {
                $filtered = $item;
            }

            $menu[] = $filtered;
        }

        if ( $nested ) {
            $menu = $this->nest_items($menu);
        }

        return $menu;
    }

    /**
     * Nest children items in parent
     *
     * @return array
     */
    private function nest_items( $menu_items ) {
        $parents = array_values(
            array_filter( $menu_items, function ($m) {
                return $m->menu_item_parent == 0;
            })
        );

        foreach ( $parents as $parent ) {
            $parent->children = null;

            $children = array_filter( $menu_items, function ( $m ) use ( $parent ) {
                return $m->menu_item_parent == $parent->ID;
            });

            if ( $children ) {
                $parent->children = array_values( $children );
            }
        }

        $parents_data = apply_filters( 'wprm/nest_items', $parents );

        return $parents_data;
    }

    private function nested_request( \WP_REST_Request $request )
    {
        return ($request->get_param('nested') && $request->get_param('nested') == 1) || false;
    }

    /**
     * Get item custom fields except built in
     *
     * TODO: Add meta field filter
     * @return array
     */
    private function get_item_meta( $item_id ) {
        
        $acf = new \stdClass();
        $meta = new \stdClass();

        if ( class_exists('acf') ) {
            if ( $acf_fields = get_fields( $item_id ) ) {
                foreach ($acf_fields as $key => $field) {
                    if ($key[0] !== '_') {
                        $acf->$key = $field ?: null;
                    }
                }
            }
        }

        

        if ( $post_meta = get_post_custom( $item_id ) ) {
            foreach ( $post_meta as $key => $val ) {
                if ( $key[0] !== '_' && !isset( $acf->$key ) ) {
                    $meta->$key = $val[0] ?: null;
                }
            }
        }
        
        return [ 'acf' => $acf, 'meta' => $meta ];
    }

    /**
     * Get filters if set
     *
     * @return mixed array|boolean
     */
    private function filter_fields( $request )
    {
        $fields = (string) $request->get_param('fields');

        if ($fields = (string) $request->get_param('fields')) {
            $array = explode(',', $fields);
            return !empty( $array ) ? $array : null;
        }

        return null;
    }

    /**
     * Visual log, print_r wrapper
     *
     */
    public function log($data)
    {
        echo '<pre style="background:#eee;padding:5px;margin-bottom:15px">';
        print_r($data);
        echo '</pre>';
    }
}
