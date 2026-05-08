<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj;

use Lacus\BrUtils\Cnpj\Enums\CnpjType;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionPrefixInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionTypeInvalidException;
use Lacus\Utils\SequenceGenerator;

/**
 * Generator for CNPJ (Cadastro Nacional da Pessoa Jurídica) identifiers. Builds
 * valid 14-character CNPJ values by combining an optional prefix with a
 * randomly generated sequence and computed check digits. Options control
 * prefix, character type (numeric, alphabetic, or alphanumeric), and whether
 * the result is formatted (`00.000.000/0000-00`).
 */
class CnpjGenerator
{
    private const CNPJ_DOT_KEY = '.';
    private const CNPJ_SLASH_KEY = '/';
    private const CNPJ_DASH_KEY = '-';

    /**
     * The default options used by this generator instance.
     */
    private readonly CnpjGeneratorOptions $options;

    /**
     * Creates a new `CnpjGenerator` with optional default options.
     *
     * Default options apply to every call to `generate` unless overridden by the
     * per-call `options` argument. Options control prefix, character type, and
     * whether the generated CNPJ is formatted.
     *
     * When `defaultOptions` is a `CnpjGeneratorOptions` instance, that instance
     * is used directly (no copy is created). Mutating it later (e.g. via the
     * `options` getter or the original reference) affects future `generate` calls
     * that do not pass per-call options. When a plain object or nothing is
     * passed, a new `CnpjGeneratorOptions` instance is created from it.
     *
     * @param ?CnpjGeneratorOptions $options
     * @param ?bool $format
     * @param ?string $prefix
     * @param ?(CnpjType|'alphanumeric'|'alphabetic'|'numeric') $type
     *
     * @throws CnpjGeneratorOptionsTypeError If any option has an invalid type.
     * @throws CnpjGeneratorOptionPrefixInvalidException If the `prefix` option
     *   contains an invalid combination of characters.
     * @throws CnpjGeneratorOptionTypeInvalidException If the `type` option is
     *   not one of the allowed values.
     */
    public function __construct(
        ?CnpjGeneratorOptions $options = null,
        $format = null,
        $prefix = null,
        $type = null,
    ) {
        $this->options = $options instanceof CnpjGeneratorOptions
            ? $options
            : new CnpjGeneratorOptions(
                format: $format,
                prefix: $prefix,
                type: $type,
                overrides: [$options],
            );
    }

    /**
     * Returns the default options used by this generator when per-call options
     * are not provided.
     *
     * The returned object is the same instance used internally; mutating it (e.g.
     * via setters on `CnpjGeneratorOptions`) affects future `generate` calls that
     * do not pass `options`.
     */
    public function getOptions(): CnpjGeneratorOptions
    {
        return $this->options;
    }

    /**
     * Generates a valid CNPJ value.
     *
     * Builds a 14-character CNPJ from the configured prefix (if any), a random
     * sequence of the configured character type, and two computed check digits.
     * If formatting is enabled, the result is returned as `00.000.000/0000-00`.
     *
     * Per-call `options` are merged over the instance default options for this
     * call only; the instance defaults are unchanged.
     *
     * @param ?CnpjGeneratorOptions $options
     * @param ?bool $format
     * @param ?string $prefix
     * @param ?CnpjType $type
     *
     * @throws CnpjGeneratorOptionsTypeError If any option has an invalid type.
     * @throws CnpjGeneratorOptionPrefixInvalidException If the `prefix` option
     *   contains an invalid combination of characters.
     * @throws CnpjGeneratorOptionTypeInvalidException If the `type` option is
     *   not one of the allowed values.
     */
    public function generate(
        ?CnpjGeneratorOptions $options = null,
        $format = null,
        $prefix = null,
        $type = null,
    ): string {
        $actualOptions = new CnpjGeneratorOptions(
            ...$this->options->getAll(),
            overrides: [
                [
                    'format' => $format,
                    'prefix' => $prefix,
                    'type' => $type,
                ],
                $options ?? [],
            ],
        );

        $charactersToGenerate = CnpjGeneratorOptions::CNPJ_PREFIX_MAX_LENGTH - strlen($actualOptions->prefix);
        $generatedCharacters = SequenceGenerator::generate($charactersToGenerate, $actualOptions->type->toSequenceType());
        $generatedCnpj = $actualOptions->prefix . $generatedCharacters;

        try {
            $cnpjCheckDigits = new CnpjCheckDigits($generatedCnpj);
            $generatedCnpj = $cnpjCheckDigits->cnpj;
        } catch (CnpjCheckDigitsException $e) {
            return $this->generate($options, $format, $prefix, $type);
        }

        if ($actualOptions->format) {
            $generatedCnpj =
                substr($generatedCnpj, 0, 2) .
                self::CNPJ_DOT_KEY .
                substr($generatedCnpj, 2, 3) .
                self::CNPJ_DOT_KEY .
                substr($generatedCnpj, 5, 3) .
                self::CNPJ_SLASH_KEY .
                substr($generatedCnpj, 8, 4) .
                self::CNPJ_DASH_KEY .
                substr($generatedCnpj, 12, 2);
        }

        return $generatedCnpj;
    }
}
