<?php

declare(strict_types=1);

namespace Modules\Api\Exceptions;

use Amp\Http\Client\Request;
use Exception;

final class ResponseStatusException extends Exception
{
    public readonly Request $request;

    public function __construct(
        string $message,
        int $code = 0,
        Request $request
    ) {
        parent::__construct($message, $code);
        $this->request = $request;
    }
}
