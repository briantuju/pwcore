<?php

add_shortcode( 'pw_portfolio', 'pwcore_show_portfolio' );

add_action( 'init', 'pwcore_create_portfolio' );

add_action( 'add_meta_boxes', 'pwcore_portfolio_meta_box', 10, 2 );

add_filter( 'manage_pw_portfolio_posts_columns', 'pwcore_custom_portfolio_columns' );

add_action( 'manage_pw_portfolio_posts_custom_column', 'pwcore_fill_portfolio_columns', 10, 2 );

function pwcore_create_portfolio(): void {
  $args = [
	  'public'             => true,
	  'publicly_queryable' => false,
	  'labels'             => [
		  'name'          => 'Portfolio',
		  'singular_name' => 'Portfolio'
	  ],
	  'supports'           => false,
	  'menu_icon'          => 'dashicons-buddicons-buddypress-logo',
	  'rewrite'            => [ 'slug' => 'portfolio' ]
  ];

  register_post_type( 'pw_portfolio', $args );
}

function pwcore_portfolio_meta_box( $post_type, $post ): void {
  $action = get_current_screen()?->action;

//  if ( $action === 'add' ) {
  //  }
  add_meta_box(
	  'pw_create_portfolio_meta_box',
	  'Create Portfolio',
	  'pwcore_create_portfolio_item',
	  'pw_portfolio'
  );
}

function pwcore_create_portfolio_item( WP_Post $post ) {
  include PW_PLUGIN_PATH . '/views/portfolio/create-portfolio.php';
}

function pwcore_custom_portfolio_columns( array $columns ): array {
  return [
	  'cb'    => __( $columns['cb'], 'pwcore' ),
	  'title' => __( $columns['title'], 'pwcore' ),
	  'type'  => __( 'Type', 'pwcore' ),
	  'url'   => __( 'URL', 'pwcore' ),
	  'date'  => __( $columns['date'], 'pwcore' )
  ];
}

function pwcore_fill_portfolio_columns( $column, $post_id ): void {
  switch ( $column ) {
	case 'title':
	  $title = get_the_title();
	  echo "<strong>$title</strong>";
	  break;
	case 'url':
	  $url = get_post_meta( get_the_ID(), 'url', true );
	  echo "<a href='$url' target='_blank' rel='noreferrer'>View</a>";
	  break;
	case 'type':
	  $type = get_post_meta( get_the_ID(), 'type', true );
	  echo "<strong>" . ucfirst( $type ) . "</strong>";
	  break;
	default:
	  break;
  }
}

function pwcore_show_portfolio(): bool|string {
  ob_start();

  include PW_PLUGIN_PATH . '/views/portfolio/list-portfolio.php';

  return ob_get_clean();
}
