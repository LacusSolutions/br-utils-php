<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj;

use Closure;
use Lacus\BrUtils\Cnpj\Enums\CnpjType as CnpjGenerationType;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjFormatterException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjFormatterInputTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjFormatterOptionsForbiddenKeyCharacterException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjFormatterOptionsHiddenRangeInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjFormatterOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionPrefixInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionTypeInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorInputTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionTypeInvalidException;

/**
 * Utility class for CNPJ (Cadastro Nacional da Pessoa Jurídica) operations.
 * Provides a unified interface for formatting, generating, and validating CNPJ
 * values.
 */
class CnpjUtils
{
    private CnpjFormatter $formatter;
    private CnpjGenerator $generator;
    private CnpjValidator $validator;

    /**
     * @param CnpjFormatterOptions|array{
     *     hidden?: bool|null,
     *     hiddenKey?: string|null,
     *     hiddenStart?: int|null,
     *     hiddenEnd?: int|null,
     *     dotKey?: string|null,
     *     slashKey?: string|null,
     *     dashKey?: string|null,
     *     escape?: bool|null,
     *     encode?: bool|null,
     *     onFail?: (Closure(mixed, CnpjFormatterException): string|null),
     * } $formatter
     * @param CnpjGeneratorOptions|array{
     *     format?: bool|null,
     *     prefix?: string|null,
     *     type?: CnpjGenerationType|null,
     * } $generator
     * @param CnpjValidatorOptions|array{
     *     caseSensitive?: bool|null,
     *     type?: CnpjValidationType|'alphanumeric'|'numeric'|null,
     * } $validator
     *
     * @throws CnpjFormatterOptionsTypeError If any option has an invalid type.
     * @throws CnpjFormatterOptionsHiddenRangeInvalidException If `hiddenStart`
     *     or `hiddenEnd` are out of valid range.
     * @throws CnpjFormatterOptionsForbiddenKeyCharacterException If any key
     *     option contains a disallowed character.
     * @throws CnpjGeneratorOptionsTypeError If any option has an invalid type.
     * @throws CnpjGeneratorOptionPrefixInvalidException If the `prefix` option
     *     contains an invalid combination of characters.
     * @throws CnpjGeneratorOptionTypeInvalidException If the `type` option is
     *     not one of the allowed values.
     * @throws CnpjValidatorOptionsTypeError If any option has an invalid type.
     * @throws CnpjValidatorOptionTypeInvalidException If the `type` option is not one of the allowed values.
     */
    public function __construct(
        $formatter = [],
        $generator = [],
        $validator = [],
    ) {
        $formatterOptions = $formatter instanceof CnpjFormatterOptions
            ? $formatter
            : new CnpjFormatterOptions(...$formatter);
        $this->formatter = new CnpjFormatter($formatterOptions);

        $generatorOptions = $generator instanceof CnpjGeneratorOptions
            ? $generator
            : new CnpjGeneratorOptions(...$generator);
        $this->generator = new CnpjGenerator($generatorOptions);

        $validatorOptions = $validator instanceof CnpjValidatorOptions
            ? $validator
            : new CnpjValidatorOptions(...$validator);
        $this->validator = new CnpjValidator($validatorOptions);
    }

    /**
     * Formats a CNPJ value into a normalized 14-character alphanumeric string.
     *
     * Input is normalized by stripping non-alphanumeric characters and converting
     * to uppercase. If the result length is not exactly 14, or if the input is
     * not a string or array of strings, the configured `onFail` callback is
     * invoked with the original value and an error; its return value is used as
     * the result.
     *
     * When valid, the result may be further transformed according to options:
     *
     * - If `hidden` is `true`, characters between `hiddenStart` and `hiddenEnd`
     *   (inclusive) are replaced with `hiddenKey`.
     * - If `escape` is `true`, HTML special characters are escaped.
     * - If `encode` is `true`, the string is passed through URL encoding (similar to
     *   JavaScript's `encodeURIComponent`).
     *
     * Per-call `options` are merged over the instance default options for this
     * call only; the instance defaults are unchanged.
     *
     * @param string|list<string> $cnpjInput
     * @param ?CnpjFormatterOptions $options
     * @param ?bool $hidden
     * @param ?string $hiddenKey
     * @param ?int $hiddenStart
     * @param ?int $hiddenEnd
     * @param ?string $dotKey
     * @param ?string $slashKey
     * @param ?string $dashKey
     * @param ?bool $escape
     * @param ?bool $encode
     * @param ?Closure(mixed, CnpjFormatterException): string $onFail
     *
     * @throws CnpjFormatterOptionsTypeError If any option has an invalid type.
     * @throws CnpjFormatterOptionsHiddenRangeInvalidException If `hiddenStart`
     *     or `hiddenEnd` are out of valid range.
     * @throws CnpjFormatterOptionsForbiddenKeyCharacterException If any key
     *     option contains a disallowed character.
     * @throws CnpjFormatterInputTypeError If the input is not a string or array of strings.
     */
    public function format(
        $cnpjInput,
        $options = null,
        $hidden = null,
        $hiddenKey = null,
        $hiddenStart = null,
        $hiddenEnd = null,
        $dotKey = null,
        $slashKey = null,
        $dashKey = null,
        $escape = null,
        $encode = null,
        $onFail = null,
    ): string {
        return $this->formatter->format(
            $cnpjInput,
            $options,
            $hidden,
            $hiddenKey,
            $hiddenStart,
            $hiddenEnd,
            $dotKey,
            $slashKey,
            $dashKey,
            $escape,
            $encode,
            $onFail,
        );
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
     * @param ?CnpjGenerationType $type
     *
     * @throws CnpjGeneratorOptionsTypeError If any option has an invalid type.
     * @throws CnpjGeneratorOptionPrefixInvalidException If the `prefix` option
     *     contains an invalid combination of characters.
     * @throws CnpjGeneratorOptionTypeInvalidException If the `type` option is
     *     not one of the allowed values.
     */
    public function generate(
        ?CnpjGeneratorOptions $options = null,
        $format = null,
        $prefix = null,
        $type = null,
    ): string {
        return $this->generator->generate(
            $options,
            $format,
            $prefix,
            $type,
        );
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
        return $this->validator->isValid(
            $cnpjInput,
            $options,
            $type,
            $caseSensitive,
        );
    }

    /**
     * Returns the formatter instance used by the utils instance.
     *
     * @return CnpjFormatter
     */
    public function getFormatter(): CnpjFormatter
    {
        return $this->formatter;
    }

    /**
     * Returns the generator instance used by the utils instance.
     *
     * @return CnpjGenerator
     */
    public function getGenerator(): CnpjGenerator
    {
        return $this->generator;
    }

    /**
     * Returns the validator instance used by the utils instance.
     *
     * @return CnpjValidator
     */
    public function getValidator(): CnpjValidator
    {
        return $this->validator;
    }
}
