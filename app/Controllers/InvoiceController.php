<?php

namespace PWCore\Controllers;

use PWCore\Enums\InvoiceStatus;
use PWCore\Services\InvoiceService;
use PWCore\Services\Orders\OrderService;
use WP_REST_Response;

class InvoiceController {

  protected OrderService $order_service;

  protected InvoiceService $invoice_service;

  public function __construct() {
	$this->order_service   = new OrderService;
	$this->invoice_service = new InvoiceService;
  }

  /**
   * @param array $data
   *
   * @return WP_REST_Response
   */
  public function store( array $data ): WP_REST_Response {
	// Get order
	$order        = $this->order_service->find_order_by_id( $data['order_id'] );
	$order_number = get_post_meta( $order->ID, 'order_number', true );

	// Void any invoice for this order, that is UNPAID
	$invoices = $this->invoice_service->get_order_invoices( $data['order_id'] );
	if ( count( $invoices ) ) {
	  foreach ( $invoices as $invoice ) {
		$this->invoice_service->update_status( $invoice, InvoiceStatus::CANCELED );
	  }
	}

	// Create invoice
	$this->invoice_service->create_invoice( [
		'title'          => "Invoice for order $order_number",
		'amount'         => $data['amount'],
		'invoice_status' => InvoiceStatus::PENDING->value,
		'user_id'        => $data['user_id'],
		'order_id'       => $order->ID,
		'invoice_number' => $order_number,
	] );

	return new WP_REST_Response( 'Invoice created successfully!' );
  }
}