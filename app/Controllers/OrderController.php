<?php

namespace PWCore\Controllers;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use PWCore\Enums\InvoiceStatus;
use PWCore\Enums\OrderStatus;
use PWCore\Services\EmailOptions;
use PWCore\Services\InvoiceService;
use PWCore\Services\Mail\EmailService;
use PWCore\Services\Orders\OrderOptions;
use PWCore\Services\Orders\OrderService;
use WP_Post;

class OrderController {

  /**
   * @var OrderService
   */
  protected OrderService $order_service;

  /**
   * @var EmailOptions
   */
  protected EmailOptions $email_options;

  /**
   * @var EmailService
   */
  protected EmailService $email_service;

  /**
   * @var InvoiceService
   */
  protected InvoiceService $invoice_service;

  /**
   * @var OrderOptions
   */
  protected OrderOptions $order_options;

  public function __construct() {
	$this->order_service   = new OrderService;
	$this->invoice_service = new InvoiceService;
	$this->email_options   = new EmailOptions;
	$this->email_service   = new EmailService;
	$this->order_options   = new OrderOptions;
  }

  /**
   * @param array $data
   *
   * @return array|WP_Post|null
   */
  public function store( array $data ): array|WP_Post|null {

	// Upload attachments if available
	$attachment = [];
	if ( count( $_FILES['attachment'] ) ) {
	  $attachment = $this->order_service->upload_single_attachment();
	}
	$data['attachment'] = $attachment;

	// Create the order
	$user            = get_user_by( 'id', get_current_user_id() );
	$data['user_id'] = $user->ID;
	$order           = $this->order_service->create_order( $data, $user );

	// Create an invoice for this order
	$amount       = get_post_meta( $data['package_id'], 'price', true );
	$order_number = get_post_meta( $order->ID, 'order_number', true );
	$this->invoice_service->create_invoice( [
		'title'          => "Invoice for order $order_number",
		'amount'         => $amount,
		'invoice_status' => InvoiceStatus::PENDING->value,
		'user_id'        => $user->ID,
		'order_id'       => $order->ID,
		'invoice_number' => $order_number,
	] );

	// Send email
	$order_mail = pwcore_get_theme_options( $this->order_options->get_order_email() );
	$email      = pwcore_get_theme_options( $this->email_options->get_option_new_order() );
	$email      = str_replace( "[name]", $user?->user_login, $email );
	$this->email_service->send_email(
		$user, "New Order", $email, "Support<$order_mail>"
	);

	return $order;
  }

  /**
   * @param int|string $order_id
   * @param string $status
   *
   * @return void
   */
  public function status_update( int|string $order_id, string $status ): void {
	$order = get_post( $order_id );

	$this->order_service->update_status( $order, OrderStatus::from( $status ) );
  }
}