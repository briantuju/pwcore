<?php

use Carbon_Fields\Field;

if ( ! class_exists( 'OrderOptions' ) ) {

  class OrderOptions {

	/**
	 * @var string
	 */
	protected string $theme_option_name = 'pw_orders_email';

	/**
	 * @var string
	 */
	protected string $theme_option_label = 'Orders Email';

	/**
	 * @return string
	 */
	public function get_theme_option_label(): string {
	  return $this->theme_option_label;
	}

	/**
	 * @return string
	 */
	public function get_theme_option_name(): string {
	  return $this->theme_option_name;
	}

	/**
	 * @return array
	 */
	public function get_options(): array {
	  return [
		  Field::make(
			  'text',
			  $this->get_theme_option_name(),
			  __( $this->get_theme_option_label(), 'pwcore' )
		  )
			   ->set_default_value( get_bloginfo( 'admin_email' ) )
			   ->set_help_text( 'Email used to send messages to client regarding orders' )
	  ];
	}
  }
}
