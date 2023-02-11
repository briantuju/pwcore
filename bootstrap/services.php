<?php

add_action( 'init', 'pwcore_create_services_page' );

function pwcore_create_services_page(): void {
  $args = [
	  'public'       => true,
	  'has_archive'  => true,
	  'labels'       => [
		  'name'          => 'Services',
		  'singular_name' => 'Service'
	  ],
	  'taxonomies'   => [ 'category', 'post_tag' ],
	  'supports'     => [
		  'title',
		  'editor',
		  'comments',
		  'revisions',
		  'author',
		  'excerpt',
		  'page-attributes',
		  'thumbnail',
		  'custom-fields'
	  ],
	  'show_in_rest' => true,
	  'menu_icon'    => 'dashicons-media-text',
	  'rewrite'      => [ 'slug' => 'service' ]
  ];

  register_post_type( 'pw_services', $args );
}
