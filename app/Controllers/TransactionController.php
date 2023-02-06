<?php

namespace PWCore\Controllers;

use PWCore\Enums\InvoiceStatus;
use PWCore\Enums\OrderStatus;
use PWCore\Enums\TransactionStatus;
use PWCore\Services\InvoiceService;
use PWCore\Services\Orders\OrderService;

class TransactionController {

  protected OrderService $order_service;

  protected InvoiceService $invoice_service;

  public function __construct() {
	$this->order_service   = new OrderService;
	$this->invoice_service = new InvoiceService;
  }

  public function store( array $data ) {
	// Prepare data
	$user         = get_user_by( 'id', get_current_user_id() );
	$order        = get_post( $data['order_id'] );
	$invoice      = get_post( $data['invoice_id'] );
	$order_number = get_post_meta( $order->ID, 'order_number', true );

	$transaction_data = [
		'post_title'     => "Order " . $order_number . " payment",
		'post_type'      => 'pw_transactions',
		'post_status'    => 'publish',
		'post_author'    => $user->user_login,
		'post_content'   => "Payment for order " . $order_number,
		'comment_status' => 'closed'
	];

	$post_id = wp_insert_post( $transaction_data );

	$data['amount']  = get_post_meta( $invoice->ID, 'amount', true );
	$data['user_id'] = $user->ID;
	add_post_meta( $post_id, 'transaction_status', TransactionStatus::COMPLETED->value );

	// Save other meta fields
	foreach ( $data as $key => $value ) {
	  add_post_meta( $post_id, $key, $value );
	}

	// Update order status and invoice status
	$this->order_service->update_status( $order, OrderStatus::IN_PROGRESS );
	$this->invoice_service->update_status( $invoice, InvoiceStatus::PAID );

	return get_post( $post_id );
  }
}