<?php

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;
use PWCore\Services\EmailOptions;
use PWCore\Services\Orders\OrderOptions;

add_action( 'admin_menu', 'pwcore_remove_publish_metabox' );

add_action( 'wp_enqueue_scripts', 'pwcore_enqueue_assets' );

add_action( 'admin_enqueue_scripts', 'pwcore_admin_styles' );

add_action( 'after_setup_theme', 'pwcore_hide_admin_bar' );

add_action( 'after_setup_theme', 'load_carbon_fields' );

add_action( 'carbon_fields_register_fields', 'create_options_page' );

add_action( 'wp_login', 'pwcore_custom_login_redirect', 10, 2 );

function pwcore_admin_styles(): void {
  // Register the stylesheet and then enqueue it
  wp_register_style(
	  'pwcore_admin_bootstrap',
	  PW_PLUGIN_URL . 'assets/css/bootstrap.min.css',
	  false,
	  '1.0.0'
  );
  wp_enqueue_style( 'pwcore_admin_bootstrap' );
}

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
	  '1.0.0',
	  true
  );

  // Pass nonce to JS.
  wp_localize_script( 'pwcore_client', 'WPNonce', [
	  'nonce' => wp_create_nonce( 'wp_rest' ),
  ] );
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
		   ->add_tab( 'General', [
			   Field::make( 'text', $emailOptions->get_option_default(),
				   __( $emailOptions->get_label_default(), 'pwcore' )
			   )
					->set_default_value( get_bloginfo( 'admin_email' ) )
					->set_help_text( 'Default website email' )
		   ] )
		   ->add_tab( 'Orders', array_merge( $orderOptions->get_options() ) )
		   ->add_tab( 'Emails', array_merge( $emailOptions->get_options() ) );
}

function pwcore_remove_publish_metabox(): void {
  remove_meta_box( 'submitdiv', 'pw_invoices', 'side' );
  remove_meta_box( 'submitdiv', 'pw_orders', 'side' );
  remove_meta_box( 'submitdiv', 'pw_packages', 'side' );
  remove_meta_box( 'submitdiv', 'pw_transactions', 'side' );
}

function pwcore_custom_login_redirect( $user_login, WP_User $user ) {
  $redirect_url = $_GET['redirect_to'];

  if ( in_array( 'subscriber', $user->roles ) ) {
	$redirect_url = $redirect_url ?? get_site_url() . '/my-orders';
  } else {
	$redirect_url = $redirect_url ?? admin_url();
  }

  wp_redirect( $redirect_url );
  exit;
}

function pwcore_hide_admin_bar(): void {
  if ( ! current_user_can( 'manage_options' ) ) {
	show_admin_bar( false );
  }
}
