<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Entity\Relation;

enum Has
{
    case many;
    case one;
}
