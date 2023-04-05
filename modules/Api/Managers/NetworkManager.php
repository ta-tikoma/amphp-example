<?php

declare(strict_types=1);

namespace Modules\Api\Managers;

use Amp\Http\Client\HttpClient;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use Modules\Api\Exceptions\ResponseStatusException;

/**
 * Обертка над HTTP клиентом
 */
class NetworkManager
{
    private const MAX_TRIES = 3;

    private HttpClient $client;

    public function __construct(
        private string $host
    ) {
        $this->client = HttpClientBuilder::buildDefault();
    }

    protected function buildRequest(string $url, string $type = 'GET', string $body = ''): Request
    {
        $request = new Request(
            "{$this->host}{$url}",
            $type,
            $body
        );

        $request->setHeader('Accept', 'application/json');
        $request->setHeader('Content-Type', 'application/json');

        return $request;
    }

    protected function callRequest(
        string $url,
        string $type = 'GET',
        string $body = '',
        int $tries = 0
    ): Response {
        $request = $this->buildRequest($url, $type, $body);
        if ($tries >= self::MAX_TRIES) {
            throw new ResponseStatusException(
                "Too many tries, url: {$request->getUri()}",
                0,
                $request
            );
        }

        $response = $this->client->request($request);
        $status = $response->getStatus();
        print_r("url:{$request->getUri()} status:{$status}\n");
        if ($status === 200) {
            return $response;
        }

        throw new ResponseStatusException(
            "Bad response status: {$status}, url: {$request->getUri()}",
            $status,
            $request
        );
    }

    public function get(string $url): array
    {
        $response = $this->callRequest($url);
        $buffer = $response->getBody()->buffer();
        $json = json_decode($buffer, true);

        return $json;
    }

    public function post(string $url, array $data): array
    {
        $response = $this->callRequest($url, 'POST', json_encode($data));
        $buffer = $response->getBody()->buffer();
        $json = json_decode($buffer, true);

        return $json;
    }
}
