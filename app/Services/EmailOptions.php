<?php

namespace PWCore\Services;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use Carbon_Fields\Field;

class EmailOptions {

  protected string $option_new_order = 'pw_email_new_order';
  protected string $label_new_order = 'Order Created';
  protected string $option_order_pay_request = 'pw_email_order_pay_request';
  protected string $label_order_pay_request = 'Order Payment Requested';
  protected string $option_order_pay_received = 'pw_email_order_pay_received';
  protected string $label_order_pay_received = 'Order Payment Received';
  protected string $option_order_status_update = 'pw_email_order_status_update';
  protected string $label_order_status_update = 'Order Updated';

  /**
   * @return array
   */
  public function get_options(): array {
	return [
		Field::make(
			'rich_text',
			$this->get_option_new_order(),
			__( $this->get_label_new_order(), 'pwcore' )
		)->set_help_text( "Allowed placeholders: <strong>name, order_url</strong>" ),
		Field::make(
			'rich_text',
			$this->get_option_order_pay_request(),
			__( $this->get_label_order_pay_request(), 'pwcore' )
		),
		Field::make(
			'rich_text',
			$this->get_option_order_pay_received(),
			__( $this->get_label_order_pay_received(), 'pwcore' )
		),
		Field::make(
			'rich_text',
			$this->get_option_order_status_update(),
			__( $this->get_label_order_status_update(), 'pwcore' )
		),
	];
  }

  /**
   * @return string
   */
  public function get_option_new_order(): string {
	return $this->option_new_order;
  }

  /**
   * @return string
   */
  public function get_option_order_pay_request(): string {
	return $this->option_order_pay_request;
  }

  /**
   * @return string
   */
  public function get_option_order_pay_received(): string {
	return $this->option_order_pay_received;
  }

  /**
   * @return string
   */
  public function get_option_order_status_update(): string {
	return $this->option_order_status_update;
  }

  /**
   * @return string
   */
  public function get_label_new_order(): string {
	return $this->label_new_order;
  }

  /**
   * @return string
   */
  public function get_label_order_pay_request(): string {
	return $this->label_order_pay_request;
  }

  /**
   * @return string
   */
  public function get_label_order_pay_received(): string {
	return $this->label_order_pay_received;
  }

  /**
   * @return string
   */
  public function get_label_order_status_update(): string {
	return $this->label_order_status_update;
  }
}