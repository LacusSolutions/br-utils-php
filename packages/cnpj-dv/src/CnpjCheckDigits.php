<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj;

use InvalidArgumentException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputLengthException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputTypeError;

const CNPJ_BASE_ID_LENGTH = 8;
const CNPJ_BASE_ID_LAST_INDEX = CNPJ_BASE_ID_LENGTH - 1;
const CNPJ_INVALID_BASE_ID = '00000000';

const CNPJ_BRANCH_ID_LENGTH = 4;
const CNPJ_INVALID_BRANCH_ID = '0000';

const DELTA_FACTOR = 48; // ord('0')

/**
 * Calculates and exposes CNPJ check digits from a valid base input. Validates
 * length, base ID, branch ID and rejects repeated-character sequences.
 *
 * @property-read string $first  First check digit (13th character of the full CNPJ).
 * @property-read string $second Second check digit (14th character of the full
 *     CNPJ).
 * @property-read string $both   Both check digits concatenated (13th and 14th
 *     characters).
 * @property-read string $cnpj   Full 14-character CNPJ (base 12 characters
 *     concatenated with the 2 check digits).
 */
class CnpjCheckDigits
{
    /**
     * Minimum number of characters required for the CNPJ check digits
     * calculation.
     */
    public const CNPJ_MIN_LENGTH = CNPJ_MIN_LENGTH;

    /**
     * Maximum number of characters accepted as input for the CNPJ check digits
     * calculation.
     */
    public const CNPJ_MAX_LENGTH = CNPJ_MAX_LENGTH;

    /** Normalized 12-character base (uppercase alphanumeric). */
    private string $cnpjBase;
    private ?int $cachedFirstDigit = null;
    private ?int $cachedSecondDigit = null;
    private ?string $cachedBothDigits = null;
    private ?string $cachedWholeCnpj = null;

    /**
     * Creates a calculator for the given CNPJ base (12 to 14 characters).
     *
     * @param string|list<string> $cnpjInput Alphanumeric CNPJ with or without
     *     formatting, or array of strings
     *
     * @throws CnpjCheckDigitsInputTypeError When input is not a string or
     *     string[].
     * @throws CnpjCheckDigitsInputLengthException When character count is not
     *     between 12 and 14.
     * @throws CnpjCheckDigitsInputInvalidException When base ID is all zero
     *     (`00.000.000`), branch ID is all zero (`0000`) or all digits are the
     *     same (repeated digits, e.g. `77.777.777/7777-...`).
     */
    public function __construct(mixed $cnpjInput)
    {
        $parsed = $this->parseInput($cnpjInput);

        $this->validateLength($parsed, $cnpjInput);
        $this->validateBaseId($parsed, $cnpjInput);
        $this->validateBranchId($parsed, $cnpjInput);
        $this->validateNonRepeatedDigits($parsed, $cnpjInput);

        $this->cnpjBase = substr($parsed, 0, self::CNPJ_MIN_LENGTH);
    }

    /**
     * Property-style access to match JS API:
     * - $cnpjCheckDigits->first
     * - $cnpjCheckDigits->second
     * - $cnpjCheckDigits->both
     * - $cnpjCheckDigits->cnpj
     */
    public function __get(string $name): string
    {
        return match ($name) {
            'first'  => $this->getFirst(),
            'second' => $this->getSecond(),
            'both'   => $this->getBoth(),
            'cnpj'   => $this->getCnpj(),
            default  => throw new InvalidArgumentException("Unknown property: {$name}"),
        };
    }

    /**
     * First check digit (13th character of the full CNPJ).
     */
    private function getFirst(): string
    {
        if ($this->cachedFirstDigit === null) {
            $sequence = str_split($this->cnpjBase, 1);
            $this->cachedFirstDigit = $this->calculate($sequence);
        }

        return (string) $this->cachedFirstDigit;
    }

    /**
     * Second check digit (14th character of the full CNPJ).
     */
    private function getSecond(): string
    {
        if ($this->cachedSecondDigit === null) {
            $sequence = [...str_split($this->cnpjBase, 1), $this->getFirst()];
            $this->cachedSecondDigit = $this->calculate($sequence);
        }

        return (string) $this->cachedSecondDigit;
    }

    /**
     * Both check digits concatenated (13th and 14th characters).
     */
    private function getBoth(): string
    {
        if ($this->cachedBothDigits === null) {
            $this->cachedBothDigits = $this->getFirst() . $this->getSecond();
        }

        return $this->cachedBothDigits;
    }

    /**
     * Full 14-character CNPJ (base 12 characters concatenated with the 2 check digits).
     */
    private function getCnpj(): string
    {
        if ($this->cachedWholeCnpj === null) {
            $this->cachedWholeCnpj = $this->cnpjBase . $this->getBoth();
        }

        return $this->cachedWholeCnpj;
    }

