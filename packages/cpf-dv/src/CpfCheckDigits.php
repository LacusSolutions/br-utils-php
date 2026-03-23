<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf;

use InvalidArgumentException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputInvalidException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputLengthException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputTypeError;

/**
 * Calculates and exposes CPF check digits from a valid base input. Validates
 * length and rejects repeated-digit sequences.
 *
 * @property-read string $first  First check digit (10th digit of the full CPF).
 * @property-read string $second Second check digit (11th digit of the full CPF).
 * @property-read string $both   Both check digits concatenated (10th and 11th digits).
 * @property-read string $cpf    Full 11-digit CPF (base 9 digits concatenated with the 2 check digits).
 */
class CpfCheckDigits
{
    /** Minimum number of digits required for the CPF check digits calculation. */
    public const CPF_MIN_LENGTH = CPF_MIN_LENGTH;

    /** Maximum number of digits accepted as input for the CPF check digits calculation. */
    public const CPF_MAX_LENGTH = CPF_MAX_LENGTH;

    /** @var list<int> */
    private array $cpfDigits;
    private ?int $cachedFirstDigit = null;
    private ?int $cachedSecondDigit = null;

    /**
     * Creates a calculator for the given CPF base (9 to 11 digits).
     *
     * @param string|list<string> $cpfInput digits with or without formatting, or array of strings
     *
     * @throws CpfCheckDigitsInputTypeError When input is not a string or string[].
     * @throws CpfCheckDigitsInputLengthException When digit count is not between 9 and 11.
     * @throws CpfCheckDigitsInputInvalidException When all digits are the same
     *   (repeated digits, e.g. 777.777.777-...).
     */
    public function __construct(mixed $cpfInput)
    {
        $parsed = $this->parseInput($cpfInput);

        $this->validateLength($parsed, $cpfInput);
        $this->validateNonRepeatedDigits($parsed, $cpfInput);

        $this->cpfDigits = array_slice($parsed, 0, self::CPF_MIN_LENGTH);
    }

    /**
     * Property-style access to match JS API:
     * - $cpfCheckDigits->first
     * - $cpfCheckDigits->second
     * - $cpfCheckDigits->both
     * - $cpfCheckDigits->cpf
     */
    public function __get(string $name): string
    {
        return match ($name) {
            'first' => $this->getFirst(),
            'second' => $this->getSecond(),
            'both' => $this->getBoth(),
            'cpf' => $this->getCpf(),
            default => throw new InvalidArgumentException("Unknown property: {$name}"),
        };
    }

    /**
     * First check digit (10th digit of the full CPF).
     */
    private function getFirst(): string
    {
        if ($this->cachedFirstDigit === null) {
            $sequence = [...$this->cpfDigits];
            $this->cachedFirstDigit = $this->calculate($sequence);
        }

        return (string) $this->cachedFirstDigit;
    }

    /**
     * Second check digit (11th digit of the full CPF).
     */
    private function getSecond(): string
    {
        if ($this->cachedSecondDigit === null) {
            $sequence = [...$this->cpfDigits, (int) $this->getFirst()];
            $this->cachedSecondDigit = $this->calculate($sequence);
        }

        return (string) $this->cachedSecondDigit;
    }

    /**
     * Both check digits concatenated (10th and 11th digits).
     */
    private function getBoth(): string
    {
        return $this->getFirst() . $this->getSecond();
    }

    /**
     * Full 11-digit CPF (base 9 digits concatenated with the 2 check digits).
     */
    private function getCpf(): string
    {
        return implode('', $this->cpfDigits) . $this->getBoth();
    }

    /**
     * Parses a string or an array of strings into an array of integers.
     *
     * @param string|list<string> $cpfInput
     * @return list<int>
     *
     * @throws CpfCheckDigitsInputTypeError When input is not a string or string[].
     */
    private function parseInput(mixed $cpfInput): array
    {
        if (is_string($cpfInput)) {
            return $this->parseStringInput($cpfInput);
        }

        if (is_array($cpfInput)) {
            return $this->parseArrayInput($cpfInput);
        }

        throw new CpfCheckDigitsInputTypeError($cpfInput, 'string or string[]');
    }

    /**
     * Parses a string into an array of integers.
     *
     * @return list<int>
     */
    private function parseStringInput(string $cpfString): array
    {
        $digitsOnly = preg_replace('/\D/', '', $cpfString) ?? '';
        $chars = str_split($digitsOnly, 1);

        return array_map('intval', $chars);
    }

    /**
     * Parses an array into an array of integers.
     *
     * @param list<string> $cpfArray
     * @return list<int>
     *
     * @throws CpfCheckDigitsInputTypeError When input is not a string or string[].
     */
    private function parseArrayInput(array $cpfArray): array
    {
        if ($cpfArray === []) {
            return [];
        }

        foreach ($cpfArray as $item) {
            if (!is_string($item)) {
                throw new CpfCheckDigitsInputTypeError($cpfArray, 'string or string[]');
            }
        }

        return $this->parseStringInput(implode('', $cpfArray));
    }

    /**
     * Ensures digit count is between CPF_MIN_LENGTH and CPF_MAX_LENGTH.
     *
     * @param list<int> $digits
     * @param string|list<string> $originalInput
     */
    private function validateLength(array $digits, string|array $originalInput): void
    {
        $count = count($digits);

        if ($count < self::CPF_MIN_LENGTH || $count > self::CPF_MAX_LENGTH) {
            $evaluated = implode('', $digits);

            throw new CpfCheckDigitsInputLengthException(
                $originalInput,
                $evaluated,
                self::CPF_MIN_LENGTH,
                self::CPF_MAX_LENGTH,
            );
        }
    }

    /**
     * Rejects inputs where all first 9 digits are the same.
     *
     * @param list<int> $digits
     * @param string|list<string> $originalInput
     */
    private function validateNonRepeatedDigits(array $digits, string|array $originalInput): void
    {
        $firstNine = array_slice($digits, 0, self::CPF_MIN_LENGTH);
        $unique = array_unique($firstNine);

        if (count($unique) === 1) {
            throw new CpfCheckDigitsInputInvalidException(
                $originalInput,
                'Repeated digits are not considered valid.',
            );
        }
    }

    /**
     * Computes a single check digit using the standard CPF modulo-11 algorithm.
     *
     * @param list<int> $cpfSequence
     */
    protected function calculate(array $cpfSequence): int
    {
        $factor = count($cpfSequence) + 1;
        $sumResult = 0;

        foreach ($cpfSequence as $num) {
            $sumResult += $num * $factor;
            $factor -= 1;
        }

        $remainder = 11 - ($sumResult % 11);

        return $remainder > 9 ? 0 : $remainder;
    }
}
