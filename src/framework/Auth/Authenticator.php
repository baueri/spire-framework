<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Auth;

use Baueri\Spire\Framework\Support\Password;

abstract class Authenticator
{
    protected ?AuthUser $user = null;

    abstract public function login(string $email, string $password): ?AuthUser;

    abstract public function authenticateBySession(): void;

    abstract public function logout(): void;

    abstract public function user(): ?AuthUser;

    public function setUser(AuthUser $user): void
    {
        $this->user = $user;
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return Password::verify($password, $hash);
    }
}
