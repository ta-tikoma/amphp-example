<?php

declare(strict_types=1);

namespace Modules\Auth\Enums;

enum TokensStatus: int
{
    case GOOD = 1; // Корректные токены
    case OLD = 2; // Устаревший рефреш токен
}

