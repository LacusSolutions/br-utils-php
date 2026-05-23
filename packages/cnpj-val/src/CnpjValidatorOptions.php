<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj;

use InvalidArgumentException;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionTypeInvalidException;

/**
 * Class to store the options for the CNPJ validator. This class provides a
 * centralized way to configure how CNPJ characters are validated, including
 * case sensitivity and the type of format that should be considered valid
 * (`numeric` or `alphanumeric`).
 *
 * @property CnpjValidationType $type
 * @property bool $caseSensitive
 */
class CnpjValidatorOptions
{
    /**
     * The standard length of a CNPJ (Cadastro Nacional da Pessoa Jurídica)
     * identifier (14 alphanumeric characters).
     */
    public const CNPJ_LENGTH = 14;

    /**
     * Default type of characters to validate for the CNPJ.
     */
    public const DEFAULT_TYPE = CnpjValidationType::Alphanumeric;

    /**
     * Default value for the `caseSensitive` option. When `false` and
     * alphanumeric CNPJ is being validated, lowercase characters are also
     * considered valid. Example: for a valid CNPJ `AB.123.CDE/FGHI-45`, if
     * `caseSensitive` is `false`, `ab.123.cde/fghi-45` is also considered
     * valid.
     */
    public const DEFAULT_CASE_SENSITIVE = true;

    /**
     * @var array{
     *     type: CnpjValidationType,
     *     caseSensitive: bool,
     * }
     */
    private array $options = []; // @phpstan-ignore-line property.defaultValue

    /**
     * Creates a new instance of `CnpjValidatorOptions`.
     *
     * Options can be provided as:
     *
     * 1. Named individual parameters for those options desired to be set.
     * 2. Multiple override objects that are merged in order (later overrides
     *    take precedence)
     *
     * All options are optional and will default to their predefined values if
     * not provided.
     *
     * @param ?(CnpjValidationType|'alphanumeric'|'numeric') $type
     * @param ?bool $caseSensitive
     * @param list<CnpjValidatorOptions|array{
     *     type?: CnpjValidationType|'alphanumeric'|'numeric'|null,
     *     caseSensitive?: bool|null,
     * }|null> $overrides
     *
     * @throws CnpjValidatorOptionsTypeError If any option has an invalid type.
     * @throws CnpjValidatorOptionTypeInvalidException If the `type` option is
     *     not one of the allowed values.
     */
    public function __construct(
        $type = null,
        $caseSensitive = null,
        ?array $overrides = [],
    ) {
        $this->setType($type);
        $this->setCaseSensitive($caseSensitive);

        foreach (($overrides ?? []) as $override) {
            if ($override === null) {
                continue;
            }

            if ($override instanceof CnpjValidatorOptions) {
                $this->set(...$override->getAll());
            } elseif (is_array($override)) {
                $this->set(
                    type: $override['type'] ?? null,
                    caseSensitive: $override['caseSensitive'] ?? null,
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
            'type'          => $this->getType(),
            'caseSensitive' => $this->getCaseSensitive(),
            default         => throw new InvalidArgumentException("Unknown property: {$name}"),
        };
    }

    /**
     * Property-style mutation to the options.
     */
    public function __set(string $name, mixed $value): void
    {
        match ($name) {
            'type'          => $this->setType($value),           // @phpstan-ignore-line argument.type
            'caseSensitive' => $this->setCaseSensitive($value),   // @phpstan-ignore-line argument.type
            default         => throw new InvalidArgumentException("Unknown property: {$name}"),
        };
    }

    /**
     * Returns a shallow copy of all current options. This is useful for
     * creating snapshots of the current configuration.
     *
     * @return array{
     *     type: CnpjValidationType,
     *     caseSensitive: bool,
     * }
     */
    public function getAll(): array
    {
        return [...$this->options];
    }

    /**
     * Gets the type of characters to validate for the CNPJ.
     */
    private function getType(): CnpjValidationType
    {
        return $this->options['type'];
    }

    /**
     * Sets the type of characters to validate for the CNPJ.
     *
     * The options are:
   * - `alphanumeric`: alphanumeric CNPJ format.
   * - `numeric`: numeric-only (legacy) CNPJ format.
     *
     * @param CnpjValidationType|'alphanumeric'|'numeric'|null $value
     *
     * @throws CnpjValidatorOptionsTypeError If the value is not a string.
     * @throws CnpjValidatorOptionTypeInvalidException If the value is not a
     *     valid type.
     */
    private function setType($value): void
    {
        $actualType = $value ?? self::DEFAULT_TYPE;
        $actualType = $this->parseCnpjValidationType('type', $actualType);

        $this->options['type'] = $actualType;
    }

    /**
     * Gets whether the CNPJ is validated in a case-sensitive manner.
     */
    private function getCaseSensitive(): bool
    {
        return $this->options['caseSensitive'];
    }

    /**
     * Sets whether the CNPJ is validated in a case-sensitive manner.
     *
     * @param bool|null $value
     */
    private function setCaseSensitive($value): void
    {
        $actualCaseSensitive = $value ?? self::DEFAULT_CASE_SENSITIVE;
        $actualCaseSensitive = (bool) $actualCaseSensitive;

        $this->options['caseSensitive'] = $actualCaseSensitive;
    }

    /**
     * Sets multiple options at once. This method allows you to update multiple
     * options in a single call. Only the non-nullable provided options are
     * updated; options not included in the object or set to `null` retain
     * their current values.
     *
     * @param ?(CnpjValidationType|'alphanumeric'|'numeric') $type
     * @param ?bool $caseSensitive
     *
     * @throws CnpjValidatorOptionsTypeError If any option has an invalid type.
     * @throws CnpjValidatorOptionTypeInvalidException If the `type` option is
     *     not one of the allowed values.
     */
    public function set(
        $type = null,
        $caseSensitive = null,
    ): self {
        $this->setType($type ?? $this->getType());
        $this->setCaseSensitive($caseSensitive ?? $this->getCaseSensitive());

        return $this;
    }

    /**
     * Throws if the given value is not a CnpjValidationType.
     *
     * @param 'type' $optionName
     * @param CnpjValidationType|string $value
     *
     * @throws CnpjValidatorOptionsTypeError If the value is not a string.
     * @throws CnpjValidatorOptionTypeInvalidException If the value is not a
     *     valid type.
     */
    private function parseCnpjValidationType(string $optionName, mixed $value): CnpjValidationType
    {
        if ($value instanceof CnpjValidationType) {
            return $value;
        }

        if (is_string($value)) {
            $cnpjValidationType = CnpjValidationType::tryFrom($value);

            if ($cnpjValidationType) {
                return $cnpjValidationType;
            }

            throw new CnpjValidatorOptionTypeInvalidException($value, CnpjValidationType::values());
        }

        throw new CnpjValidatorOptionsTypeError($optionName, $value, 'CnpjValidationType or string');
    }
}
