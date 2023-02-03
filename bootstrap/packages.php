<?php

add_action( 'init', 'pwcore_create_packages' );

add_action( 'add_meta_boxes', 'pwcore_create_package_meta_box' );

function pwcore_create_packages(): void {
  $args = [
	  'public'             => true,
	  'publicly_queryable' => false,
	  'has_archive'        => true,
	  'labels'             => [
		  'name'          => 'Packages',
		  'singular_name' => 'Package'
	  ],
	  'show_in_rest'       => true,
	  'supports'           => [ 'title', 'editor', 'custom-fields' ],
	  'rewrite'            => [ 'slug' => 'packages' ]
  ];

  register_post_type( 'pw_packages', $args );
}

function pwcore_create_package_meta_box(): void {
  add_meta_box(
	  'pw_package_meta_box',
	  'Package Details',
	  'pwcore_show_package',
	  'pw_packages'
  );
}

function pwcore_show_package(): void {
  // Get ALL meta data
  $package_metas = get_post_meta( get_the_ID() );

  // Get SINGLE meta data entry
  $price = get_post_meta( get_the_ID(), 'price', true );

  echo "<div style='display: flex; flex-direction: column; gap: 12px'>";
  echo "<h3 style='margin-bottom: 0'>" . "Price " . "</h3 > "
	   . "<strong style='font-size: 2rem'>$ $price</strong>";
  echo " </div > ";
}