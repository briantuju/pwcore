<?php

namespace PWCore\Services;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use PWCore\Concerns\UploadImage;
use WP_Post;

class PortfolioService {
  use UploadImage;

  /**
   * @param array $data
   *
   * @return array|WP_Post|null
   */
  public function createOrUpdate( array $data ): array|WP_Post|null {
	if (
		isset( $_FILES['image'] ) &&
		count( $_FILES['image'] ) &&
		$_FILES['image']['error'] === 0
	) {
	  $image = $this->upload();
	}

	$data['image'] = $image ?? null;

	// Check if it's a new post or not
	if ( $portfolio = isset( $data['ID'] ) ? get_post( $data['ID'] ) : null ) {
	  wp_update_post( [
		  'ID'           => $portfolio->ID,
		  'post_title'   => $data['name'],
		  'post_content' => "Portfolio for " . $data['name'],
	  ] );
	} else {
	  // Prepare portfolio for saving
	  $portfolio_data = [
		  'post_title'     => $data['name'],
		  'post_type'      => 'pw_portfolio',
		  'post_status'    => 'publish',
		  'post_author'    => wp_get_current_user()?->user_login,
		  'post_content'   => "Portfolio for " . $data['name'],
		  'comment_status' => 'closed'
	  ];

	  $post_id   = wp_insert_post( $portfolio_data );
	  $portfolio = get_post( $post_id );
	};

	unset( $data['name'] );

	// Update other meta fields
	foreach ( $data as $key => $value ) {
	  update_post_meta( $portfolio->ID, $key, $value );
	}

	return $portfolio;
  }

  /**
   * Get a list of portfolio
   *
   * @return WP_Post[]
   */
  public function query( string $type ): array {
	return get_posts( [
		'post_type'  => 'pw_portfolio',
		'meta_key'   => 'type',
		'meta_value' => $type
	] );
  }
}