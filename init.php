<?php 

/**
 * Plugin Name: WordPress JWT Rest API Middlewear
 * Description: WordPress middlewear composer plugin that uses JWTs
 * Author: Gemma Black <gblackuk@gmail.com>
 */

/**
 * Prototype
 * @todo rewrite properly
 */

use ReallySimpleJWT\Token;
use Wcom\Jwt\JsonAuth;
use Wcom\Jwt\GetUser;

$secret = 'MY_SEcRET_123!';

add_action('rest_api_init', function() use ($secret) {
    register_rest_route('wcom/jwt/v1', '/action/login', [
        'methods' => 'POST',
        'callback' => function(WP_REST_Request $request) use ($secret) {
            
            $username = $request->get_param('username');
            $password = $request->get_param('password');

            $auth = wp_authenticate($username, $password);
            
            if (is_wp_error($auth)) {
                return wp_send_json_error($auth->get_error_data(), 400);
            }

            $now = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            $token = Token::getToken($auth->ID, $secret, $now, get_home_url());

            return [
                'token' => $token   
            ];
        }
    ]);
});

add_action('rest_api_init', function() use ($secret) {
    register_rest_route('wcom/jwt/v1', '/verify/(?P<token>\S+)', [
        'methods' => 'GET',
        'callback' => function(WP_REST_Request $request) use ($secret) {
            $token = $request->get_param('token');
            $result = JsonAuth::verify(new GetUser, $token, $secret);

            if (!$result) {
                wp_send_json_error([], 400);
            } else {
                wp_send_json(['success' => true]);
            }
        }
    ]);
});