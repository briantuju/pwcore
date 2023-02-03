<?php

namespace PWCore\Services;

use WP_Post;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

class InvoiceService {

  /**
   * @param array $data
   *
   * @return array|WP_Post|null
   */
  public function create_invoice( array $data ): array|WP_Post|null {
	// Prepare invoice for saving
	$invoice_data = [
		'post_title'     => $data['invoice_number'],
		'post_type'      => 'pw_invoices',
		'post_status'    => 'publish',
		'post_author'    => wp_get_current_user()?->user_login,
		'post_content'   => "Invoice for order " . $data['order_number'],
		'comment_status' => 'closed'
	];

	$post_id = wp_insert_post( $invoice_data );

	// Save other meta fields
	foreach ( $data as $key => $value ) {
	  add_post_meta( $post_id, $key, $value );
	}

	return get_post( $post_id );
  }
}