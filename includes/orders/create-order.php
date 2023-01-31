<?php

add_shortcode( 'new_order_form', 'pwcore_create_order_form' );

add_action( 'wp_enqueue_scripts', 'pwcore_enqueue_assets' );

add_action( 'rest_api_init', 'pwcore_create_rest_endpoints' );

add_action( 'init', 'pwcore_create_orders_page' );

add_action( 'add_meta_boxes', 'pwcore_create_meta_box' );

add_filter( 'manage_pw_orders_posts_columns', 'pwcore_custom_orders_columns' );

add_action( 'manage_pw_orders_posts_custom_column', 'pwcore_fill_orders_columns', 10, 2 );

function pwcore_create_order_form(): void {
  include PW_PLUGIN_PATH . './includes/templates/orders/new-order-form.php';
}

function pwcore_enqueue_assets(): void {
  wp_enqueue_style(
	  'pwcore_style',
	  PW_PLUGIN_URL . '/assets/css/style.css',
	  false
  );
}

function pwcore_create_meta_box(): void {
  add_meta_box(
	  'pw_orders_meta_box',
	  'Order Details',
	  'pwcore_show_order',
	  'pw_orders'
  );
}

function pwcore_custom_orders_columns( array $columns ): array {
  return [
	  'cb'         => __( $columns['cb'], 'pwcore' ),
	  'topic'      => __( 'Order Topic', 'pwcore' ),
	  'service_id' => __( 'Service', 'pwcore' ),
	  'deadline'   => __( 'Order Deadline', 'pwcore' ),
	  'date'       => __( $columns['date'], 'pwcore' )
  ];
}

function pwcore_fill_orders_columns( $column, $post_id ): void {
  switch ( $column ) {
	case 'topic':
	  echo get_post_meta( $post_id, 'topic', true );
	  break;
	case 'deadline':
	  echo get_post_meta( $post_id, 'deadline', true );
	  break;
	case 'service_id':
	  echo get_post_meta( $post_id, 'service_id', true );
	  break;
	default:
	  break;
  }
}

function pwcore_show_order(): void {
  // Get ALL meta data
  $post_metas = get_post_meta( get_the_ID() );

  // Get SINGLE meta data entry
  $order_topic = get_post_meta( get_the_ID(), 'topic', true );

  echo "<h1>" . $order_topic . "</h1><br/>";

  unset( $post_metas['_edit_last'] );
  unset( $post_metas['_edit_lock'] );

  echo "<div style='display: flex; flex-direction: column; gap: 12px'>";
  foreach ( $post_metas as $key => $value ) {
	switch ( $key ) {
	  case 'topic':
		break;
	  case 'description':
		echo "<div><h4 style='margin-bottom: 0.25rem'>Order Description</h4>"
			 . "<div style='padding: 1rem; background-color:#fff; border: 1px solid #cecece'>"
			 . "$value[0]</div></div>";
		break;
	  case 'attachment':
		$url = get_post_meta( get_the_ID(), 'attachment', true )['url'];
		echo "<h3 style='margin-bottom: 0'>Attachment</h3>"
			 . "<a href=\"$url\" rel='noreferrer' target='_blank' style='max-width: max-content'>"
			 . "Download</a>";
		break;
	  default:
		echo "<h3 style='margin-bottom: 0'>" . ucfirst( $key ) . "</h3>"
			 . "$value[0] <br/> <br/>";
		break;
	}
  }
  echo "</div>";
}

function pwcore_create_orders_page(): void {
  $args = [
	  'public'             => true,
	  'publicly_queryable' => false,
	  'has_archive'        => true,
	  'labels'             => [
		  'name'          => 'Orders',
		  'singular_name' => 'Order',
		  'edit_item'     => 'Order Page'
	  ],
	  'supports'           => false,
	  'capability_type'    => 'post',
	  'capabilities'       => [
		  'create_posts' => false
	  ],
	  'map_meta_cap'       => true,
	  'rewrite'            => [ 'slug' => 'orders' ]
  ];

  register_post_type( 'pw_orders', $args );
}

function pwcore_create_rest_endpoints(): void {
  register_rest_route( 'pwcore/v1', 'orders', [
	  'methods'  => 'POST',
	  'callback' => 'pwcore_store_order'
  ] );
}

function pwcore_store_order( WP_REST_Request $data ): WP_REST_Response {
  $params = $data->get_params();

  if ( ! function_exists( 'wp_handle_upload' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
  }

  $upload = wp_handle_upload( $_FILES['attachment'], [
	  'test_form' => false
  ] );

  if ( isset( $upload['error'] ) ) {
	return new WP_REST_Response( $upload['error'], 400 );
  } else {
	$params['attachment'] = [
		'url'  => $upload['url'],
		'file' => $upload['file'],
		'type' => $upload['type'],
	];
  }

  // Validate data
  $params['topic']       = ucfirst( sanitize_text_field( $params['topic'] ) );
  $params['description'] = sanitize_textarea_field( $params['description'] );
  $params['deadline']    = sanitize_text_field( $params['deadline'] );
  $params['service_id']  = sanitize_text_field( $params['service_id'] );
  unset( $params['_wpnonce'] );
  unset( $params['_wp_http_referer'] );
  unset( $params['security'] );

  // Save order
  pwcore_save_order( $params );

  // Send email
  pwcore_send_new_order_email();

  return new WP_REST_Response( 'Order created', 201 );
}

function pwcore_send_new_order_email(): void {
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

function pwcore_save_order( array $data ): void {
  $order_data = [
	  'post_title'     => $data['topic'],
	  'post_type'      => 'pw_orders',
	  'post_status'    => 'publish',
	  'post_author'    => wp_get_current_user()?->user_login,
	  'post_content'   => $data['description'],
	  'comment_status' => 'closed'
  ];

  $post_id = wp_insert_post( $order_data );

  foreach ( $data as $key => $value ) {
	add_post_meta( $post_id, $key, $value );
  }
}
