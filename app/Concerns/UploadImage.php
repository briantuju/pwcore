<?php

namespace PWCore\Concerns;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

trait UploadImage {

  /**
   * @param string $name
   *
   * @return array|mixed|null
   */
  public function upload( string $name = 'image' ): mixed {
	if ( ! function_exists( 'wp_handle_upload' ) ) {
	  require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	$uploaded_file = $_FILES[ $name ];
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
	  return null;
	}
  }
}