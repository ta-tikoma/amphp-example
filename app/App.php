<?php

declare(strict_types=1);

namespace App;

use Dotenv\Dotenv;
use Modules\Api\Clients\ApiClient;
use Modules\Api\Clients\ApiGuestClient;
use Modules\Api\Dto\EntitiesCount\EntitiesCountResponse;
use Modules\Api\Dto\Feed\FeedResponse;
use Modules\Api\Dto\Login\MeResponse;
use Modules\Api\Managers\NetworkManager;
use Modules\Api\Managers\NetworkWithAuthManager;
use Modules\Auth\Managers\AuthManager;
use Modules\Storage\Repositories\TokenRepository;

use function Amp\async;
use function Amp\Future\await;

final class App
{
    public function __construct()
    {
        $dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../');
        $dotenv->load();
    }

    public function run(): void
    {
        echo 'Date: ' . date('d.m.Y H:i:s') . PHP_EOL;

        $host = getenv('HOST');
        $api = new ApiClient(new NetworkWithAuthManager(
            new AuthManager(new ApiGuestClient(new NetworkManager($host)), new TokenRepository()),
            $host
        ));

        $responses = await([
            async(fn () => $api->me()),
            async(fn () => $api->entitiesCount()),
            async(fn () => $api->feed()),
        ]);

        foreach ($responses as $response) {
            if ($response instanceof MeResponse) {
                echo "Name: {$response->name}\n";
            } elseif ($response instanceof EntitiesCountResponse) {
                echo "CartItemsCount: {$response->cart_items}\n";
            } elseif ($response instanceof FeedResponse) {
                echo 'ProductCount: ' . count($response->items) . PHP_EOL;
            } else {
                print_r($response);
            }
        }
    }
}
