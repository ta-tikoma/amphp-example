<?php

declare(strict_types=1);

namespace Modules\Api\Clients;

use Modules\Api\Dto\Login\LoginResponse;
use Modules\Api\Dto\Login\MeResponse;
use Modules\Api\Dto\Login\TokensResponse;
use Modules\Api\Managers\NetworkManager;

final class ApiGuestClient
{
    public function __construct(
        private NetworkManager $networkManager,
    ) {
    }

    /**
     * Вход
     */
    public function login(string $phone, string $password): LoginResponse
    {
        $json = $this->networkManager->post(
            '/api/v2/client-profile/authentication/password/login',
            ['phone' => $phone, 'password' => $password]
        );

        return new LoginResponse(
            new TokensResponse(...$json['tokens']),
            new MeResponse(...$json['me']),
        );
    }

    /**
     * Вход
     */
    public function refresh(string $refreshToken): TokensResponse
    {
        $json = $this->networkManager->post(
            '/api/v1/client-profile/authentication/refresh',
            ['refresh_token' => $refreshToken]
        );

        return new TokensResponse(...$json);
    }
}
