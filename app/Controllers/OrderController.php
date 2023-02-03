<?php

namespace PWCore\Controllers;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

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

	// Upload attachments if available
	$attachment = [];
	if ( count( $_FILES['attachment'] ) ) {
	  $attachment = $this->order_service->upload_single_attachment();

	  if ( ! is_array( $attachment ) ) {
		return new WP_REST_Response( $attachment, 400 );
	  }
	}
	$data['attachment'] = $attachment;

	$this->order_service->create_order( $data );

	// Send email
	$this->order_service->send_new_order_email();
  }
}