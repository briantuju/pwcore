<?php

namespace PWCore\Services\Orders;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use Carbon_Fields\Field;

class OrderOptions {

  /**
   * @var string
   */
  protected string $order_email = 'pw_orders_email';

  /**
   * @var string
   */
  protected string $order_email_label = 'Orders Email';

  /**
   * @return string
   */
  public function get_order_email_label(): string {
	return $this->order_email_label;
  }

  /**
   * @return string
   */
  public function get_order_email(): string {
	return $this->order_email;
  }

  /**
   * @return array
   */
  public function get_options(): array {
	return [
		Field::make(
			'text',
			$this->get_order_email(),
			__( $this->get_order_email_label(), 'pwcore' )
		)
			 ->set_default_value( get_bloginfo( 'admin_email' ) )
			 ->set_help_text( 'Email used to send messages to client regarding orders' )
	];
  }
}