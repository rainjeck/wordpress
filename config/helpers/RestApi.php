<?php

namespace tnwpt\helpers;

use tnwpt\helpers\View;

class RestApi
{
    public function register()
    {
        // wp-json
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('template_redirect', 'rest_output_link_header', 11);

        add_filter( 'rest_dispatch_request', [&$this, 'filter_rest_dispatch_request'], 10, 4 );

        add_filter('rest_pre_dispatch', [&$this, 'filter_rest_pre_dispatch'], 10, 3);

        // add_action('rest_api_init', [&$this, 'action_rest_api_init']);
    }

    public function filter_rest_dispatch_request($dispatch_result, $request, $route, $handler)
    {
        if ( !in_array($route, []) ) {
            return new \WP_Error('rest_forbidden', '', [ 'status' => 401 ]);
        }

        return $dispatch_result;
    }

    public function filter_rest_pre_dispatch($result, $rest_server, $request)
    {
        // maybe authentication error already set
        if ( !is_null($result) ) {
            return $result;
        }

        // only for `/wp/v2` namespace & admin
        if (
            '/wp/v2' === substr( $request->get_route(), 0, 6 ) &&
            !current_user_can('manage_options')
        ) {
            return new \WP_Error('rest_forbidden', '', [ 'status' => 401 ]);
        }

        return $result;
    }

    public function action_rest_api_init()
    {
        // пространство имен
	    $namespace = 'some-my-namespace';

        // маршрут
	    $route = "/some-my-route";

        // параметры конечной точки (маршрута)
        $route_params = [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [&$this, 'some_my_function'],
            'permission_callback' => '__return_true',
        ];

        register_rest_route($namespace, $route, $route_params);
    }
}
