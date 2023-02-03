<?php

namespace PWCore\Controllers;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use PWCore\Enums\OrderStatus;
use PWCore\Services\Orders\OrderService;
use WP_REST_Response;

class OrderController {

  protected OrderService $order_service;

  public function __construct() {
	$this->order_service = new OrderService;
  }

  /**
   * @param array $data
   *
   * @return void|WP_REST_Response|null
   */
  public function store( array $data ) {

	if ( ! function_exists( 'wp_handle_upload' ) ) {
	  require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	$upload = wp_handle_upload( $_FILES['attachment'], [
		'test_form' => false
	] );

	if ( isset( $upload['error'] ) ) {
	  return new WP_REST_Response( $upload['error'], 400 );
	} else {
	  $data['attachment'] = [
		  'url'  => $upload['url'],
		  'file' => $upload['file'],
		  'type' => $upload['type'],
	  ];
	}

	// Prepare order for saving
	$order_data = [
		'post_title'     => $data['topic'],
		'post_type'      => 'pw_orders',
		'post_status'    => 'publish',
		'post_author'    => wp_get_current_user()?->user_login,
		'post_content'   => $data['description'],
		'comment_status' => 'closed'
	];

	$post_id = wp_insert_post( $order_data );

	// Add order number meta field
	$order_number = $this->order_service->generate_order_number( $post_id );
	add_post_meta( $post_id, 'order_number', $order_number );

	// Create order status meta field
	add_post_meta( $post_id, 'order_status', OrderStatus::PENDING->value );

	// Save other meta fields
	foreach ( $data as $key => $value ) {
	  add_post_meta( $post_id, $key, $value );
	}

	// Send email
	( new OrderService )->send_new_order_email();
  }
}