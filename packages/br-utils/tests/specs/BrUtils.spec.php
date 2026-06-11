<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests;

use InvalidArgumentException;
use Lacus\BrUtils;
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
use Lacus\BrUtils\Cpf\CpfFormatter;
use Lacus\BrUtils\Cpf\CpfFormatterOptions;
use Lacus\BrUtils\Cpf\CpfGenerator;
use Lacus\BrUtils\Cpf\CpfGeneratorOptions;
use Lacus\BrUtils\Cpf\CpfUtils;
use Lacus\CpfFmt\CpfFormatterOptions as LegacyCpfFormatterOptions;
use Lacus\CpfGen\CpfGeneratorOptions as LegacyCpfGeneratorOptions;
use Lacus\CpfUtils\CpfFormatter as LegacyCpfFormatter;
use Lacus\CpfUtils\CpfGenerator as LegacyCpfGenerator;
use Lacus\CpfUtils\CpfValidator as LegacyCpfValidator;

describe('BrUtils', function () {
    /**
     * @return array{
     *     escape: bool,
     *     hidden: bool,
     *     hiddenKey: string,
     *     hiddenStart: int,
     *     hiddenEnd: int,
     *     dotKey: string,
     *     dashKey: string,
     *     onFail: \Closure(string, \Exception): string,
     * }
     */
    function getCpfFormatterOptions(LegacyCpfFormatterOptions $options): array
    {
        return [
            'escape' => $options->isEscaped(),
            'hidden' => $options->isHidden(),
            'hiddenKey' => $options->getHiddenKey(),
            'hiddenStart' => $options->getHiddenStart(),
            'hiddenEnd' => $options->getHiddenEnd(),
            'dotKey' => $options->getDotKey(),
            'dashKey' => $options->getDashKey(),
            'onFail' => $options->getOnFail(),
        ];
    }

    /**
     * @return array{
     *     format: bool,
     *     prefix: string,
     * }
     */
    function getCpfGeneratorOptions(LegacyCpfGeneratorOptions $options): array
    {
        return [
            'format' => $options->isFormatting(),
            'prefix' => $options->getPrefix(),
        ];
    }

    describe('constructor', function () {
        describe('when called with no arguments', function () {
            it('creates an instance with necessary resource instances', function () {
                $defaultCpfFormatterOptions = new CpfFormatterOptions();
                $defaultCpfGeneratorOptions = new CpfGeneratorOptions();
                $defaultCnpjFormatterOptions = new CnpjFormatterOptions();
                $defaultCnpjGeneratorOptions = new CnpjGeneratorOptions();
                $defaultCnpjValidatorOptions = new CnpjValidatorOptions();

                $utils = new BrUtils();

                expect(
                    getCpfFormatterOptions($utils->cpf->getFormatter()->getOptions())
                )->toMatchArray(
                    getCpfFormatterOptions($defaultCpfFormatterOptions)
                );
                expect(
                    getCpfGeneratorOptions($utils->cpf->getGenerator()->getOptions())
                )->toMatchArray(
                    getCpfGeneratorOptions($defaultCpfGeneratorOptions)
                );
                expect($utils->cnpj->getFormatter()->getOptions()->getAll())->toMatchArray($defaultCnpjFormatterOptions->getAll());
                expect($utils->cnpj->getGenerator()->getOptions()->getAll())->toMatchArray($defaultCnpjGeneratorOptions->getAll());
                expect($utils->cnpj->getValidator()->getOptions()->getAll())->toMatchArray($defaultCnpjValidatorOptions->getAll());
            });
        });

        describe('when called with arguments', function () {
            it("configures CPF's formatter, generator, and validator from arrays", function () {
                $cpfFormatterOptions = ['hidden' => true, 'hiddenKey' => '#', 'hiddenStart' => 8, 'hiddenEnd' => 10, 'dotKey' => '_', 'dashKey' => ' dv ', 'onFail' => function () {
                    return '1234567890';
                }];
                $cpfGeneratorOptions = ['format' => true, 'prefix' => '12345678'];

                $utils = new BrUtils(
                    cpf: [
                        'formatter' => $cpfFormatterOptions,
                        'generator' => $cpfGeneratorOptions,
                    ],
                );

                expect(
                    getCpfFormatterOptions($utils->cpf->getFormatter()->getOptions())
                )->toMatchArray($cpfFormatterOptions);
                expect(
                    getCpfGeneratorOptions($utils->cpf->getGenerator()->getOptions())
                )->toMatchArray($cpfGeneratorOptions);
            });

            it("configures CNPJ's formatter, generator, and validator from arrays", function () {
                $cnpjFormatterOptions = ['slashKey' => '|'];
                $cnpjGeneratorOptions = ['format' => true, 'prefix' => '12345'];
                $cnpjValidatorOptions = ['type' => CnpjValidationType::Numeric, 'caseSensitive' => false];

                $utils = new BrUtils(
                    cnpj: [
                        'formatter' => $cnpjFormatterOptions,
                        'generator' => $cnpjGeneratorOptions,
                        'validator' => $cnpjValidatorOptions,
                    ],
                );

                expect($utils->cnpj->getFormatter()->getOptions()->getAll())->toMatchArray($cnpjFormatterOptions);
                expect($utils->cnpj->getGenerator()->getOptions()->getAll())->toMatchArray($cnpjGeneratorOptions);
                expect($utils->cnpj->getValidator()->getOptions()->getAll())->toMatchArray($cnpjValidatorOptions);
            });

            it('uses provided options instances directly', function () {
                $cnpjFormatterOptions = new CnpjFormatterOptions();
                $cnpjGeneratorOptions = new CnpjGeneratorOptions();
                $cnpjValidatorOptions = new CnpjValidatorOptions();

                $utils = new BrUtils(
                    cnpj: [
                        'formatter' => $cnpjFormatterOptions,
                        'generator' => $cnpjGeneratorOptions,
                        'validator' => $cnpjValidatorOptions,
                    ],
                );

                expect($utils->cnpj->getFormatter()->getOptions())->toBe($cnpjFormatterOptions);
                expect($utils->cnpj->getGenerator()->getOptions())->toBe($cnpjGeneratorOptions);
                expect($utils->cnpj->getValidator()->getOptions())->toBe($cnpjValidatorOptions);
            });

            it('mutates shared options instances and affects later calls', function () {
                $cnpjFormatterOptions = new CnpjFormatterOptions();
                $utils = new BrUtils(
                    cnpj: [ 'formatter' => $cnpjFormatterOptions ],
                );

                $cnpjFormatterOptions->dashKey = '|';

                expect($utils->cnpj->getFormatter()->getOptions()->getAll())->toMatchArray([
                    'dashKey' => '|',
                ]);
            });
        });

        describe('when called with invalid options', function () {
            it('throws CPF formatter exceptions for invalid formatter options', function () {
                expect(function () {
                    new BrUtils(cpf: ['formatter' => ['hiddenStart' => -1]]);
                })->toThrow(InvalidArgumentException::class);
            });

            it('throws CPF generator exceptions for invalid generator options', function () {
                expect(function () {
                    new BrUtils(cpf: ['generator' => ['prefix' => '1234567890']]);
                })->toThrow(InvalidArgumentException::class);
            });

            it('throws CNPJ formatter exceptions for invalid formatter options', function () {
                expect(function () {
                    new BrUtils(cnpj: ['formatter' => ['hiddenStart' => -1]]);
                })->toThrow(CnpjFormatterOptionsHiddenRangeInvalidException::class);

                expect(function () {
                    new BrUtils(cnpj: ['formatter' => ['dashKey' => "\u{00e5}"]]);
                })->toThrow(CnpjFormatterOptionsForbiddenKeyCharacterException::class);
            });

            it('throws CNPJ generator exceptions for invalid generator options', function () {
                expect(function () {
                    new BrUtils(cnpj: ['generator' => ['prefix' => '00000000']]);
                })->toThrow(CnpjGeneratorOptionPrefixInvalidException::class);

                expect(function () {
                    new BrUtils(cnpj: ['generator' => ['type' => 'invalid']]);
                })->toThrow(CnpjGeneratorOptionTypeInvalidException::class);

                expect(function () {
                    new BrUtils(cnpj: ['generator' => ['prefix' => 123]]);
                })->toThrow(CnpjGeneratorOptionsTypeError::class);
            });

            it('throws CNPJ validator exceptions for invalid validator options', function () {
                expect(function () {
                    new BrUtils(cnpj: ['validator' => ['type' => 'invalid']]);
                })->toThrow(CnpjValidatorOptionTypeInvalidException::class);
            });
        });
    });

    describe('resources accessors', function () {
        it('returns the `CpfUtils instance', function () {
            $utils = new BrUtils();

            expect($utils->cpf)->toBeInstanceOf(CpfUtils::class);
        });

        it('returns the `CpfFormatter` instance', function () {
            $utils = new BrUtils();

            expect($utils->cpf->getFormatter())->toBeInstanceOf(LegacyCpfFormatter::class);
        });

        it('returns the `CpfGenerator` instance', function () {
            $utils = new BrUtils();

            expect($utils->cpf->getGenerator())->toBeInstanceOf(LegacyCpfGenerator::class);
        });

        it('returns the `CpfValidator` instance', function () {
            $utils = new BrUtils();

            expect($utils->cpf->getValidator())->toBeInstanceOf(LegacyCpfValidator::class);
        });

        it('returns the `CnpjUtils instance', function () {
            $utils = new BrUtils();

            expect($utils->cnpj)->toBeInstanceOf(CnpjUtils::class);
        });

        it('returns the `CnpjFormatter` instance', function () {
            $utils = new BrUtils();

            expect($utils->cnpj->getFormatter())->toBeInstanceOf(CnpjFormatter::class);
        });

        it('returns the `CnpjGenerator` instance', function () {
            $utils = new BrUtils();

            expect($utils->cnpj->getGenerator())->toBeInstanceOf(CnpjGenerator::class);
        });

        it('returns the `CnpjValidator` instance', function () {
            $utils = new BrUtils();

            expect($utils->cnpj->getValidator())->toBeInstanceOf(CnpjValidator::class);
        });
    });

    describe('CPF utils', function () {
        describe('`format` method', function () {
            /**
             * @param ?string $dotKey
             * @param ?string $dashKey
             */
            $formatWithNamedOptionsInConstructor = function (string $cpf, $dotKey = null, $dashKey = null): string {
                $utils = new BrUtils(cpf: ['formatter' => compact('dotKey', 'dashKey')]);

                return $utils->cpf->format($cpf);
            };

            /**
             * @param ?string $dotKey
             * @param ?string $dashKey
             */
            $formatWithNamedOptionsInMethod = function (string $cpf, $dotKey = null, $dashKey = null): string {
                $utils = new BrUtils();

                return $utils->cpf->format($cpf, dotKey: $dotKey, dashKey: $dashKey);
            };

            $formatContexts = [
                ['when options are passed to constructor as an array', $formatWithNamedOptionsInConstructor],
                ['when options are passed to the method as named arguments', $formatWithNamedOptionsInMethod],
            ];

            foreach ($formatContexts as $formatContext) {
                [$description, $format] = $formatContext;

                describe($description, function () use ($format) {
                    it('matches `CpfFormatter::format` behavior', function () use ($format) {
                        $input = '80976511061';
                        $formatter = new CpfFormatter();

                        $result = $format($input);

                        expect($result)->toBe($formatter->format($input));
                    });

                    it('forwards formatting options', function () use ($format) {
                        $input = '80976511061';
                        $dotKey = '_';
                        $dashKey = ' dv ';

                        $result = $format($input, $dotKey, $dashKey);

                        expect($result)->toBe("809_765_110 dv 61");
                    });
                });
            }

            it('applies constructor formatter defaults when method options are omitted', function () {
                $utils = new BrUtils(
                    cpf: [
                        'formatter' => [
                            'hidden' => true,
                            'hiddenKey' => '#',
                        ],
                    ],
                );

                $result = $utils->cpf->format('80976511061');

                expect($result)->toContain('#');
            });
        });

        describe('`generate` method', function () {
            /**
             * @param ?bool $format
             * @param ?string $prefix
             */
            $generateWithNamedOptionsInConstructor = function ($format = null, $prefix = null): string {
                $utils = new BrUtils(cpf: ['generator' => compact('format', 'prefix')]);

                return $utils->cpf->generate();
            };

            /**
             * @param ?bool $format
             * @param ?string $prefix
             */
            $generateWithNamedOptionsInMethod = function ($format = null, $prefix = null): string {
                $utils = new BrUtils();

                return $utils->cpf->generate($format, $prefix);
            };

            $generateContexts = [
                ['when options are passed to constructor as an array', $generateWithNamedOptionsInConstructor],
                ['when options are passed to the method as named arguments', $generateWithNamedOptionsInMethod],
            ];

            foreach ($generateContexts as $generateContext) {
                [$description, $generate] = $generateContext;

                describe($description, function () use ($generate) {
                    it('matches `CpfGenerator::generate` behavior', function () use ($generate) {
                        $generator = new CpfGenerator();

                        $result = $generate();

                        expect($result)->toMatch('/^\d{11}$/');
                        expect(strlen($result))->toBe(strlen($generator->generate()));
                    });

                    it('forwards generation options', function () use ($generate) {
                        $options = [
                            'format' => true,
                            'prefix' => '12345',
                        ];

                        $result = $generate(...$options);

                        expect($result)->toMatch('/^123\.45\d\.\d{3}-\d{2}$/');
                    });

                    it('returns a deterministic CPF for a full 9-character prefix', function () use ($generate) {
                        $prefix = '123456789';
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
    });

    describe('CNPJ utils', function () {
        describe('`format` method', function () {
            /**
             * @param string|list<string> $cnpj
             * @param ?string $slashKey
             */
            $formatWithNamedOptionsInConstructor = function ($cnpj, $slashKey = null): string {
                $utils = new BrUtils(cnpj: ['formatter' => ['slashKey' => $slashKey]]);

                return $utils->cnpj->format($cnpj);
            };

            /**
             * @param string|list<string> $cnpj
             * @param ?string $slashKey
             */
            $formatWithFormatterOptionsInConstructor = function (string $cnpj, $slashKey = null): string {
                $options = new CnpjFormatterOptions(slashKey: $slashKey);
                $utils = new BrUtils(cnpj: ['formatter' => $options]);

                return $utils->cnpj->format($cnpj);
            };

            /**
             * @param string|list<string> $cnpj
             * @param ?string $slashKey
             */
            $formatWithNamedOptionsInMethod = function (string $cnpj, $slashKey = null): string {
                $utils = new BrUtils();

                return $utils->cnpj->format($cnpj, slashKey: $slashKey);
            };

            /**
             * @param string|list<string> $cnpj
             * @param ?string $slashKey
             */
            $formatWithFormatterOptionsInMethod = function (string $cnpj, $slashKey = null): string {
                $utils = new BrUtils();
                $options = new CnpjFormatterOptions(slashKey: $slashKey);

                return $utils->cnpj->format($cnpj, $options);
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
                $utils = new BrUtils(
                    cnpj: [
                        'formatter' => [
                            'hidden' => true,
                            'hiddenKey' => '#',
                        ],
                    ],
                );

                $result = $utils->cnpj->format('12ABC34500DE99');

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
                $utils = new BrUtils(cnpj: ['generator' => compact('format', 'prefix', 'type')]);

                return $utils->cnpj->generate();
            };

            /**
             * @param ?bool $format
             * @param ?string $prefix
             * @param ?CnpjGenerationType $type
             */
            $generateWithGeneratorOptionsInConstructor = function ($format = null, $prefix = null, $type = null): string {
                $options = new CnpjGeneratorOptions(format: $format, prefix: $prefix, type: $type);
                $utils = new BrUtils(cnpj: ['generator' => $options]);

                return $utils->cnpj->generate();
            };

            /**
             * @param ?bool $format
             * @param ?string $prefix
             * @param ?CnpjGenerationType $type
             */
            $generateWithNamedOptionsInMethod = function ($format = null, $prefix = null, $type = null): string {
                $utils = new BrUtils();

                return $utils->cnpj->generate(format: $format, prefix: $prefix, type: $type);
            };

            /**
             * @param ?bool $format
             * @param ?string $prefix
             * @param ?CnpjGenerationType $type
             */
            $generateWithGeneratorOptionsInMethod = function ($format = null, $prefix = null, $type = null): string {
                $utils = new BrUtils();
                $options = new CnpjGeneratorOptions(format: $format, prefix: $prefix, type: $type);

                return $utils->cnpj->generate($options);
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
                $utils = new BrUtils(cnpj: ['validator' => compact('type', 'caseSensitive')]);

                return $utils->cnpj->isValid($cnpj);
            };

            /**
             * @param string|list<string> $cnpj
             * @param ?CnpjValidationType $type
             * @param ?bool $caseSensitive
             */
            $isValidWithValidatorOptionsInConstructor = function (string $cnpj, $type = null, $caseSensitive = null): bool {
                $options = new CnpjValidatorOptions(type: $type, caseSensitive: $caseSensitive);
                $utils = new BrUtils(cnpj: ['validator' => $options]);

                return $utils->cnpj->isValid($cnpj);
            };

            /**
             * @param string|list<string> $cnpj
             * @param ?CnpjValidationType $type
             * @param ?bool $caseSensitive
             */
            $isValidWithNamedOptionsInMethod = function (string $cnpj, $type = null, $caseSensitive = null): bool {
                $utils = new BrUtils();

                return $utils->cnpj->isValid($cnpj, type: $type, caseSensitive: $caseSensitive);
            };

            /**
             * @param string|list<string> $cnpj
             * @param ?CnpjValidationType $type
             * @param ?bool $caseSensitive
             */
            $isValidWithValidatorOptionsInMethod = function (string $cnpj, $type = null, $caseSensitive = null): bool {
                $utils = new BrUtils();
                $options = new CnpjValidatorOptions(type: $type, caseSensitive: $caseSensitive);

                return $utils->cnpj->isValid($cnpj, $options);
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
});
