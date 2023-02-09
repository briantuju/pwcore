<?php

namespace PWCore\Services\Mail;

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
	$headers   = [];
	$headers[] = "From: " . $from ?? self::get_from_address();
	$headers[] = "Reply-to: " . $reply_to ?? "no-reply<" . self::get_from_mail() . ">";
	$headers[] = "Content-Type: text/html";

	wp_mail( $user->user_email, $subject, $email, $headers );
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