    /**
     * Parses a string or an array of strings into a normalized uppercase
     * alphanumeric string.
     *
     * @param string|list<string> $cnpjInput
     *
     * @throws CnpjCheckDigitsInputTypeError When input is not a string or
     *     string[].
     */
    private function parseInput(mixed $cnpjInput): string
    {
        if (is_string($cnpjInput)) {
            return $this->parseStringInput($cnpjInput);
        }

        if (is_array($cnpjInput)) {
            return $this->parseArrayInput($cnpjInput);
        }

        throw new CnpjCheckDigitsInputTypeError($cnpjInput, 'string or string[]');
    }

    /**
     * Strips non-alphanumeric characters and uppercases the remaining ones.
     */
    private function parseStringInput(string $cnpjString): string
    {
        $alphanumericUpper = strtoupper($cnpjString);
        $alphanumericOnly = preg_replace('/[^0-9A-Z]/i', '', $alphanumericUpper);

        return $alphanumericOnly ?? '';
    }

    /**
     * Concatenates an array of strings (validating element types) and
     * normalizes the result via {@see parseStringInput()}.
     *
     * @param list<string> $cnpjArray
     *
     * @throws CnpjCheckDigitsInputTypeError When input is not a string or string[].
     */
    private function parseArrayInput(array $cnpjArray): string
    {
        $buffer = '';

        if ($cnpjArray === []) {
            return $buffer;
        }

        foreach ($cnpjArray as $item) {
            if (!is_string($item)) {
                throw new CnpjCheckDigitsInputTypeError($cnpjArray, 'string or string[]');
            }

            $buffer .= $item;
        }

        return $this->parseStringInput($buffer);
    }

    /**
     * Ensures character count is between 12 and 14.
     *
     * @param string|list<string> $originalInput
     */
    private function validateLength(string $parsed, string|array $originalInput): void
    {
        $length = strlen($parsed);

        if ($length < self::CNPJ_MIN_LENGTH || $length > self::CNPJ_MAX_LENGTH) {
            throw new CnpjCheckDigitsInputLengthException(
                $originalInput,
                $parsed,
                self::CNPJ_MIN_LENGTH,
                self::CNPJ_MAX_LENGTH,
            );
        }
    }

    /**
     * Ensures the base ID is not all zeros (`00.000.000`).
     *
     * @param string|list<string> $originalInput
     *
     * @throws CnpjCheckDigitsInputInvalidException When base ID is all zeros.
     *     (`00.000.000`).
     */
    private function validateBaseId(string $parsed, string|array $originalInput): void
    {
        if (str_starts_with($parsed, CNPJ_INVALID_BASE_ID)) {
            throw new CnpjCheckDigitsInputInvalidException(
                $originalInput,
                'Base ID "'.CNPJ_INVALID_BASE_ID.'" is not eligible.',
            );
        }
    }

    /**
     * Ensures the branch ID is not all zeros (`0000`).
     *
     * @param string|list<string> $originalInput
     *
     * @throws CnpjCheckDigitsInputInvalidException When branch ID is all
     *     zeros (`0000`).
     */
    private function validateBranchId(string $parsed, string|array $originalInput): void
    {
        $cnpjBranchId = substr($parsed, CNPJ_BASE_ID_LENGTH, CNPJ_BRANCH_ID_LENGTH);

        if ($cnpjBranchId === CNPJ_INVALID_BRANCH_ID) {
            throw new CnpjCheckDigitsInputInvalidException(
                $originalInput,
                'Branch ID "'.CNPJ_INVALID_BRANCH_ID.'" is not eligible.',
            );
        }
    }

    /**
     * Ensures the first 12 characters are not all the same digit.
     *
     * @param string|list<string> $originalInput
     *
     * @throws CnpjCheckDigitsInputInvalidException When all digits are numeric
     *     and the same (repeated digits, e.g. `77.777.777/7777-...`).
     */
    private function validateNonRepeatedDigits(string $parsed, string|array $originalInput): void
    {
        $cnpjBase = substr($parsed, 0, self::CNPJ_MIN_LENGTH);
        $cnpjFirstDigit = $cnpjBase[0];

        if ($cnpjFirstDigit >= '0' && $cnpjFirstDigit <= '9' && $cnpjBase === str_repeat($cnpjFirstDigit, self::CNPJ_MIN_LENGTH)) {
            throw new CnpjCheckDigitsInputInvalidException(
                $originalInput,
                'Repeated digits are not considered valid.',
            );
        }
    }

    /**
     * Computes a single check digit using the standard CNPJ modulo-11
     * algorithm.
     *
     * @param list<string> $cnpjSequence
     */
    protected function calculate(array $cnpjSequence): int
    {
        $factor = 2;
        $sumResult = 0;

        for ($i = count($cnpjSequence) - 1; $i >= 0; $i--) {
            $charValue = ord($cnpjSequence[$i]);
            $charValue = $charValue - DELTA_FACTOR;

            $sumResult += $charValue * $factor;
            $factor = $factor === 9 ? 2 : $factor + 1;
        }

        $remainder = $sumResult % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
