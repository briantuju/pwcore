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

  foreach ( $post_metas as $key => $value ) {
	echo "<strong>" . ucfirst( $key ) . "</strong>" . " => $value[0] <br/> <br/>";
  }
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
  register_rest_route( 'v1/orders', 'store', [
	  'methods'  => 'POST',
	  'callback' => 'pwcore_store_order'
  ] );
}

function pwcore_store_order( WP_REST_Request $data ): WP_REST_Response {
  $params = $data->get_params();

  // TODO:
  // if (!wp_verify_nonce($params, 'wp_rest')) {
  //   return new WP_REST_Response(null, 422);
  // }

  // Validate data
  $params['topic']       = ucfirst( sanitize_text_field( $params['topic'] ) );
  $params['description'] = sanitize_textarea_field( $params['description'] );
  $params['deadline']    = sanitize_text_field( $params['deadline'] );
  $params['service_id']  = sanitize_text_field( $params['service_id'] );
  unset( $params['_wpnonce'] );
  unset( $params['_wp_http_referer'] );

  // Send email to admin
  $admin_mail = pwcore_get_theme_options( ( new OrderOptions )->get_theme_option_name() );
  $admin_name = get_bloginfo( 'name' );

  $headers   = [];
  $headers[] = "From: $admin_name <$admin_mail>";
  $headers[] = "Reply-to: no-reply@example.com";
  $headers[] = "Content-Type: text/html";
  $message   = "<p style='font-size: larger; color: brown'>A new order has been placed</p>";
  $subject   = "New Order";

  // Save order
  pwcore_save_order( $params );

  wp_mail( $admin_mail, $subject, $message, $headers );

  return new WP_REST_Response( 'Order created', 201 );
}

function pwcore_save_order( array $data ): void {
  $order_data = [
	  'post_title'  => $data['topic'],
	  'post_type'   => 'pw_orders',
	  'post_status' => 'publish'
  ];

  $post_id = wp_insert_post( $order_data );

  foreach ( $data as $key => $value ) {
	add_post_meta( $post_id, $key, $value );
  }
}
