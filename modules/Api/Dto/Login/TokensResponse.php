<?php

declare(strict_types=1);

namespace Modules\Api\Dto\Login;

final class TokensResponse
{
    public function __construct(
        public readonly string $token,
        public readonly string $refresh_token,
        public readonly string $status,
    ) {
    }
}
