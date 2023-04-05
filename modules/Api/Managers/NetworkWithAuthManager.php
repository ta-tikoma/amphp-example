<?php

declare(strict_types=1);

namespace Modules\Api\Managers;

use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use Modules\Api\Exceptions\ResponseStatusException;
use Modules\Auth\Managers\AuthManager;

/**
 * Обертка над оберткой HTTP клиента с авторизацией запросов
 */
final class NetworkWithAuthManager extends NetworkManager
{
    private const HEADER = 'Authorization';

    public function __construct(
        private AuthManager $authManager,
        private string $host
    ) {
        parent::__construct($host);
    }

    protected function buildRequest(string $url, string $type = 'GET', string $body = ''): Request
    {
        $token = $this->authManager->getAccessToken();

        $request = parent::buildRequest($url, $type, $body);
        $request->setHeader(self::HEADER, 'Bearer ' . $token);

        return $request;
    }

    protected function callRequest(
        string $url,
        string $type = 'GET',
        string $body = '',
        int $tries = 0
    ): Response {
        try {
            return parent::callRequest($url, $type, $body, $tries);
        } catch (ResponseStatusException $e) {
            // если у нас ошибка авторизации то попробуем с новым токеном
            if ($e->getCode() === 401) {
                $this->authManager->invalidToken(
                    substr($e->request->getHeader(self::HEADER), 7)
                );

                return $this->callRequest($url, $type, $body, $tries + 1);
            }

            throw $e;
        }
    }
}
