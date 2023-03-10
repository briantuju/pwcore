<?php

add_action( 'init', 'pwcore_create_invoices' );

add_action( 'add_meta_boxes', 'pwcore_invoice_meta_box', 10, 2 );

add_filter( 'manage_pw_invoices_posts_columns', 'pwcore_custom_invoices_columns' );

add_action( 'manage_pw_invoices_posts_custom_column', 'pwcore_fill_invoices_columns', 10, 2 );

function pwcore_create_invoices(): void {
  $args = [
	  'public'             => true,
	  'publicly_queryable' => false,
	  'has_archive'        => true,
	  'labels'             => [
		  'name'          => 'Invoices',
		  'singular_name' => 'Invoice',
		  'edit_item'     => 'Invoice Page'
	  ],
	  'supports'           => false,
	  'menu_icon'          => 'dashicons-text-page',
	  'map_meta_cap'       => true,
	  'rewrite'            => [ 'slug' => 'invoices' ]
  ];

  register_post_type( 'pw_invoices', $args );
}

function pwcore_invoice_meta_box( $post_type, $post ): void {
  $action = get_current_screen()?->action;

  if ( $action === 'add' ) {
	add_meta_box(
		'pw_create_invoice_meta_box',
		'Create Invoice',
		'pwcore_create_invoice',
		'pw_invoices'
	);
  } else {
	add_meta_box(
		'pw_invoice_details_meta_box',
		'Invoice Details',
		'pwcore_show_invoice',
		'pw_invoices'
	);
  }
}

function pwcore_create_invoice(): void {
  include PW_PLUGIN_PATH . '/views/invoices/create-invoice.php';
}

function pwcore_show_invoice(): void {
  // Get ALL meta data
  $post_metas = get_post_meta( get_the_ID() );

  // Get SINGLE meta data entry
  $invoice_title = get_post_meta( get_the_ID(), 'title', true );

  echo "<h1>" . $invoice_title . "</h1><br/>";

  unset( $post_metas['_edit_last'] );
  unset( $post_metas['_edit_lock'] );

  echo "<div style='display: flex; flex-direction: column; gap: 12px; font-size: 1.15rem'>";
  foreach ( $post_metas as $key => $value ) {
	switch ( $key ) {
	  case 'title':
		break;
	  case 'invoice_number':
		echo "<h3 style='margin-bottom: 0'>" . "Invoice Number" . "</h3>"
			 . "$value[0] <br/>";
		break;
	  case 'amount':
		echo "<h3 style='margin-bottom: 0'>" . "Amount" . "</h3>"
			 . ucfirst( $value[0] ) . " <br/>";
		break;
	  case 'invoice_status':
		echo "<h3 style='margin-bottom: 0'>" . "Invoice Status" . "</h3>"
			 . ucfirst( $value[0] ) . " <br/>";
		break;
	  case 'user_id':
		$user = get_user_by( 'id', $value[0] );
		echo "<h3 style='margin-bottom: 0'>" . "Customer" . "</h3>"
			 . ucfirst( $user->display_name ) . " <br/>";
		break;
	  case 'order_id':
		$order = get_post_meta( $value[0], 'order_number', true );
		echo "<h3 style='margin-bottom: 0'>" . "Order" . "</h3>"
			 . ucfirst( $order ) . " <br/>";
		break;
	  default:
		echo "<h3 style='margin-bottom: 0'>" . ucfirst( $key ) . "</h3>"
			 . "$value[0] <br/>";
		break;
	}
  }
  echo "</div>";
}

function pwcore_custom_invoices_columns( array $columns ): array {
  return [
	  'cb'             => __( $columns['cb'], 'pwcore' ),
	  'invoice_number' => __( 'Invoice Number', 'pwcore' ),
	  'amount'         => __( 'Amount', 'pwcore' ),
	  'invoice_status' => __( 'Invoice Status', 'pwcore' ),
	  'user_id'        => __( 'Customer', 'pwcore' ),
	  'order_id'       => __( 'Order', 'pwcore' ),
	  'date'           => __( $columns['date'], 'pwcore' ),
  ];
}

function pwcore_fill_invoices_columns( $column, $post_id ): void {
  switch ( $column ) {
	case 'invoice_number':
	  $invoice_number = get_post_meta( get_the_ID(), 'invoice_number', true );
	  echo "<strong>$invoice_number</strong>";
	  break;
	case 'amount':
	  $amount = get_post_meta( get_the_ID(), 'amount', true );
	  echo "<strong>$ $amount</strong>";
	  break;
	case 'invoice_status':
	  $status = ucfirst( get_post_meta( get_the_ID(), 'invoice_status', true ) );
	  echo "<span>$status</span>";
	  break;
	case 'user_id':
	  $user_id = get_post_meta( get_the_ID(), 'user_id', true );
	  $user    = get_user_by( 'id', $user_id );
	  echo "<strong>" . $user->display_name ?? $user->user_login . "</strong>";
	  break;
	case 'order_id':
	  $order_id     = get_post_meta( get_the_ID(), 'order_id', true );
	  $order_number = get_post_meta( $order_id, 'order_number', true );
	  echo "<strong>$order_number</strong>";
	  break;
	default:
	  break;
  }
}