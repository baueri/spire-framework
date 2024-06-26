<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Enums;

enum Environment
{
    case production;
    case demo;
    case local;
    case test;
}
