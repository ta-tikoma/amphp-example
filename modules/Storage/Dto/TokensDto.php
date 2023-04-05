<?php

declare(strict_types=1);

namespace Modules\Storage\Dto;

final class TokensDto
{
    public function __construct(
        public readonly string $access_token,
        public readonly string $refresh_token,
    ) {
    }
}
