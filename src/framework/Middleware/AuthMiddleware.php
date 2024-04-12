<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Middleware;

use Baueri\Spire\Framework\Auth\Authenticator;

class AuthMiddleware implements Middleware
{
    public function __construct(
        private readonly Authenticator $service
    ) {
    }

    public function handle(): void
    {
        $this->service->authenticateBySession();
    }
}
