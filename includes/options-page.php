<?php

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'after_setup_theme', 'load_carbon_fields' );
add_action( 'carbon_fields_register_fields', 'create_options_page' );

/**
 * We boot Carbon_Fields after the `after_setup_theme` WordPress hook
 */
function load_carbon_fields(): void {
  Carbon_Fields::boot();
}


function create_options_page(): void {

  include_once PW_PLUGIN_PATH . '/includes/orders/order-options.php';
  include_once PW_PLUGIN_PATH . '/includes/emails/email-options.php';

  $orderOptions = new OrderOptions;
  $emailOptions = new EmailOptions;

  Container::make( 'theme_options', __( 'PW Core', 'pwcore' ) )
		   ->set_icon( 'dashicons-screenoptions' )
		   ->add_tab( 'Orders', array_merge( $orderOptions->get_options() ) )
		   ->add_tab( 'Emails', array_merge( $emailOptions->get_options() ) );
}
