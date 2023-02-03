<?php

namespace PWCore\Requests;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

class StoreOrderRequest {

  /**
   * @param array $data
   *
   * @return array
   */
  public function validate( array $data ): array {
	$data['topic']       = ucfirst( sanitize_text_field( $data['topic'] ) );
	$data['description'] = sanitize_textarea_field( $data['description'] );
	$data['deadline']    = sanitize_text_field( $data['deadline'] );
	$data['package_id']  = sanitize_text_field( $data['package_id'] );

	unset( $data['_wpnonce'] );
	unset( $data['_wp_http_referer'] );
	unset( $data['security'] );
	unset( $data['rest_route'] );

	return $data;
  }
}