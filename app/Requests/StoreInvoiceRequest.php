<?php

namespace PWCore\Requests;

use WP_REST_Request;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

class StoreInvoiceRequest {
  /**
   * @param WP_REST_Request $request
   *
   * @return array
   */
  public function validate( WP_REST_Request $request ): array {
	$data = $request->get_params();

	$data['user_id']  = sanitize_text_field( $data['user_id'] );
	$data['order_id'] = sanitize_text_field( $data['order_id'] );
	$data['amount']   = sanitize_text_field( $data['amount'] );

	unset( $data['_wpnonce'] );
	unset( $data['_wp_http_referer'] );
	unset( $data['rest_route'] );

	return $data;
  }
}