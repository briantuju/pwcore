<?php

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use PWCore\Controllers\InvoiceController;
use PWCore\Controllers\OrderController;
use PWCore\Controllers\TransactionController;
use PWCore\Requests\StoreInvoiceRequest;
use PWCore\Requests\StoreOrderRequest;
use PWCore\Requests\StoreTransactionRequest;
use PWCore\Services\Orders\OrderService;

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

  register_rest_route( 'pwcore/v1', 'invoices', [
	  'methods'             => WP_REST_Server::ALLMETHODS,
	  'callback'            => 'pwcore_invoices_api',
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
  $data = ( new StoreOrderRequest )->validate( $request );

  ( new OrderController )->store( $data );

  return new WP_REST_Response( 'Order created', 201 );
}

function pwcore_store_payment( WP_REST_Request $request ): WP_REST_Response {
  $data = ( new StoreTransactionRequest )->validate( $request );

  ( new TransactionController )->store( $data );

  return new WP_REST_Response( 'Order paid successfully', 201 );
}

function pwcore_invoices_api( WP_REST_Request $request ): WP_REST_Response {
  $method = $request->get_method();
  $params = $request->get_params();

  switch ( $method ) {
	case WP_REST_Server::READABLE:
	{
	  if ( isset( $params['user_id'] ) ) {
		$orders = ( new OrderService )->get_orders_by_user_id( $params['user_id'] );

		wp_send_json( $orders );
	  } else {
		return new WP_REST_Response( 'user_id parameter missing', 422 );
	  }
	}

	case WP_REST_Server::CREATABLE:
	{
	  if ( isset( $params['user_id'], $params['order_id'], $params['amount'] ) ) {
		$data = ( new StoreInvoiceRequest )->validate( $request );

		return ( new InvoiceController )->store( $data );
	  } else {
		return new WP_REST_Response( 'All parameters are required', 422 );
	  }
	}

	default:
	  return new WP_REST_Response( 'This request cannot be handled', 400 );
  }
}
