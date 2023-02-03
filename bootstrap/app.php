<?php

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use PWCore\Services\EmailOptions;
use PWCore\Services\Orders\OrderOptions;

add_action( 'wp_enqueue_scripts', 'pwcore_enqueue_assets' );

add_action( 'after_setup_theme', 'load_carbon_fields' );

add_action( 'carbon_fields_register_fields', 'create_options_page' );

function pwcore_enqueue_assets(): void {
  wp_enqueue_style(
	  'pwcore_style',
	  PW_PLUGIN_URL . 'assets/css/style.css',
	  false
  );

  wp_enqueue_style(
	  'pwcore_style',
	  PW_PLUGIN_URL . 'assets/css/utilities.css',
	  false
  );

  wp_enqueue_style(
	  'pwcore_bootstrap',
	  PW_PLUGIN_URL . 'assets/css/bootstrap.min.css',
	  false
  );

  wp_enqueue_script(
	  'bootstrap_js',
	  PW_PLUGIN_URL . 'assets/js/bootstrap.bundle.min.js',
	  [],
	  '5.3.0',
	  true
  );

  wp_enqueue_script(
	  'pwcore_client',
	  PW_PLUGIN_URL . 'assets/js/client.js',
	  [ 'jquery' ],
	  '1.0.0'
  );
}

/**
 * We boot Carbon_Fields after the `after_setup_theme` WordPress hook
 */
function load_carbon_fields(): void {
  Carbon_Fields::boot();
}

function create_options_page(): void {
  $orderOptions = new OrderOptions;
  $emailOptions = new EmailOptions;

  Container::make( 'theme_options', __( 'PW Core', 'pwcore' ) )
		   ->set_icon( 'dashicons-screenoptions' )
		   ->add_tab( 'Orders', array_merge( $orderOptions->get_options() ) )
		   ->add_tab( 'Emails', array_merge( $emailOptions->get_options() ) );
}
