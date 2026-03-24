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
 * @property-read string $second Second check digit (14th character of the full CNPJ).
 * @property-read string $both   Both check digits concatenated (13th and 14th characters).
 * @property-read string $cnpj   Full 14-character CNPJ (base 12 characters concatenated with the 2 check digits).
 */
class CnpjCheckDigits
{
    /** Minimum number of characters required for the CNPJ check digits calculation. */
    public const CNPJ_MIN_LENGTH = CNPJ_MIN_LENGTH;

    /** Maximum number of characters accepted as input for the CNPJ check digits calculation. */
    public const CNPJ_MAX_LENGTH = CNPJ_MAX_LENGTH;


    /** @var list<string> */
    private array $cnpjChars;
    private ?int $cachedFirstDigit = null;
    private ?int $cachedSecondDigit = null;

    /**
     * Creates a calculator for the given CNPJ base (12 to 14 characters).
     *
     * @param string|list<string> $cnpjInput Alphanumeric CNPJ with or without formatting, or array of strings
     *
     * @throws CnpjCheckDigitsInputTypeError When input is not a string or string[].
     * @throws CnpjCheckDigitsInputLengthException When character count is not between 12 and 14.
     * @throws CnpjCheckDigitsInputInvalidException When base ID is all zero (`00.000.000`), branch ID is all zero
     *   (`0000`) or all digits are the same (repeated digits, e.g. `77.777.777/7777-...`).
     */
    public function __construct(mixed $cnpjInput)
    {
        $parsed = $this->parseInput($cnpjInput);

        $this->validateLength($parsed, $cnpjInput);
        $this->validateBaseId($parsed, $cnpjInput);
        $this->validateBranchId($parsed, $cnpjInput);
        $this->validateNonRepeatedDigits($parsed, $cnpjInput);

        $this->cnpjChars = array_slice($parsed, 0, self::CNPJ_MIN_LENGTH);
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
            'first' => $this->getFirst(),
            'second' => $this->getSecond(),
            'both' => $this->getBoth(),
            'cnpj' => $this->getCnpj(),
            default => throw new InvalidArgumentException("Unknown property: {$name}"),
        };
    }

    /**
     * First check digit (13th character of the full CNPJ).
     */
    private function getFirst(): string
    {
        if ($this->cachedFirstDigit === null) {
            $baseCharsSequence = [...$this->cnpjChars];
            $this->cachedFirstDigit = $this->calculate($baseCharsSequence);
        }

        return (string) $this->cachedFirstDigit;
    }

    /**
     * Second check digit (14th character of the full CNPJ).
     */
    private function getSecond(): string
    {
        if ($this->cachedSecondDigit === null) {
            $sequence = [...$this->cnpjChars, $this->getFirst()];
            $this->cachedSecondDigit = $this->calculate($sequence);
        }

        return (string) $this->cachedSecondDigit;
    }

    /**
     * Both check digits concatenated (13th and 14th characters).
     */
    private function getBoth(): string
    {
        return $this->getFirst() . $this->getSecond();
    }

    /**
     * Full 14-character CNPJ (base 12 characters concatenated with the 2 check digits).
     */
    private function getCnpj(): string
    {
        return implode('', $this->cnpjChars) . $this->getBoth();
    }

    /**
     * Parses a string or an array of strings into an array of alphanumeric characters.
     *
     * @param string|list<string> $cnpjInput
     * @return list<string>
     *
     * @throws CnpjCheckDigitsInputTypeError When input is not a string or string[].
     */
    private function parseInput(mixed $cnpjInput): array
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
     * Parses a string into an array of alphanumeric characters.
     *
     * @return list<string>
     */
    private function parseStringInput(string $cnpjString): array
    {
        $alphanumericOnly = preg_replace('/[^0-9A-Z]/i', '', $cnpjString) ?? '';
        $alphanumericUpper = strtoupper($alphanumericOnly);
        $alphanumericArray = str_split($alphanumericUpper, 1);

        return $alphanumericArray;
    }

    /**
     * Parses an array into an array of alphanumeric characters.
     *
     * @param list<string> $cnpjArray
     * @return list<string>
     *
     * @throws CnpjCheckDigitsInputTypeError When input is not a string or string[].
     */
    private function parseArrayInput(array $cnpjArray): array
    {
        if ($cnpjArray === []) {
            return [];
        }

        foreach ($cnpjArray as $item) {
            if (!is_string($item)) {
                throw new CnpjCheckDigitsInputTypeError($cnpjArray, 'string or string[]');
            }
        }

        return $this->parseStringInput(implode('', $cnpjArray));
    }

    /**
     * Ensures character count is between 12 and 14.
     *
     * @param list<string> $cnpjChars
     * @param string|list<string> $originalInput
     */
    private function validateLength(array $cnpjChars, string|array $originalInput): void
    {
        $count = count($cnpjChars);

        if ($count < self::CNPJ_MIN_LENGTH || $count > self::CNPJ_MAX_LENGTH) {
            throw new CnpjCheckDigitsInputLengthException(
                $originalInput,
                implode('', $cnpjChars),
                self::CNPJ_MIN_LENGTH,
                self::CNPJ_MAX_LENGTH,
            );
        }
    }

    /**
     * @param list<string> $cnpjIntArray
     * @param string|list<string> $originalInput
     *
     * @throws CnpjCheckDigitsInputInvalidException When base ID is all zeros.
     *   (`00.000.000`).
     */
    private function validateBaseId(array $cnpjIntArray, string|array $originalInput): void
    {
        $cnpjBaseIdArray = array_slice($cnpjIntArray, 0, CNPJ_BASE_ID_LAST_INDEX + 1);
        $cnpjBaseIdString = implode('', $cnpjBaseIdArray);

        if ($cnpjBaseIdString === CNPJ_INVALID_BASE_ID) {
            throw new CnpjCheckDigitsInputInvalidException(
                $originalInput,
                'Base ID "'.CNPJ_INVALID_BASE_ID.'" is not eligible.',
            );
        }
    }

    /**
     * @param list<string> $cnpjIntArray
     * @param string|list<string> $originalInput
     *
     * @throws CnpjCheckDigitsInputInvalidException When branch ID is all zeros.
     *   (`0000`).
     */
    private function validateBranchId(array $cnpjIntArray, string|array $originalInput): void
    {
        $cnpjBranchIdArray = array_slice($cnpjIntArray, CNPJ_BASE_ID_LENGTH, CNPJ_BRANCH_ID_LENGTH);
        $cnpjBranchIdString = implode('', $cnpjBranchIdArray);

        if ($cnpjBranchIdString === CNPJ_INVALID_BRANCH_ID) {
            throw new CnpjCheckDigitsInputInvalidException(
                $originalInput,
                'Branch ID "'.CNPJ_INVALID_BRANCH_ID.'" is not eligible.',
            );
        }
    }

    /**
     * @param list<string> $cnpjIntArray
     * @param string|list<string> $originalInput
     *
     * @throws CnpjCheckDigitsInputInvalidException When all digits are numeric
     *   and the same (repeated digits, e.g. `77.777.777/7777-...`).
     */
    private function validateNonRepeatedDigits(array $cnpjIntArray, string|array $originalInput): void
    {
        $firstTwelve = array_slice($cnpjIntArray, 0, self::CNPJ_MIN_LENGTH);
        $unique = array_unique($firstTwelve);

        if (count($unique) === 1 && preg_match('/^\d$/', $firstTwelve[0] ?? '') === 1) {
            throw new CnpjCheckDigitsInputInvalidException(
                $originalInput,
                'Repeated digits are not considered valid.',
            );
        }
    }

    /**
     * Computes a single check digit using the standard CNPJ modulo-11 algorithm.
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
