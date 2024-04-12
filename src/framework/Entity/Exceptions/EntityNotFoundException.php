<?php

namespace Baueri\Spire\Framework\Entity\Exceptions;

use Throwable;

class EntityNotFoundException extends QueryBuilderException
{
    public function __construct($message = "", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
