<?php

namespace PWCore\Services;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use WP_Post;

class PackageService {

  /**
   * @param string $order_by
   * @param string $dir
   *
   * @return WP_Post[]
   */
  public function index( string $order_by = 'position', string $dir = 'ASC' ): array {
	// Get posts ordered by the meta key

	return get_posts( [
		'post_type'   => 'pw_packages',
		'post_status' => 'publish',
		'numberposts' => - 1,
		'meta_key'    => $order_by,
		'orderby'     => 'meta_value_num',
		'order'       => $dir
	] );
  }
}