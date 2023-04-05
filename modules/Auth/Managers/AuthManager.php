<?php

declare(strict_types=1);

namespace Modules\Auth\Managers;

use Amp\Sync\LocalMutex;
use Amp\Sync\LocalParcel;
use Modules\Api\Clients\ApiGuestClient;
use Modules\Auth\Dto\Tokens;
use Modules\Auth\Enums\TokensStatus;
use Modules\Storage\Dto\TokensDto;
use Modules\Storage\Repositories\TokenRepository;

final class AuthManager
{
    private LocalParcel $parcel;

    public function __construct(
        private ApiGuestClient $apiGuestClient,
        private TokenRepository $tokenRepository,
    ) {
        $this->parcel = new LocalParcel(new LocalMutex, null);
    }

    /**
     * Помечаем токен невалидным
     */
    public function invalidToken(string $accessToken): void
    {
        $this->parcel->synchronized(function (Tokens $tokens) use ($accessToken): Tokens {
            if ($tokens->access_token === $accessToken) {
                if ($tokens->status === TokensStatus::GOOD) {
                    return new Tokens($tokens->access_token, $tokens->refresh_token, TokensStatus::OLD);
                }
            }

            return $tokens;
        });
    }

    /**
     * Получение токена
     */
    public function getAccessToken(): string
    {
        $tokens = $this->parcel->synchronized(function (Tokens|null $tokens): Tokens {
            // если значений нет проверим их в хранилище
            if ($tokens === null) {
                $value = $this->tokenRepository->get();
                // если токенов в хранилище нет то следует авторизоваться чтоб их получить
                if ($value === null) {
                    $response = $this->apiGuestClient->login(
                        getenv('PHONE'),
                        getenv('PASSWORD'),
                    );

                    // кладем свежие в хранилище
                    $this->tokenRepository->set(new TokensDto(
                        $response->tokens->token,
                        $response->tokens->refresh_token
                    ));

                    return new Tokens($response->tokens->token, $response->tokens->refresh_token);
                }

                return new Tokens($value->access_token, $value->refresh_token);
            }

            // если токены устарели то обновляем их
            if ($tokens->status === TokensStatus::OLD) {
                $response = $this->apiGuestClient->refresh($tokens->refresh_token);

                // кладем свежие в хранилище
                $this->tokenRepository->set(new TokensDto(
                    $response->token,
                    $response->refresh_token
                ));

                return new Tokens($response->token, $response->refresh_token);
            }

            return $tokens;
        });

        return $tokens->access_token;
    }
}
