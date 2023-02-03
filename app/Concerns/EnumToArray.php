<?php

namespace PWCore\Concerns;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

trait EnumToArray {
  /**
   * Return the values of backed enums
   *
   * @return array
   */
  public static function values(): array {
	return array_column( self::cases(), 'value' );
  }

  /**
   * Return the key value pairs
   *
   * @param bool $withTitle
   *
   * @return array
   */
  public static function keyValArr( bool $withTitle = false ): array {
	$arr = [];

	foreach ( self::cases() as $case ) {
	  $arr[ $case->name ] = $withTitle ? ucfirst( $case->value ) : $case->value;
	}

	return $arr;
  }
}