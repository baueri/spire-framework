<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Middleware;

use Baueri\Spire\Framework\Auth\BaseAuth;
use Baueri\Spire\Framework\Exception\UnauthorizedException;

class BaseAuthMiddleware implements Middleware
{
    private BaseAuth $auth;

    public function __construct(BaseAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @throws UnauthorizedException
     */
    public function handle(): void
    {
        if (!config('app.base_auth')) {
            return;
        }

        $this->auth->authenticate(
            get_site_url() . ' Basic Authentication',
            config('app.base_auth.user'),
            config('app.base_auth.password')
        );
    }
}
