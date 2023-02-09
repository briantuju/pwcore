<?php

namespace PWCore\Services;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use Carbon_Fields\Field;

class EmailOptions {

  protected string $option_default = 'pw_email';
  protected string $label_default = 'Site Email';
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
		)->set_default_value(
			"<h3>Hello [name],</h3>" .
			"<p>We have received your order. Kindly pay for it to proceed.</p>"
		)->set_help_text( "Allowed placeholders: <strong>name, order_url</strong>" ),
		Field::make(
			'rich_text',
			$this->get_option_order_pay_request(),
			__( $this->get_label_order_pay_request(), 'pwcore' )
		)->set_default_value(
			"<h3>Hello [name],</h3>" .
			"<p>Kindly pay for order [order_number] for us to serve you.</p>"
		),
		Field::make(
			'rich_text',
			$this->get_option_order_pay_received(),
			__( $this->get_label_order_pay_received(), 'pwcore' )
		)->set_default_value(
			"<h3>Hello [name],</h3>" .
			"<p>We have received your payment of [amount] for order [order_number].</p>"
		),
		Field::make(
			'rich_text',
			$this->get_option_order_status_update(),
			__( $this->get_label_order_status_update(), 'pwcore' )
		)->set_default_value(
			"<h3>Hello [name],</h3>" .
			"<p>Your order has been updated from [old_status] to [new_status]</p>"
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

  /**
   * @return string
   */
  public function get_option_default(): string {
	return $this->option_default;
  }

  /**
   * @return string
   */
  public function get_label_default(): string {
	return $this->label_default;
  }
}