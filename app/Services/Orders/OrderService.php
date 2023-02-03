<?php

namespace PWCore\Services\Orders;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use DateTime;
use PWCore\Services\EmailOptions;

class OrderService {

  /**
   * @param int $order_id
   *
   * @return string
   */
  public function generate_order_number( int $order_id ): string {
	$date = new DateTime( 'now' );

	return "W" . $date->format( 'Y' ) . "_" . $order_id;
  }

  /**
   * @return void
   */
  public function send_new_order_email(): void {
	// Send email to admin
	$admin_name = get_bloginfo( 'name' );
	$admin_mail = pwcore_get_theme_options( ( new OrderOptions )->get_option_name() );
	$email      = pwcore_get_theme_options( ( new EmailOptions )->get_option_new_order() );

	$headers   = [];
	$headers[] = "From: $admin_name <$admin_mail>";
	$headers[] = "Reply-to: no-reply@example.com";
	$headers[] = "Content-Type: text/html";

	// If the email is set, we do a string replacement of all placeholders
	if ( $email ) {
	  // For now, we have 'name' only
	  $email = str_replace(
		  "[name]",
		  wp_get_current_user()?->user_login,
		  $email
	  // apply_filters( 'the_content', carbon_get_the_post_meta( '' ) )
	  );
	}
	$subject = "New Order";
	$message = $email;

	wp_mail( $admin_mail, $subject, $message, $headers );
  }
}