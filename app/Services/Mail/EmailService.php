<?php

namespace PWCore\Services\Mail;

use DateTime;
use PWCore\Services\EmailOptions;
use WP_User;

class EmailService {
  /**
   * @var string|mixed|null
   */
  protected static string $from_address;

  /**
   * @var string
   */
  protected static string $from_mail;

  /**
   * @var EmailOptions
   */
  protected EmailOptions $email_options;

  public function __construct() {
	$this->email_options = new EmailOptions;

	$admin_name         = get_bloginfo( 'name' );
	$admin_mail         = pwcore_get_theme_options(
		$this->email_options->get_option_default()
	);
	self::$from_address = "$admin_name<$admin_mail>";
	self::$from_mail    = $admin_mail;
  }

  /**
   * @param WP_User $user
   * @param string $subject
   * @param mixed $email
   * @param string|null $from
   * @param string|null $reply_to
   *
   * @return void
   */
  public function send_email(
	  WP_User $user,
	  string $subject,
	  mixed $email,
	  string $from = null,
	  string $reply_to = null,
  ): void {
	$headers       = [];
	$headers[]     = "From: " . $from ?? self::get_from_address();
	$headers[]     = "Reply-to: " . $reply_to ?? "no-reply<" . self::get_from_mail() . ">";
	$headers[]     = "Content-Type: text/html";
	$date          = new DateTime( 'now' );
	$year          = $date->format( 'Y' );
	$site_name     = get_bloginfo( 'name' );
	$custom_header = "<p style='padding: 2rem 1rem; background-color:#e5e7eb; border-top-left-radius: 1rem; border-top-right-radius: 1rem'></p>";
	$custom_footer = "<p style='padding: 2rem 1rem; background-color:#e5e7eb; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem; text-align: center'>&copy; "
					 . $year . " $site_name. All rights reserved</p>";
	$message       = $custom_header . "<div style='padding: 0 1rem'>"
					 . $email . "</div>" . $custom_footer;

	wp_mail( $user->user_email, $subject, $message, $headers );
  }

  /**
   * @return string
   */
  public static function get_from_address(): string {
	return self::$from_address;
  }

  /**
   * @return string
   */
  public static function get_from_mail(): string {
	return self::$from_mail;
  }
}