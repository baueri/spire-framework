<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests;

use Baueri\Spire\Framework\Bootstrapper;
use PHPUnit\Framework\TestCase;
use Baueri\Spire\Framework\Application;

class ApplicationTest extends TestCase
{
    public function testBootClass(): void
    {
        $bootstrapMock = $this->createPartialMock(Bootstrapper::class, ['boot']);
        $bootstrapMock->expects($this->once())->method('boot');

        $app = $this->createApplication();

        $app->bootWith($bootstrapMock);

        $app->boot();
    }

    public function testBootClassWithMultipleBootstrappers(): void
    {
        $bootstrapMock1 = $this->createPartialMock(Bootstrapper::class, ['boot']);
        $bootstrapMock1->expects($this->once())->method('boot');

        $bootstrapMock2 = $this->createPartialMock(Bootstrapper::class, ['boot']);
        $bootstrapMock2->expects($this->once())->method('boot');

        $app = $this->createApplication();

        $app->bootWith([$bootstrapMock1, $bootstrapMock2]);

        $app->boot();
    }

    public function testBootCallable(): void
    {
        $booted = false;
        $app = $this->createApplication();

        $app->bootWith(function () use (&$booted) {
            $booted = true;
        });

        $app->boot();

        $this->assertTrue($booted);
    }

    public function testGetPathReturnsCorrectPath(): void
    {
        $app = Application::create(__DIR__ . '/..');
        $this->assertSame(__DIR__ . '/..', $app->root);
    }

    protected function createApplication(): Application
    {
        return Application::create(__DIR__ . '/..');
    }
}
