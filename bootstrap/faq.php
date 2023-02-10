<?php

add_shortcode( 'faq_component', 'pwcore_faq_component' );

add_action( 'init', 'pwcore_create_faq_page' );

function pwcore_faq_component(): bool|string {
  ob_start();

  include PW_PLUGIN_PATH . '/views/faq/faq-accordion.php';

  return ob_get_clean();
}

function pwcore_create_faq_page(): void {
  $args = [
	  'public'       => true,
	  'has_archive'  => true,
	  'labels'       => [
		  'name'          => 'FAQs',
		  'singular_name' => 'FAQ'
	  ],
	  'taxonomies'   => [ 'category' ],
	  'supports'     => [
		  'title',
		  'editor',
		  'custom-fields'
	  ],
	  'template'     => [],
	  'show_in_rest' => true,
	  'menu_icon'    => 'dashicons-controls-repeat',
	  'rewrite'      => [ 'slug' => 'faq' ]
  ];

  register_post_type( 'pw_faq', $args );
}
