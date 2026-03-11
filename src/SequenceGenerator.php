<?php

declare(strict_types=1);

namespace Lacus\Utils;

/**
 * Class SequenceGenerator
 *
 * Utility for generating random character sequences of specified type and length.
 *
 * Supported sequence types:
 *  - Numeric (0-9)
 *  - Alphabetic (A-Z)
 *  - Alphanumeric (0-9, A-Z)
 */
final class SequenceGenerator
{
    private const NUMERIC = '0123456789';
    private const ALPHABETIC = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const ALPHANUMERIC = self::NUMERIC . self::ALPHABETIC;

    /**
     * Generates random character sequences of a given length and type (mirrors JS generateRandomSequence).
     *
     * @example
     *   SequenceGenerator::generate(10, SequenceType::Numeric);       // e.g. '9956000611'
     *   SequenceGenerator::generate(6, SequenceType::Alphabetic);    // e.g. 'AXQMZB'
     *   SequenceGenerator::generate(8, SequenceType::Alphanumeric);  // e.g. '8ZFB2K09'
     */
    public static function generate(int $size, SequenceType $type): string
    {
        $chars = match ($type) {
            SequenceType::Numeric => self::NUMERIC,
            SequenceType::Alphabetic => self::ALPHABETIC,
            SequenceType::Alphanumeric => self::ALPHANUMERIC,
        };
        $length = strlen($chars);
        $result = '';

        for ($i = 0; $i < $size; $i++) {
            $result .= $chars[random_int(0, $length - 1)];
        }

        return $result;
    }

    /**
     * Generates a random numeric (0-9) sequence of a given length.
     */
    public static function generateNumeric(int $size): string
    {
        return self::generate($size, SequenceType::Numeric);
    }

    /**
     * Generates a random alphabetic (A-Z) sequence of a given length.
     */
    public static function generateAlphabetic(int $size): string
    {
        return self::generate($size, SequenceType::Alphabetic);
    }

    /**
     * Generates a random alphanumeric (0-9A-Z) sequence of a given length.
     */
    public static function generateAlphanumeric(int $size): string
    {
        return self::generate($size, SequenceType::Alphanumeric);
    }
}
