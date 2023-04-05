<?php

declare(strict_types=1);

namespace Modules\Auth\Dto;

use Modules\Auth\Enums\TokensStatus;

final class Tokens
{
    public function __construct(
        public readonly string $access_token,
        public readonly string $refresh_token,
        public readonly TokensStatus $status = TokensStatus::GOOD,
    ) {
    }
}
