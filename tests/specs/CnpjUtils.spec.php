<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests\Cnpj;

use Lacus\BrUtils\Cnpj\CnpjFormatter;
use Lacus\BrUtils\Cnpj\CnpjFormatterOptions;
use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\CnpjGeneratorOptions;
use Lacus\BrUtils\Cnpj\CnpjUtils;
use Lacus\BrUtils\Cnpj\CnpjValidator;
use Lacus\BrUtils\Cnpj\CnpjValidatorOptions;
use Lacus\BrUtils\Cnpj\Enums\CnpjType as CnpjGenerationType;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjFormatterOptionsForbiddenKeyCharacterException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjFormatterOptionsHiddenRangeInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionPrefixInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionTypeInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionTypeInvalidException;

describe('CnpjUtils', function () {
    describe('constructor', function () {
        describe('when called with no arguments', function () {
            it('creates an instance with default options', function () {
                $defaultFormatterOptions = new CnpjFormatterOptions();
                $defaultGeneratorOptions = new CnpjGeneratorOptions();
                $defaultValidatorOptions = new CnpjValidatorOptions();

                $utils = new CnpjUtils();

                expect($utils->getFormatter()->getOptions()->getAll())->toBe($defaultFormatterOptions->getAll());
                expect($utils->getGenerator()->getOptions()->getAll())->toBe($defaultGeneratorOptions->getAll());
                expect($utils->getValidator()->getOptions()->getAll())->toBe($defaultValidatorOptions->getAll());
            });
        });

        describe('when called with arguments', function () {
            it('configures formatter, generator, and validator from arrays', function () {
                $formatterOptions = ['slashKey' => '|'];
                $generatorOptions = ['format' => true, 'prefix' => '12345'];
                $validatorOptions = ['type' => CnpjValidationType::Numeric, 'caseSensitive' => false];

                $utils = new CnpjUtils(
                    formatter: $formatterOptions,
                    generator: $generatorOptions,
                    validator: $validatorOptions,
                );

                expect($utils->getFormatter()->getOptions()->getAll())->toMatchArray($formatterOptions);
                expect($utils->getGenerator()->getOptions()->getAll())->toMatchArray($generatorOptions);
                expect($utils->getValidator()->getOptions()->getAll())->toMatchArray($validatorOptions);
            });

            it('uses provided options instances directly', function () {
                $formatterOptions = new CnpjFormatterOptions(slashKey: '|');
                $generatorOptions = new CnpjGeneratorOptions(format: true);
                $validatorOptions = new CnpjValidatorOptions(type: CnpjValidationType::Numeric);

                $utils = new CnpjUtils(
                    formatter: $formatterOptions,
                    generator: $generatorOptions,
                    validator: $validatorOptions,
                );

                expect($utils->getFormatter()->getOptions())->toBe($formatterOptions);
                expect($utils->getGenerator()->getOptions())->toBe($generatorOptions);
                expect($utils->getValidator()->getOptions())->toBe($validatorOptions);
            });

            it('mutates shared options instances and affects later calls', function () {
                $generatorOptions = new CnpjGeneratorOptions(format: false, type: CnpjGenerationType::Numeric);
                $utils = new CnpjUtils(generator: $generatorOptions);

                $generatorOptions->format = true;
                $generatorOptions->type = CnpjGenerationType::Alphabetic;

                expect($utils->getGenerator()->getOptions()->getAll())->toMatchArray([
                    'format' => true,
                    'type' => CnpjGenerationType::Alphabetic,
                ]);
            });
        });

        describe('when called with invalid options', function () {
            it('throws formatter exceptions for invalid formatter options', function () {
                expect(function () {
                    new CnpjUtils(formatter: ['hiddenStart' => -1]);
                })->toThrow(CnpjFormatterOptionsHiddenRangeInvalidException::class);

                expect(function () {
                    new CnpjUtils(formatter: ['dashKey' => "\u{00e5}"]);
                })->toThrow(CnpjFormatterOptionsForbiddenKeyCharacterException::class);
            });

            it('throws generator exceptions for invalid generator options', function () {
                expect(function () {
                    new CnpjUtils(generator: ['prefix' => '00000000']);
                })->toThrow(CnpjGeneratorOptionPrefixInvalidException::class);

                expect(function () {
                    new CnpjUtils(generator: ['type' => 'invalid']);
                })->toThrow(CnpjGeneratorOptionTypeInvalidException::class);

                expect(function () {
                    new CnpjUtils(generator: ['prefix' => 123]);
                })->toThrow(CnpjGeneratorOptionsTypeError::class);
            });

            it('throws validator exceptions for invalid validator options', function () {
                expect(function () {
                    new CnpjUtils(validator: ['type' => 'invalid']);
                })->toThrow(CnpjValidatorOptionTypeInvalidException::class);
            });
        });
    });

    describe('resources accessors', function () {
        it('returns the formatter instance used internally', function () {
            $utils = new CnpjUtils();

            expect($utils->getFormatter())->toBeInstanceOf(CnpjFormatter::class);
        });

        it('returns the generator instance used internally', function () {
            $utils = new CnpjUtils();

            expect($utils->getGenerator())->toBeInstanceOf(CnpjGenerator::class);
        });

        it('returns the validator instance used internally', function () {
            $utils = new CnpjUtils();

            expect($utils->getValidator())->toBeInstanceOf(CnpjValidator::class);
        });
    });

    describe('`format` method', function () {
        /**
         * @param string|list<string> $cnpj
         * @param ?string $slashKey
         */
        $formatWithNamedOptionsInConstructor = function ($cnpj, $slashKey = null): string {
            $utils = new CnpjUtils(formatter: ['slashKey' => $slashKey]);

            return $utils->format($cnpj);
        };

        /**
         * @param string|list<string> $cnpj
         * @param ?string $slashKey
         */
        $formatWithFormatterOptionsInConstructor = function (string $cnpj, $slashKey = null): string {
            $options = new CnpjFormatterOptions(slashKey: $slashKey);
            $utils = new CnpjUtils(formatter: $options);

            return $utils->format($cnpj);
        };

        /**
         * @param string|list<string> $cnpj
         * @param ?string $slashKey
         */
        $formatWithNamedOptionsInMethod = function (string $cnpj, $slashKey = null): string {
            $utils = new CnpjUtils();

            return $utils->format($cnpj, slashKey: $slashKey);
        };

        /**
         * @param string|list<string> $cnpj
         * @param ?string $slashKey
         */
        $formatWithFormatterOptionsInMethod = function (string $cnpj, $slashKey = null): string {
            $utils = new CnpjUtils();
            $options = new CnpjFormatterOptions(slashKey: $slashKey);

            return $utils->format($cnpj, $options);
        };

        $formatContexts = [
            ['when options are passed to constructor as an array', $formatWithNamedOptionsInConstructor],
            ['when options are passed to constructor as a `CnpjFormatterOptions` instance', $formatWithFormatterOptionsInConstructor],
            ['when options are passed to the method as named arguments', $formatWithNamedOptionsInMethod],
            ['when options are passed to the method as a `CnpjFormatterOptions` instance', $formatWithFormatterOptionsInMethod],
        ];

        foreach ($formatContexts as $formatContext) {
            [$description, $format] = $formatContext;

            describe($description, function () use ($format) {
                it('matches `CnpjFormatter::format` behavior', function () use ($format) {
                    $input = '91415732000793';
                    $formatter = new CnpjFormatter();

                    $result = $format($input);

                    expect($result)->toBe($formatter->format($input));
                });

                it('forwards formatting options', function () use ($format) {
                    $input = '01ABC234000X56';
                    $slashKey = '|';

                    $result = $format($input, $slashKey);

                    expect($result)->toBe("01.ABC.234{$slashKey}000X-56");
                });
            });
        }

        it('applies constructor formatter defaults when method options are omitted', function () {
            $utils = new CnpjUtils(
                formatter: [
                    'hidden' => true,
                    'hiddenKey' => '#',
                ],
            );

            $result = $utils->format('12ABC34500DE99');

            expect($result)->toContain('#');
        });
    });

    describe('`generate` method', function () {
        /**
         * @param ?bool $format
         * @param ?string $prefix
         * @param ?CnpjGenerationType $type
         */
        $generateWithNamedOptionsInConstructor = function ($format = null, $prefix = null, $type = null): string {
            $utils = new CnpjUtils(generator: compact('format', 'prefix', 'type'));

            return $utils->generate();
        };

        /**
         * @param ?bool $format
         * @param ?string $prefix
         * @param ?CnpjGenerationType $type
         */
        $generateWithGeneratorOptionsInConstructor = function ($format = null, $prefix = null, $type = null): string {
            $options = new CnpjGeneratorOptions(format: $format, prefix: $prefix, type: $type);
            $utils = new CnpjUtils(generator: $options);

            return $utils->generate();
        };

        /**
         * @param ?bool $format
         * @param ?string $prefix
         * @param ?CnpjGenerationType $type
         */
        $generateWithNamedOptionsInMethod = function ($format = null, $prefix = null, $type = null): string {
            $utils = new CnpjUtils();

            return $utils->generate(format: $format, prefix: $prefix, type: $type);
        };

        /**
         * @param ?bool $format
         * @param ?string $prefix
         * @param ?CnpjGenerationType $type
         */
        $generateWithGeneratorOptionsInMethod = function ($format = null, $prefix = null, $type = null): string {
            $utils = new CnpjUtils();
            $options = new CnpjGeneratorOptions(format: $format, prefix: $prefix, type: $type);

            return $utils->generate($options);
        };

        $generateContexts = [
            ['when options are passed to constructor as an array', $generateWithNamedOptionsInConstructor],
            ['when options are passed to constructor as a `CnpjGeneratorOptions` instance', $generateWithGeneratorOptionsInConstructor],
            ['when options are passed to the method as named arguments', $generateWithNamedOptionsInMethod],
            ['when options are passed to the method as a `CnpjGeneratorOptions` instance', $generateWithGeneratorOptionsInMethod],
        ];

        foreach ($generateContexts as $generateContext) {
            [$description, $generate] = $generateContext;

            describe($description, function () use ($generate) {
                it('matches `CnpjGenerator::generate` behavior', function () use ($generate) {
                    $generator = new CnpjGenerator();

                    $result = $generate();

                    expect($result)->toMatch('/^[0-9A-Z]{14}$/');
                    expect(strlen($result))->toBe(strlen($generator->generate()));
                });

                it('forwards generation options', function () use ($generate) {
                    $options = [
                        'format' => true,
                        'prefix' => '12345',
                        'type' => CnpjGenerationType::Numeric,
                    ];

                    $result = $generate(...$options);

                    expect($result)->toMatch('/^12\.345\.\d{3}\/\d{4}-\d{2}$/');
                });

                it('returns a deterministic CNPJ for a full 12-character prefix', function () use ($generate) {
                    $prefix = '123456780009';
                    $results = [];

                    for ($i = 0; $i < 20; $i++) {
                        $results[] = $generate(prefix: $prefix);
                    }

                    $uniqueValues = array_unique($results);

                    expect($uniqueValues)->toHaveCount(1);
                });
            });
        }
    });

    describe('`isValid` method', function () {
        /**
         * @param string|list<string> $cnpj
         * @param ?CnpjValidationType $type
         * @param ?bool $caseSensitive
         */
        $isValidWithNamedOptionsInConstructor = function (string $cnpj, $type = null, $caseSensitive = null): bool {
            $utils = new CnpjUtils(validator: compact('type', 'caseSensitive'));

            return $utils->isValid($cnpj);
        };

        /**
         * @param string|list<string> $cnpj
         * @param ?CnpjValidationType $type
         * @param ?bool $caseSensitive
         */
        $isValidWithValidatorOptionsInConstructor = function (string $cnpj, $type = null, $caseSensitive = null): bool {
            $options = new CnpjValidatorOptions(type: $type, caseSensitive: $caseSensitive);
            $utils = new CnpjUtils(validator: $options);

            return $utils->isValid($cnpj);
        };

        /**
         * @param string|list<string> $cnpj
         * @param ?CnpjValidationType $type
         * @param ?bool $caseSensitive
         */
        $isValidWithNamedOptionsInMethod = function (string $cnpj, $type = null, $caseSensitive = null): bool {
            $utils = new CnpjUtils();

            return $utils->isValid($cnpj, type: $type, caseSensitive: $caseSensitive);
        };

        /**
         * @param string|list<string> $cnpj
         * @param ?CnpjValidationType $type
         * @param ?bool $caseSensitive
         */
        $isValidWithValidatorOptionsInMethod = function (string $cnpj, $type = null, $caseSensitive = null): bool {
            $utils = new CnpjUtils();
            $options = new CnpjValidatorOptions(type: $type, caseSensitive: $caseSensitive);

            return $utils->isValid($cnpj, $options);
        };

        $isValidContexts = [
            ['when options are passed to constructor as an array', $isValidWithNamedOptionsInConstructor],
            ['when options are passed to constructor as a `CnpjValidatorOptions` instance', $isValidWithValidatorOptionsInConstructor],
            ['when options are passed to the method as named arguments', $isValidWithNamedOptionsInMethod],
            ['when options are passed to the method as a `CnpjValidatorOptions` instance', $isValidWithValidatorOptionsInMethod],
        ];

        foreach ($isValidContexts as $isValidContext) {
            [$description, $isValid] = $isValidContext;

            describe($description, function () use ($isValid) {
                it('matches `CnpjValidator::isValid` behavior', function () use ($isValid) {
                    $input = '91415732000793';
                    $validator = new CnpjValidator();

                    $result = $isValid($input);

                    expect($result)->toBe($validator->isValid($input));
                });

                it('forwards validation options', function () use ($isValid) {
                    $input = '1QB5UKALPYFP59';

                    $result = $isValid($input, CnpjValidationType::Numeric);
                    expect($result)->toBeFalse();

                    $result = $isValid($input, CnpjValidationType::Alphanumeric);
                    expect($result)->toBeTrue();
                });

                it('validates formatted and unformatted CNPJ strings', function () use ($isValid) {
                    $result = $isValid('1QB5UKALPYFP59');
                    expect($result)->toBeTrue();

                    $result = $isValid('1QB5.UKAL.PYF/P59');
                    expect($result)->toBeTrue();

                    $result = $isValid('AB123CDE0001555');
                    expect($result)->toBeFalse();
                });
            });
        }
    });
});
