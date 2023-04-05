<?php

declare(strict_types=1);

namespace Modules\Api\Dto\Login;

final class MeResponse
{
    public function __construct(
        public readonly string $avatar_url,
        public readonly string $name,
        public readonly string $phone,
        public readonly string $email,
        public readonly string $status,
    ) {
    }
}
