<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http;

use Baueri\Spire\Framework\Traits\EnumTrait;

enum RequestMethod
{
    use EnumTrait;

    case GET;
    case HEAD;
    case POST;
    case PUT;
    case PATCH;
    case DELETE;
    case OPTIONS;
    case ALL;

    public function is(array|self $method): bool
    {
        if (is_array($method)) {
            foreach ($method as $m) {
                if ($this->is($m)) {
                    return true;
                }
            }
            return false;
        }

        return $method->name === $this->name;
    }
}
