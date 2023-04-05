<?php

declare(strict_types=1);

namespace Modules\Api\Dto\Feed;

final class PaginationResponse
{
    public function __construct(
        public readonly int $per_page,
        public readonly int $current_page,
        public readonly int $total,
        public readonly int $last_page,
    ) {
    }
}
