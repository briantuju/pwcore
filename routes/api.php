<?php

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use PWCore\Controllers\OrderController;
use PWCore\Requests\StoreOrderRequest;

/*
 * In this file, we can register all the routes that will
 * be made via the rest endpoints or via API call
 */

add_action( 'rest_api_init', 'pwcore_create_rest_endpoints' );

function pwcore_create_rest_endpoints(): void {
  register_rest_route( 'pwcore/v1', 'orders', [
	  'methods'             => 'POST',
	  'callback'            => 'pwcore_store_order',
	  'permission_callback' => function () {
		return '';
	  }
  ] );

  register_rest_route( 'pwcore/v1', 'payments', [
	  'methods'             => 'POST',
	  'callback'            => 'pwcore_store_payment',
	  'permission_callback' => function () {
		return '';
	  }
  ] );
}

function pwcore_store_order( WP_REST_Request $request ): WP_REST_Response {
  $data = ( new StoreOrderRequest )->validate( $request->get_params() );

  ( new OrderController )->store( $data );

  return new WP_REST_Response( 'Order created', 201 );
}

function pwcore_store_payment( WP_REST_Request $request ): WP_REST_Response {

  return new WP_REST_Response( "Success" );
}