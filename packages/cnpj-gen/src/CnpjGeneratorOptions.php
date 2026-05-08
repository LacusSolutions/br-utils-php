<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj;

use InvalidArgumentException;
use Lacus\BrUtils\Cnpj\Enums\CnpjType;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionPrefixInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionTypeInvalidException;

/**
 * Class to store the options for the CNPJ generator. This class provides a
 * centralized way to configure how CNPJ characters are generated, including
 * partial start string, formatting, and the type of characters to be generated
 * (numeric, alphabetic, or alphanumeric).
 *
 * @property bool $format
 * @property string $prefix
 * @property CnpjType $type
 */
class CnpjGeneratorOptions
{
    /**
     * The standard length of a CNPJ (Cadastro Nacional da Pessoa Jurídica)
     * identifier (14 alphanumeric characters).
     */
    public const CNPJ_LENGTH = 14;

    /**
     * Maximum length of the prefix (base ID and branch ID) of a CNPJ.
     */
    public const CNPJ_PREFIX_MAX_LENGTH = self::CNPJ_LENGTH - 2;

    private const CNPJ_BASE_ID_LENGTH = 8;
    private const CNPJ_BASE_ID_LAST_INDEX = self::CNPJ_BASE_ID_LENGTH - 1;
    private const ZEROED_CNPJ_BASE_ID = '00000000';

    private const CNPJ_BRANCH_ID_LENGTH = 4;
    private const CNPJ_BRANCH_ID_LAST_INDEX = self::CNPJ_BASE_ID_LAST_INDEX + self::CNPJ_BRANCH_ID_LENGTH;
    private const ZEROED_CNPJ_BRANCH_ID = '0000';

    /**
     * Default value for the `format` option. When `true`, the generated CNPJ
     * string will have the standard formatting (`00.000.000/0000-00`).
     */
    public const DEFAULT_FORMAT = false;

    /**
     * Default string used as the initial string of the generated CNPJ.
     */
    public const DEFAULT_PREFIX = '';

    /**
     * Default type of characters to generate for the CNPJ.
     */
    public const DEFAULT_TYPE = CnpjType::Alphanumeric;

    /**
     * @var array{
     *     format: bool,
     *     prefix: string,
     *     type: CnpjType,
     * }
     */
    private array $options = []; // @phpstan-ignore-line property.defaultValue

    /**
     * Creates a new instance of `CnpjGeneratorOptions`.
     *
     * Options can be provided in multiple ways:
     *
     * 1. As a single options object or another `CnpjGeneratorOptions` instance.
     * 2. As multiple override objects that are merged in order (later overrides take
     *    precedence)
     *
     * All options are optional and will default to their predefined values if not
     * provided.
     *
     * @param ?bool $format
     * @param ?string $prefix
     * @param ?(CnpjType|'alphanumeric'|'alphabetic'|'numeric') $type
     * @param list<CnpjGeneratorOptions|array{
     *     format?: bool|null,
     *     prefix?: string|null,
     *     type?: CnpjType|'alphanumeric'|'alphabetic'|'numeric'|null,
     * }|null> $overrides
     *
     * @throws CnpjGeneratorOptionPrefixInvalidException If the `prefix` option
     *   contains invalid combination of characters.
     * @throws CnpjGeneratorOptionsTypeError If any option has an invalid type.
     * @throws CnpjGeneratorOptionTypeInvalidException If the `type` option is
     *   not one of the allowed values.
     */
    public function __construct(
        $format = null,
        $prefix = null,
        $type = null,
        ?array $overrides = [],
    ) {
        $this->setFormat($format);
        $this->setPrefix($prefix);
        $this->setType($type);

        foreach (($overrides ?? []) as $override) {
            if ($override === null) {
                continue;
            }

            if ($override instanceof CnpjGeneratorOptions) {
                $this->set(...$override->getAll());
            } elseif (is_array($override)) {
                $this->set(
                    format: $override['format'] ?? null,
                    prefix: $override['prefix'] ?? null,
                    type: $override['type'] ?? null,
                );
            }
        }
    }

    /**
     * Property-style access to the options.
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'format' => $this->getFormat(),
            'prefix' => $this->getPrefix(),
            'type'   => $this->getType(),
            default  => throw new InvalidArgumentException("Unknown property: {$name}"),
        };
    }

    /**
     * Property-style mutation to the options.
     */
    public function __set(string $name, mixed $value): void
    {
        match ($name) {
            'format' => $this->setFormat($value),   // @phpstan-ignore-line argument.type
            'prefix' => $this->setPrefix($value),   // @phpstan-ignore-line argument.type
            'type'   => $this->setType($value),     // @phpstan-ignore-line argument.type
            default  => throw new InvalidArgumentException("Unknown property: {$name}"),
        };
    }

