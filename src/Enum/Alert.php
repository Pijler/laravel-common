<?php

namespace Common\Enum;

use Common\Traits\EnumMethods;

enum Alert: string
{
    use EnumMethods;

    case INFO = 'info';
    case ERROR = 'error';
    case SUCCESS = 'success';
    case WARNING = 'warning';
}
