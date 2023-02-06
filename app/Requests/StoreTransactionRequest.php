<?php

namespace PWCore\Requests;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use WP_REST_Request;

class StoreTransactionRequest {
  /**
   * @param WP_REST_Request $request
   *
   * @return array
   */
  public function validate( WP_REST_Request $request ): array {
	$data = $request->get_params();

	$data['ref_number'] = sanitize_text_field( $data['ref_number'] );
	$data['invoice_id'] = sanitize_text_field( $data['invoice_id'] );
	$data['order_id']   = sanitize_text_field( $data['order_id'] );

	unset( $data['_wpnonce'] );
	unset( $data['_wp_http_referer'] );
	unset( $data['rest_route'] );

	return $data;
  }
}