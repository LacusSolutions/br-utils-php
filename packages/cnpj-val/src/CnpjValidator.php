<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj;

use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorInputTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionTypeInvalidException;
use Throwable;

/**
 * Validator for CNPJ (Cadastro Nacional da Pessoa Jurídica) identifiers.
 * Validates CNPJ strings according to the Brazilian CNPJ validation algorithm.
 */
class CnpjValidator
{
    /**
     * The default options used by this validator instance.
     */
    private readonly CnpjValidatorOptions $options;

    /**
     * Creates a new `CnpjValidator` with optional default options.
     *
     * Default options apply to every call to `isValid` unless overridden by
     * per-call argument. Options control case sensitivity and the type of
     * whether the CNPJ input is alphanumeric or numeric.
     *
     * When `defaultOptions` is a `CnpjValidatorOptions` instance, that
     * instance is used directly (no copy is created). Mutating it later (e.g.
     * via the `options` getter or the original reference) affects future
     * `isValid` calls that do not pass per-call options.
     *
     * When named parameters or none are passed, a new `CnpjValidatorOptions`
     * instance is created from it.
     *
     * @param ?CnpjValidatorOptions $options
     * @param ?(CnpjValidationType|'alphanumeric'|'numeric') $type
     * @param ?bool $caseSensitive
     *
     * @throws CnpjValidatorOptionsTypeError If any option has an invalid type.
     * @throws CnpjValidatorOptionTypeInvalidException If the `type` option is not one of the allowed values.
     */
    public function __construct(
        ?CnpjValidatorOptions $options = null,
        $type = null,
        $caseSensitive = null,
    ) {
        $this->options = $options instanceof CnpjValidatorOptions
            ? $options
            : new CnpjValidatorOptions(
                caseSensitive: $caseSensitive,
                type: $type,
                overrides: [$options],
            );
    }

    /**
     * Returns the default options used by this validator when per-call options
     * are not provided.
     *
     * The returned object is the same instance used internally; mutating it (e.g.
     * via setters on `CnpjValidatorOptions`) affects future `isValid` calls that
     * do not pass `options`.
     */
    public function getOptions(): CnpjValidatorOptions
    {
        return $this->options;
    }

    /**
     * Validates a CNPJ input.
     *
     * Per-call `options` are merged over the default options instance for this
     * call alone. The default options instance remains unchanged.
     *
     * @param string|list<string> $cnpjInput
     * @param ?CnpjValidatorOptions $options
     * @param ?(CnpjValidationType|'alphanumeric'|'numeric') $type
     * @param ?bool $caseSensitive
     *
     * @throws CnpjValidatorInputTypeError If the input is not a string or an
     *     array of strings.
     * @throws CnpjValidatorOptionsTypeError If any option has an invalid type.
     * @throws CnpjValidatorOptionTypeInvalidException If the `type` option is not one of the allowed values.
     */
    public function isValid(
        $cnpjInput,
        $options = null,
        $type = null,
        $caseSensitive = null,
    ): bool {
        $actualInput = $this->toStringInput($cnpjInput);
        $actualOptions = $this->resolveOptions($options, $type, $caseSensitive);

        $sanitizedCnpj = $actualInput;

        if (!$actualOptions->caseSensitive) {
            $sanitizedCnpj = strtoupper($sanitizedCnpj);
        }

        if ($actualOptions->type === CnpjValidationType::Numeric) {
            $sanitizedCnpj = preg_replace('/[^0-9]/', '', $sanitizedCnpj) ?? '';
        } else {
            $sanitizedCnpj = preg_replace('/[^0-9A-Z]/i', '', $sanitizedCnpj) ?? '';
        }

        if (strlen($sanitizedCnpj) !== CnpjValidatorOptions::CNPJ_LENGTH) {
            return false;
        }

        if (
            $sanitizedCnpj[12] < '0'
            || $sanitizedCnpj[12] > '9'
            || $sanitizedCnpj[13] < '0'
            || $sanitizedCnpj[13] > '9'
        ) {
            return false;
        }

        try {
            $cnpjCheckDigits = new CnpjCheckDigits($sanitizedCnpj);

            return $sanitizedCnpj === $cnpjCheckDigits->cnpj;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Normalizes the input to a string.
     *
     * @throws CnpjValidatorInputTypeError If the input is not a string or an
     *     array of strings.
     */
    private function toStringInput(mixed $cnpjInput): string
    {
        if (is_string($cnpjInput)) {
            return $cnpjInput;
        }

        if (is_array($cnpjInput)) {
            $joined = '';

            foreach ($cnpjInput as $item) {
                if (!is_string($item)) {
                    throw new CnpjValidatorInputTypeError($cnpjInput, 'string or string[]');
                }

                $joined .= $item;
            }

            return $joined;
        }

        throw new CnpjValidatorInputTypeError($cnpjInput, 'string or string[]');
    }

    /**
     * Merges per-call options over instance defaults when any override is present.
     *
     * @param ?CnpjValidatorOptions $options
     * @param ?(CnpjValidationType|'alphanumeric'|'numeric') $type
     * @param ?bool $caseSensitive
     *
     * @throws CnpjValidatorOptionsTypeError If any option has an invalid type.
     * @throws CnpjValidatorOptionTypeInvalidException If the `type` option is not one of the allowed values.
     */
    private function resolveOptions(
        $options = null,
        $type = null,
        $caseSensitive = null,
    ): CnpjValidatorOptions {
        if ($options === null && $type === null && $caseSensitive === null) {
            return $this->options;
        }

        return new CnpjValidatorOptions(
            ...$this->options->getAll(),
            overrides: [
                [
                    'type' => $type,
                    'caseSensitive' => $caseSensitive,
                ],
                $options ?? [],
            ],
        );
    }
}
