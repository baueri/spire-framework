<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Mail;

interface Recipient
{
    public function email(): string;

    public function name(): string;
}
