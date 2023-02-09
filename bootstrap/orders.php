<?php

add_shortcode( 'new_order_form', 'pwcore_create_order_form' );

add_shortcode( 'order_payment', 'pwcore_order_payment' );

add_shortcode( 'order_details', 'pwcore_order_details' );

add_shortcode( 'my_orders', 'pwcore_my_orders' );

add_action( 'init', 'pwcore_create_orders_page' );

add_action( 'add_meta_boxes', 'pwcore_create_order_meta_box' );

add_filter( 'manage_pw_orders_posts_columns', 'pwcore_custom_orders_columns' );

add_action( 'manage_pw_orders_posts_custom_column', 'pwcore_fill_orders_columns', 10, 2 );

function pwcore_create_order_form(): bool|string {
  ob_start();

  include PW_PLUGIN_PATH . '/views/orders/new-order-form.php';

  return ob_get_clean();
}

function pwcore_order_payment(): bool|string {
  ob_start();

  include PW_PLUGIN_PATH . '/views/orders/order-payment.php';

  return ob_get_clean();
}

function pwcore_my_orders(): bool|string {
  ob_start();

  include PW_PLUGIN_PATH . '/views/orders/my-orders.php';

  return ob_get_clean();
}

function pwcore_order_details(): bool|string {
  ob_start();

  include PW_PLUGIN_PATH . '/views/orders/order-details.php';

  return ob_get_clean();
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
	  'menu_icon'          => 'dashicons-portfolio',
	  'map_meta_cap'       => true,
	  'rewrite'            => [ 'slug' => 'orders' ]
  ];

  register_post_type( 'pw_orders', $args );
}

function pwcore_create_order_meta_box(): void {
  add_meta_box(
	  'pw_orders_meta_box',
	  'Order Details',
	  'pwcore_show_order',
	  'pw_orders'
  );
}

function pwcore_custom_orders_columns( array $columns ): array {
  return [
	  'cb'           => __( $columns['cb'], 'pwcore' ),
	  'order_number' => __( 'Order Number', 'pwcore' ),
	  'user_id'      => __( 'Customer', 'pwcore' ),
	  'order_status' => __( 'Order Status', 'pwcore' ),
	  'package_id'   => __( 'Package', 'pwcore' ),
	  'deadline'     => __( 'Order Deadline', 'pwcore' ),
	  'date'         => __( $columns['date'], 'pwcore' )
  ];
}

function pwcore_fill_orders_columns( $column, $post_id ): void {
  switch ( $column ) {
	case 'order_number':
	  $order_number = get_post_meta( $post_id, 'order_number', true );
	  echo "<strong>$order_number</strong>";
	  break;
	case 'user_id':
	  $user_id = get_post_meta( get_the_ID(), 'user_id', true );
	  $user    = get_user_by( 'id', $user_id );
	  echo "<span>" . $user->display_name ?? $user->user_login . "</span>";
	  break;
	case 'topic':
	  echo get_post_meta( $post_id, 'topic', true );
	  break;
	case 'order_status':
	  echo ucfirst( get_post_meta( $post_id, 'order_status', true ) );
	  break;
	case 'deadline':
	  echo get_post_meta( $post_id, 'deadline', true );
	  break;
	case 'package_id':
	  $package_id   = get_post_meta( $post_id, 'package_id', true );
	  $package_name = get_post( $package_id );
	  echo "<strong>$package_name?->post_title</strong>";
	  break;
	default:
	  break;
  }
}

function pwcore_show_order(): void {
  include PW_PLUGIN_PATH . '/views/partials/show-order-meta.php';
}