    /**
     * Returns a shallow copy of all current options. This is useful for creating
     * snapshots of the current configuration.
     *
     * @return array{
     *     format: bool,
     *     prefix: string,
     *     type: CnpjType,
     * }
     */
    public function getAll(): array
    {
        return [...$this->options];
    }

    /**
     * Gets whether the generated CNPJ string will have the standard formatting
     * (`00.000.000/0000-00`).
     */
    private function getFormat(): bool
    {
        return $this->options['format'];
    }

    /**
     * Sets whether the generated CNPJ string will have the standard formatting
     * (`00.000.000/0000-00`). The value is converted to a boolean using
     * `Boolean()`, so truthy/falsy values are handled appropriately.
     *
     * @param bool|null $value
     */
    private function setFormat($value): void
    {
        $actualFormat = $value ?? self::DEFAULT_FORMAT;
        $actualFormat = (bool) $actualFormat;

        $this->options['format'] = $actualFormat;
    }

    /**
     * Gets the string used as the initial string of the generated CNPJ.
     *
     * Note: If the evaluated prefix (after stripping non-alphanumeric characters)
     * is longer than 12 characters, the extra characters are ignored, because a
     * CNPJ has 12 base characters followed by 2 calculated check digits.
     */
    private function getPrefix(): string
    {
        return $this->options['prefix'];
    }

    /**
     * Sets the string used as the initial string of the generated CNPJ. Only
     * alphanumeric characters are kept and the rest is stripped. If provided,
     * only the missing characters are generated randomly. For example, if the
     * prefix `AAABBB` (6 characters) is given, only the next 8 characters are
     * randomly generated and concatenated to the prefix.
     *
     * Note: If the evaluated prefix (after stripping non-alphanumeric characters)
     * is longer than 12 characters, the extra characters are ignored, because a
     * CNPJ has 12 base characters followed by 2 calculated check digits.
     *
     * @param string|null $value
     *
     * @throws CnpjGeneratorOptionsTypeError If the value is not a string.
     * @throws CnpjGeneratorOptionPrefixInvalidException If the `prefix` option
     *   contains invalid combination of characters or is too long.
     */
    private function setPrefix($value): void
    {
        $actualPrefix = $value ?? self::DEFAULT_PREFIX;

        $this->assertIsString('prefix', $actualPrefix);

        /** @var string */
        $actualPrefix = preg_replace('/[^0-9A-Z]/i', '', $actualPrefix);
        $actualPrefix = strtoupper($actualPrefix);
        $actualPrefix = substr($actualPrefix, 0, self::CNPJ_PREFIX_MAX_LENGTH);

        $this->validatePrefixBaseId($actualPrefix);
        $this->validatePrefixBranchId($actualPrefix);
        $this->validatePrefixNonRepeatedDigits($actualPrefix);

        $this->options['prefix'] = $actualPrefix;
    }

    /**
     * Gets the type of characters to generate for the CNPJ.
     */
    private function getType(): CnpjType
    {
        return $this->options['type'];
    }

    /**
     * Sets the type of characters to generate for the CNPJ.
     *
     * The options are:
     *
     * - `alphabetic`: Generates a sequence of alphabetic characters (`A-Z`).
     * - `alphanumeric`: Generates a sequence of alphanumeric characters (`0-9A-Z`).
     * - `numeric`: Generates a sequence of numbers-only characters (`0-9`).
     *
     * @param CnpjType|'alphanumeric'|'alphabetic'|'numeric'|null $value
     *
     * @throws CnpjGeneratorOptionsTypeError If the value is not a string.
     * @throws CnpjGeneratorOptionTypeInvalidException If the value is not a
     *   valid type.
     */
    private function setType($value): void
    {
        $actualType = $value ?? self::DEFAULT_TYPE;
        $actualType = $this->parseCnpjType('type', $actualType);

        $this->options['type'] = $actualType;
    }

