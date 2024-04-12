<?php

namespace Baueri\Spire\Framework\Middleware;

interface Middleware
{
    public function handle(): void;
}
