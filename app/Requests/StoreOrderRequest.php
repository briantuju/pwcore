<?php

namespace PWCore\Requests;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use WP_REST_Request;

class StoreOrderRequest {

  /**
   * @param WP_REST_Request $request
   *
   * @return array
   */
  public function validate( WP_REST_Request $request ): array {
	$data = $request->get_params();

	$data['topic']       = ucfirst( sanitize_text_field( $data['topic'] ) );
	$data['description'] = sanitize_textarea_field( $data['description'] );
	$data['deadline']    = sanitize_text_field( $data['deadline'] );
	$data['package_id']  = sanitize_text_field( $data['package_id'] );

	unset( $data['_wpnonce'] );
	unset( $data['_wp_http_referer'] );
	unset( $data['rest_route'] );

	return $data;
  }
}