<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum RoundOnEnum: int
{
    use EnumHelper;

    case UP = 1;
    case DOWN = 2;
}
