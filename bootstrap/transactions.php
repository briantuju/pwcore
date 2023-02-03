<?php

add_action( 'init', 'pwcore_create_transactions' );

add_filter( 'manage_pw_transactions_posts_columns', 'pwcore_custom_transactions_columns' );

add_action( 'manage_pw_transactions_posts_custom_column', 'pwcore_fill_transactions_columns', 10, 2 );

function pwcore_create_transactions(): void {
  $args = [
	  'public'             => true,
	  'publicly_queryable' => false,
	  'has_archive'        => true,
	  'labels'             => [
		  'name'          => 'Transactions',
		  'singular_name' => 'Transaction',
		  'edit_item'     => 'Transaction Page'
	  ],
	  'supports'           => false,
	  'capability_type'    => 'post',
	  'capabilities'       => [
		  'create_posts' => false
	  ],
	  'menu_icon'          => 'dashicons-money-alt',
	  'map_meta_cap'       => true,
	  'rewrite'            => [ 'slug' => 'transactions' ]
  ];

  register_post_type( 'pw_transactions', $args );
}

function pwcore_custom_transactions_columns( array $columns ): array {
  return [
	  'cb'         => __( $columns['cb'], 'pwcore' ),
	  'ref_number' => __( 'Ref Number', 'pwcore' ),
	  'amount'     => __( 'Amount', 'pwcore' ),
	  'invoice_id' => __( 'Invoice', 'pwcore' ),
	  'user_id'    => __( 'Customer', 'pwcore' ),
	  'order_id'   => __( 'Order', 'pwcore' ),
	  'date'       => __( $columns['date'], 'pwcore' )
  ];
}

function pwcore_fill_transactions_columns( $column, $post_id ): void {
  switch ( $column ) {
	case 'ref_number':
	  $ref_number = get_post_meta( get_the_ID(), 'ref_number', true );
	  echo "<strong>$ref_number</strong>";
	  break;
	case 'amount':
	  $amount = get_post_meta( get_the_ID(), 'amount', true );
	  echo "<strong>$ $amount</strong>";
	  break;
	case 'invoice_id':
	  $status = ucfirst( get_post_meta( get_the_ID(), 'invoice_id', true ) );
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
