<?php

namespace PWCore\Controllers;

use PWCore\Enums\InvoiceStatus;
use PWCore\Services\EmailOptions;
use PWCore\Services\InvoiceService;
use PWCore\Services\Mail\EmailService;
use PWCore\Services\Orders\OrderOptions;
use PWCore\Services\Orders\OrderService;
use WP_REST_Response;

class InvoiceController {

  protected OrderService $order_service;

  protected InvoiceService $invoice_service;

  protected EmailOptions $email_options;

  protected EmailService $email_service;

  protected OrderOptions $order_options;

  public function __construct() {
	$this->order_service   = new OrderService;
	$this->order_options   = new OrderOptions;
	$this->invoice_service = new InvoiceService;
	$this->email_options   = new EmailOptions;
	$this->email_service   = new EmailService;
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
	$user         = get_user_by( 'id', get_current_user_id() );

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

	// Send payment received email
	$order_mail = pwcore_get_theme_options( $this->order_options->get_order_email() );
	$email      = pwcore_get_theme_options(
		$this->email_options->get_option_new_invoice()
	);
	$email      = str_replace( "[name]", $user?->user_login, $email );
	$email      = str_replace( "[amount]", $data['amount'], $email );
	$email      = str_replace( "[order_number]", $order_number, $email );
	$this->email_service->send_email(
		$user,
		"Invoice for Order " . $order_number,
		$email, "Support<$order_mail>"
	);

	return new WP_REST_Response( 'Invoice created successfully!' );
  }
}