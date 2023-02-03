<?php

if ( ! function_exists( 'pwcore_get_theme_options' ) ) {
  function pwcore_get_theme_options( string $name ) {
	return carbon_get_theme_option( $name );
  }
}