<?php

if ( ! function_exists( 'pwcore_get_theme_options' ) ) {
  function pwcore_get_theme_options( string $name ) {
	return carbon_get_theme_option( $name );
  }
}

if ( ! function_exists( 'pwcore_needs_login' ) ) {
  function pwcore_needs_login(): void {
	if ( ! is_user_logged_in() ) {
	  auth_redirect();
	}
  }
}