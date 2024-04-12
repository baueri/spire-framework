<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Exception;

use Throwable;

class UnauthorizedException extends \Exception
{
    public function __construct($message = "", $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
