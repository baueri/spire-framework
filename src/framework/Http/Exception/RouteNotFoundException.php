<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http\Exception;

use Exception;
use Throwable;

class RouteNotFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
