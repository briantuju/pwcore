<?php

add_action( 'init', 'pwcore_create_blog_posts_page' );

function pwcore_create_blog_posts_page(): void {
  $args = [
	  'public'       => true,
	  'has_archive'  => true,
	  'labels'       => [
		  'name'          => 'Blog',
		  'singular_name' => 'Blog Post'
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
		  'post-formats',
		  'custom-fields'
	  ],
	  'template'     => [],
	  'show_in_rest' => true,
	  'menu_icon'    => 'dashicons-media-text',
	  'rewrite'      => [ 'slug' => 'blog' ]
  ];

  register_post_type( 'pw_blog', $args );
}
