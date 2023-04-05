<?php

declare(strict_types=1);

namespace Modules\Api\Clients;

use Modules\Api\Dto\EntitiesCount\EntitiesCountResponse;
use Modules\Api\Dto\Feed\FeedResponse;
use Modules\Api\Dto\Feed\PaginationResponse;
use Modules\Api\Dto\Login\MeResponse;
use Modules\Api\Managers\NetworkWithAuthManager;

final class ApiClient
{
    public function __construct(
        private NetworkWithAuthManager $networkWithAuthManager
    ) {
    }

    public function me(): MeResponse
    {
        $json = $this->networkWithAuthManager->get('/api/v1/client-profile/personal/me');

        return new MeResponse(...$json);
    }

    public function entitiesCount(): EntitiesCountResponse
    {
        $json = $this->networkWithAuthManager->get('/api/v1/client-profile/entities-count');

        return new EntitiesCountResponse(...$json);
    }

    public function feed(): FeedResponse
    {
        $json = $this->networkWithAuthManager->get('/api/v1/product/products/feed?per_page=100&page=1');

        return new FeedResponse(
            $json['items'],
            new PaginationResponse(...$json['pagination'])
        );
    }
}
