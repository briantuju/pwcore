<?php

namespace PWCore\Controllers;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use PWCore\Enums\InvoiceStatus;
use PWCore\Services\InvoiceService;
use PWCore\Services\Orders\OrderService;
use WP_REST_Response;

class OrderController {

  /**
   * @var OrderService
   */
  protected OrderService $order_service;

  /**
   * @var InvoiceService
   */
  protected InvoiceService $invoice_service;

  public function __construct() {
	$this->order_service   = new OrderService;
	$this->invoice_service = new InvoiceService;
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

	// Create the order
	$user  = get_user_by( 'id', get_current_user_id() );
	$order = $this->order_service->create_order( $data, $user );

	// Create an invoice for this order
	$amount       = get_post_meta( $data['package_id'], 'price', true );
	$order_number = get_post_meta( $order->ID, 'order_number', true );

	$invoice = $this->invoice_service->create_invoice( [
		'title'          => "Invoice for order $order_number",
		'amount'         => $amount,
		'invoice_status' => InvoiceStatus::PENDING->value,
		'user_id'        => $user->ID,
		'order_id'       => $order->ID,
		'invoice_number' => $order_number,
	] );

	// Send email
	$this->order_service->send_new_order_email( $user );
  }
}