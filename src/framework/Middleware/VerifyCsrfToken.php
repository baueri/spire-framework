<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Middleware;

use Baueri\Spire\Framework\Http\Exception\TokenMismatchException;
use Baueri\Spire\Framework\Http\Request;
use Baueri\Spire\Framework\Http\RequestMethod;
use Baueri\Spire\Framework\Http\Session;

class VerifyCsrfToken implements Middleware
{
    protected array $except = [];

    public function __construct(
        private readonly Request $request
    ) {
        $this->except = config('app.exclude_csrf');
    }

    /**
     * @throws TokenMismatchException
     */
    public function handle(): void
    {
        if (
            $this->isReading() ||
            $this->isExcepted() ||
            $this->tokenMatches()
        ) {
            return;
        }
        throw new TokenMismatchException('csrf token mismatch');
    }

    private function isExcepted(): bool
    {
        if (in_array($this->request->uri, $this->except)) {
            return true;
        }
        foreach ($this->except as $except) {
            $mask = preg_quote($this->request->route->getUriMask());
            if ($this->request->route->getAs() === $except || preg_match("#{$mask}#", $except)) {
                return true;
            }
        }

        return false;
    }

    private function isReading(): bool
    {
        return $this->request->requestMethod->is([RequestMethod::GET, RequestMethod::HEAD, RequestMethod::OPTIONS]);
    }

    private function tokenMatches(): bool
    {
        $token = $this->request->token();
        return $token && Session::token() && $token === Session::token();
    }
}
