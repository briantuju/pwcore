<?php

namespace PWCore\Enums;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use PWCore\Concerns\EnumToArray;

enum InvoiceStatus: string {
  use EnumToArray;

  case PENDING = 'pending';

  case PAID = 'paid';

  case CANCELED = 'canceled';
}
