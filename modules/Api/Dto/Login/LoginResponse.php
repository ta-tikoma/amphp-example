<?php

declare(strict_types=1);

namespace Modules\Api\Dto\Login;

final class LoginResponse
{
    public function __construct(
        public readonly TokensResponse $tokens,
        public readonly MeResponse $me,
    ) {
    }
}