    /**
     * Sets multiple options at once. This method allows you to update multiple
     * options in a single call. Only the non-nullable provided options are
     * updated; options not included in the object or set to `null` retain their
     * current values.
     *
     * @param ?bool $format
     * @param ?string $prefix
     * @param ?(CnpjType|'alphanumeric'|'alphabetic'|'numeric') $type
     *
     * @throws CnpjGeneratorOptionPrefixInvalidException If the `prefix` option
     *   contains invalid combination of characters.
     * @throws CnpjGeneratorOptionsTypeError If any option has an invalid type.
     * @throws CnpjGeneratorOptionTypeInvalidException If the `type` option is
     *   not one of the allowed values.
     */
    public function set(
        $format = null,
        $prefix = null,
        $type = null,
    ): self {
        $this->setFormat($format ?? $this->getFormat());
        $this->setPrefix($prefix ?? $this->getPrefix());
        $this->setType($type ?? $this->getType());

        return $this;
    }

    /**
     * Throws if the given value is not a string.
     *
     * @param 'prefix'|'type' $optionName
     *
     * @throws CnpjGeneratorOptionsTypeError If the option value is not a string.
     */
    private function assertIsString(string $optionName, mixed $value): void
    {
        if (!is_string($value)) {
            throw new CnpjGeneratorOptionsTypeError($optionName, $value, 'string');
        }
    }

    /**
     * Throws if the given value is not a CnpjType.
     *
     * @param 'type' $optionName
     * @param CnpjType|string $value
     *
     * @throws CnpjGeneratorOptionsTypeError If the value is not a string.
     * @throws CnpjGeneratorOptionTypeInvalidException If the value is not a
     *   valid type.
     */
    private function parseCnpjType(string $optionName, mixed $value): CnpjType
    {
        if ($value instanceof CnpjType) {
            return $value;
        }

        if (is_string($value)) {
            $cnpjType = CnpjType::tryFrom($value);

            if ($cnpjType) {
                return $cnpjType;
            }

            throw new CnpjGeneratorOptionTypeInvalidException($value, CnpjType::values());
        }

        throw new CnpjGeneratorOptionsTypeError($optionName, $value, 'CnpjType or string');
    }

    /**
     * Throws if the prefix's first 8 characters (base ID) are all zeros.
     *
     * @throws CnpjGeneratorOptionPrefixInvalidException If the prefix's first 8
     *   characters are all zeros.
     */
    private function validatePrefixBaseId(string $partialCnpj): void
    {
        if (strlen($partialCnpj) < self::CNPJ_BASE_ID_LENGTH) {
            return;
        }

        $cnpjBaseIdString = substr($partialCnpj, 0, self::CNPJ_BASE_ID_LAST_INDEX + 1);

        if ($cnpjBaseIdString === self::ZEROED_CNPJ_BASE_ID) {
            throw new CnpjGeneratorOptionPrefixInvalidException(
                $partialCnpj,
                'Zeroed base ID is not eligible.',
            );
        }
    }

    /**
     * Throws if the prefix's characters at positions 9–12 (branch ID) are all
     * zeros.
     *
     * @throws CnpjGeneratorOptionPrefixInvalidException If the prefix's
     *   characters at positions 9–12 are all zeros.
     */
    private function validatePrefixBranchId(string $partialCnpj): void
    {
        if (strlen($partialCnpj) < self::CNPJ_BASE_ID_LENGTH + self::CNPJ_BRANCH_ID_LENGTH) {
            return;
        }

        $cnpjBranchIdString = substr(
            $partialCnpj,
            self::CNPJ_BASE_ID_LENGTH,
            self::CNPJ_BRANCH_ID_LAST_INDEX + 1,
        );

        if ($cnpjBranchIdString === self::ZEROED_CNPJ_BRANCH_ID) {
            throw new CnpjGeneratorOptionPrefixInvalidException(
                $partialCnpj,
                'Zeroed branch ID is not eligible.',
            );
        }
    }

    /**
     * Throws if the prefix has 12 characters and they are all the same digit.
     *
     * @throws CnpjGeneratorOptionPrefixInvalidException If the prefix has 12
     *   characters that are all the same digit.
     */
    private function validatePrefixNonRepeatedDigits(string $partialCnpj): void
    {
        if (strlen($partialCnpj) < self::CNPJ_PREFIX_MAX_LENGTH) {
            return;
        }

        $eligibleCnpjPrefix = substr($partialCnpj, 0, self::CNPJ_PREFIX_MAX_LENGTH);
        $uniqueCharacters = array_unique(str_split($eligibleCnpjPrefix));

        if (count($uniqueCharacters) === 1 && preg_match('/^\d$/', $uniqueCharacters[0] ?? '')) {
            throw new CnpjGeneratorOptionPrefixInvalidException(
                $partialCnpj,
                'Repeated digits are not considered valid.',
            );
        }
    }
}
