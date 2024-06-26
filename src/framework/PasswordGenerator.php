<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework;

use InvalidArgumentException;

class PasswordGenerator
{
    public const LETTERS = 'abcdefghijklmnopqrstuvwxyz';
    public const NUMBERS = '0123456789';

    public const OPTION_LOWER = 'LOWER';
    public const OPTION_UPPER = 'UPPER';
    public const OPTION_NUMBERS = 'NUMBER';

    private array $settings = [
        self::OPTION_LOWER => true,
        self::OPTION_UPPER => true,
        self::OPTION_NUMBERS => true,
    ];

    private int $length;

    public function __construct(null|int $length = null, array $settings = [])
    {
        $length ??= 8;
        if (is_numeric($length) && $length <= 0) {
            throw new InvalidArgumentException('Please provide a valid number!');
        }

        if ($settings) {
            $this->settings = $settings;
        }

        $this->length = $length;
    }

    public function generate(): string
    {
        $pattern = $this->settings[self::OPTION_LOWER] ? self::LETTERS : '';
        if ($this->settings[self::OPTION_UPPER]) {
            $pattern .= strtoupper(static::LETTERS);
        }
        if ($this->settings[self::OPTION_NUMBERS]) {
            $pattern .= self::NUMBERS;
        }

        $pwd = '';
        $patternArr = str_split($pattern);
        for ($i = 0; $i < $this->length; $i++) {
            $pwd .= $patternArr[array_rand($patternArr)];
        }
        return $pwd;
    }

    public function setOpt($option, $value): PasswordGenerator
    {
        if (!isset($this->settings[$option])) {
            throw new InvalidArgumentException('Invalid password option: ' . $option);
        }
        if (!is_bool($value)) {
            throw new InvalidArgumentException('Invalid password option value');
        }

        $this->settings[$option] = $value;
        return $this;
    }

    public function useLower(bool $use): PasswordGenerator
    {
        $this->setOpt(self::OPTION_LOWER, $use);
        return $this;
    }

    public function useUpper(bool $use): PasswordGenerator
    {
        $this->setOpt(self::OPTION_UPPER, $use);
        return $this;
    }

    public function useNumbers(bool $use): PasswordGenerator
    {
        $this->setOpt(self::OPTION_NUMBERS, $use);
        return $this;
    }

    public function __toString()
    {
        return $this->generate();
    }
}
