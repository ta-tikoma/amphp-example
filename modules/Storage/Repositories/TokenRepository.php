<?php

declare(strict_types=1);

namespace Modules\Storage\Repositories;

use Modules\Storage\Dto\TokensDto;

final class TokenRepository
{
    private const FILE = 'tokens.data';

    private function getPath(): string
    {
        return __DIR__ . '/../../../' . self::FILE;
    }

    public function get(): TokensDto|null
    {
        $path = $this->getPath();
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if ($content !== false) {
                [$accessToken, $refreshToken] = explode("\n", $content);

                return new TokensDto($accessToken, $refreshToken);
            }
        }

        return null;
    }

    public function set(TokensDto $tokens): void
    {
        file_put_contents(
            $this->getPath(),
            implode("\n", [$tokens->access_token, $tokens->refresh_token])
        );
    }
}
