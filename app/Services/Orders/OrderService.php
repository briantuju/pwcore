<?php

namespace PWCore\Services\Orders;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use DateTime;
use PWCore\Enums\InvoiceStatus;
use PWCore\Enums\OrderStatus;
use PWCore\Services\EmailOptions;
use PWCore\Services\InvoiceService;
use WP_Post;
use WP_User;

class OrderService {

  protected EmailOptions $email_options;

  protected OrderOptions $order_options;

  protected InvoiceService $invoice_service;

  public function __construct() {
	$this->email_options   = new EmailOptions;
	$this->order_options   = new OrderOptions;
	$this->invoice_service = new InvoiceService;
  }

  /**
   * @param int $order_id
   *
   * @return string
   */
  public function generate_order_number( int $order_id ): string {
	$date = new DateTime( 'now' );

	return "W" . $date->format( 'Y' ) . "_" . $order_id;
  }

  /**
   * @param WP_User $user
   *
   * @return void
   */
  public function send_new_order_email( WP_User $user ): void {
	// Send email to admin
	$admin_name = get_bloginfo( 'name' );
	$admin_mail = pwcore_get_theme_options( $this->order_options->get_order_email() );
	$email      = pwcore_get_theme_options( $this->email_options->get_option_new_order() );

	$headers   = [];
	$headers[] = "From: $admin_name<$admin_mail>";
	$headers[] = "Reply-to: no-reply<$admin_mail>";
	$headers[] = "Content-Type: text/html";

	// If the email is set, we do a string replacement of all placeholders
	if ( $email ) {
	  // For now, we have 'name' only
	  $email = str_replace(
		  "[name]",
		  wp_get_current_user()?->user_login,
		  $email
	  // apply_filters( 'the_content', carbon_get_the_post_meta( '' ) )
	  );
	}
	$subject = "New Order";
	$message = $email;

	wp_mail( $user->user_email, $subject, $message, $headers );
  }

  /**
   * @return array|mixed|void
   */
  public function upload_single_attachment() {
	if ( ! function_exists( 'wp_handle_upload' ) ) {
	  require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	$uploaded_file = $_FILES['attachment'];
	$movefile      = wp_handle_upload( $uploaded_file, [
		'test_form' => false
	] );

	if ( $movefile && ! isset( $movefile['error'] ) ) {
	  return $movefile['error'] ?? [
		  'url'  => $movefile['url'],
		  'file' => $movefile['file'],
		  'type' => $movefile['type'],
	  ];
	} else {
	  wp_send_json( $movefile['error'], 400 );
	}
  }

  /**
   * @param array $data
   * @param WP_User $user
   *
   * @return array|WP_Post|null
   */
  public function create_order( array $data, WP_User $user ): array|WP_Post|null {
	// Prepare order for saving
	$order_data = [
		'post_title'     => $data['topic'],
		'post_type'      => 'pw_orders',
		'post_status'    => 'publish',
		'post_author'    => $user->user_login,
		'post_content'   => $data['description'],
		'comment_status' => 'closed'
	];

	$post_id = wp_insert_post( $order_data );

	// Add order number meta field
	$order_number = $this->generate_order_number( $post_id );
	add_post_meta( $post_id, 'order_number', $order_number );

	// Create order status meta field
	add_post_meta( $post_id, 'order_status', OrderStatus::PENDING->value );

	// Save other meta fields
	foreach ( $data as $key => $value ) {
	  add_post_meta( $post_id, $key, $value );
	}

	return get_post( $post_id );
  }

  /**
   * @param WP_Post $order
   * @param OrderStatus $status
   *
   * @return bool|int
   */
  public function update_status( WP_Post $order, OrderStatus $status ): bool|int {
	return update_post_meta( $order->ID, 'order_status', $status->value );
  }

  /**
   * @param int|string $user_id
   *
   * @return array
   */
  public function get_orders_by_user_id( int|string $user_id ): array {
	return get_posts( [
		'post_type'   => 'pw_orders',
		'numberposts' => - 1,
		'meta_query'  => [
			[
				'key'   => 'user_id',
				'value' => $user_id,
			]
		]
	] );
  }

  /**
   * @param int|string $order_id
   *
   * @return array|WP_Post|null
   */
  public function find_order_by_id( int|string $order_id ): array|WP_Post|null {
	return get_post( $order_id );
  }

  /**
   * @param int|string $order_id
   *
   * @return WP_Post|null
   */
  public function get_pending_payment( int|string $order_id ): WP_Post|null {
	$payment  = null;
	$invoices = $this->invoice_service->get_order_invoices( $order_id );

	if ( count( $invoices ) ) {
	  foreach ( $invoices as $invoice ) {
		$status = get_post_meta( $invoice->ID, 'invoice_status', true );

		// We just take one invoice at a time, hence the need for break statement
		if ( $status === InvoiceStatus::PENDING->value ) {
		  $payment = $invoice;
		  break;
		}
	  }
	};

	return $payment;
  }
}