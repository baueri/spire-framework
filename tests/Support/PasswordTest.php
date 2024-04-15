<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests\Support;

use Baueri\Spire\Framework\Support\Password;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function testHash(): void
    {
        $password = 'password';
        $hash = Password::hash($password);

        $this->assertTrue(Password::verify($password, $hash));
    }

    public function testGenerate(): void
    {
        $password = Password::generate();

        $this->assertTrue(Password::verify($password->password, $password->hash));
    }
}
