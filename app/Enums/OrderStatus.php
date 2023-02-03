<?php

namespace PWCore\Enums;

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use PWCore\Concerns\EnumToArray;

enum OrderStatus: string {
  use EnumToArray;

  /**
   * Customer has created a new order
   * */
  case PENDING = 'pending';

  /**
   * Order is being processed
   * */
  case IN_PROGRESS = 'in-progress';

  /**
   * Order has been processed successfully
   * */
  case COMPLETED = 'completed';

  /**
   * Order canceled by the admin or client
   * */
  case CANCELED = 'canceled';

  /**
   * Order taken back for revision
   * */
  case REVISION = 'revision';
}