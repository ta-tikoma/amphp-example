<?php

declare(strict_types=1);

namespace Modules\Api\Dto\EntitiesCount;

final class EntitiesCountResponse
{
    public function __construct(
        public readonly int $notifications,
        public readonly int $cart_items,
    ) {
    }
}
