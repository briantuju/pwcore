<?php

namespace PWCore\Controllers;

use PWCore\Enums\InvoiceStatus;
use PWCore\Enums\OrderStatus;
use PWCore\Enums\TransactionStatus;
use PWCore\Services\EmailOptions;
use PWCore\Services\InvoiceService;
use PWCore\Services\Mail\EmailService;
use PWCore\Services\Orders\OrderOptions;
use PWCore\Services\Orders\OrderService;

class TransactionController {

  protected OrderService $order_service;

  protected InvoiceService $invoice_service;

  protected EmailOptions $email_options;

  protected OrderOptions $order_options;

  protected EmailService $email_service;

  public function __construct() {
	$this->order_service   = new OrderService;
	$this->email_options   = new EmailOptions;
	$this->order_options   = new OrderOptions;
	$this->invoice_service = new InvoiceService;
	$this->email_service   = new  EmailService;
  }

  public function store( array $data ) {
	// Prepare data
	$user         = get_user_by( 'id', get_current_user_id() );
	$order        = get_post( $data['order_id'] );
	$invoice      = get_post( $data['invoice_id'] );
	$order_number = get_post_meta( $order->ID, 'order_number', true );
	$amount       = get_post_meta( $invoice->ID, 'amount', true );

	$transaction_data = [
		'post_title'     => "Order " . $order_number . " payment",
		'post_type'      => 'pw_transactions',
		'post_status'    => 'publish',
		'post_author'    => $user->user_login,
		'post_content'   => "Payment for order " . $order_number,
		'comment_status' => 'closed'
	];

	$post_id = wp_insert_post( $transaction_data );

	$data['amount']  = $amount;
	$data['user_id'] = $user->ID;
	add_post_meta( $post_id, 'transaction_status', TransactionStatus::COMPLETED->value );

	// Save other meta fields
	foreach ( $data as $key => $value ) {
	  add_post_meta( $post_id, $key, $value );
	}

	// Update order status and invoice status
	$this->order_service->update_status( $order, OrderStatus::IN_PROGRESS );
	$this->invoice_service->update_status( $invoice, InvoiceStatus::PAID );

	// Send payment received email
	$order_mail = pwcore_get_theme_options( $this->order_options->get_order_email() );
	$email      = pwcore_get_theme_options(
		$this->email_options->get_option_order_pay_received()
	);
	$email      = str_replace( "[name]", $user?->user_login, $email );
	$email      = str_replace( "[amount]", $amount, $email );
	$email      = str_replace( "[order_number]", $order_number, $email );
	$this->email_service->send_email(
		$user,
		"Order payment received",
		$email, "Support<$order_mail>"
	);

	return get_post( $post_id );
  }
}