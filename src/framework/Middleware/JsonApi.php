<?php

namespace Baueri\Spire\Framework\Middleware;

use Baueri\Spire\Framework\Http\Response;

class JsonApi implements Middleware
{
    public function handle(): void
    {
        Response::asJson();
    }
}
