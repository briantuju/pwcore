<?php

namespace PWCore\Services\Orders;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use DateTime;
use PWCore\Enums\OrderStatus;
use PWCore\Services\EmailOptions;

class OrderService {

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
   * @return void
   */
  public function send_new_order_email(): void {
	// Send email to admin
	$admin_name = get_bloginfo( 'name' );
	$admin_mail = pwcore_get_theme_options( ( new OrderOptions )->get_option_name() );
	$email      = pwcore_get_theme_options( ( new EmailOptions )->get_option_new_order() );

	$headers   = [];
	$headers[] = "From: $admin_name <$admin_mail>";
	$headers[] = "Reply-to: no-reply@example.com";
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

	wp_mail( $admin_mail, $subject, $message, $headers );
  }

  public function generate_order_invoice( int $order_id, float $amount, int $user_id ) {
	// TODO:
  }

  /**
   * @return array|mixed
   */
  public function upload_single_attachment(): mixed {
	if ( ! function_exists( 'wp_handle_upload' ) ) {
	  require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	$upload = wp_handle_upload( $_FILES['attachment'], [
		'test_form' => false
	] );

	return $upload['error'] ?? [
		'url'  => $upload['url'],
		'file' => $upload['file'],
		'type' => $upload['type'],
	];
  }

  /**
   * @param array $data
   *
   * @return void
   */
  public function create_order( array $data ): void {
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
	$order_number = $this->generate_order_number( $post_id );
	add_post_meta( $post_id, 'order_number', $order_number );

	// Create order status meta field
	add_post_meta( $post_id, 'order_status', OrderStatus::PENDING->value );

	// Save other meta fields
	foreach ( $data as $key => $value ) {
	  add_post_meta( $post_id, $key, $value );
	}
  }
}