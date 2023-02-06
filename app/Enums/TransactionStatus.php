<?php

namespace PWCore\Enums;

use PWCore\Concerns\EnumToArray;

enum TransactionStatus: string {
  use EnumToArray;

  case PENDING = 'pending';

  case COMPLETED = 'completed';

  case CANCELED = 'canceled';
}
