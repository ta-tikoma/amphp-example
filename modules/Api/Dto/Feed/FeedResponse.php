<?php

declare(strict_types=1);

namespace Modules\Api\Dto\Feed;

final class FeedResponse
{
    public function __construct(
        public readonly array $items,
        public readonly PaginationResponse $pagination
    ) {
    }
}
