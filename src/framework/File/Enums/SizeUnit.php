<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\File\Enums;

enum SizeUnit
{
    case B;
    case KB;
    case MB;
    case GB;
    case TB;

    public static function getSizeUnits(): array
    {
        return [
            self::KB->name => 1,
            self::MB->name => 2,
            self::GB->name => 3,
            self::TB->name => 4
        ];
    }
}
