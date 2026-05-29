<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests\Cnpj;

use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\CnpjGeneratorOptions;
use Lacus\BrUtils\Cnpj\Enums\CnpjType;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionPrefixInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionTypeInvalidException;

describe('CnpjGenerator', function () {
    describe('constructor', function () {
        describe('when called with no arguments', function () {
            it('creates an instance with default options', function () {
                $defaultOptions = new CnpjGeneratorOptions();

                $generator = new CnpjGenerator();

                expect($generator->getOptions()->getAll())->toBe($defaultOptions->getAll());
            });
        });

        describe('when called with a `CnpjGeneratorOptions` instance', function () {
            it('uses the provided instance directly', function () {
                $options = new CnpjGeneratorOptions(
                    format: true,
                    prefix: '12345678',
                    type: 'numeric',
                );

                $generator = new CnpjGenerator($options);

                expect($generator->getOptions())->toBe($options);
                expect($generator->getOptions()->getAll())->toBe($options->getAll());
            });

            it('mutates the instance and affects future generate calls', function () {
                $options = new CnpjGeneratorOptions(
                    format: false,
                    type: 'numeric',
                );
                $generator = new CnpjGenerator($options);

                $options->format = true;
                $options->type = CnpjType::Alphabetic;

                expect($generator->getOptions()->format)->toBe(true);
                expect($generator->getOptions()->type)->toBe(CnpjType::Alphabetic);
            });
        });

        describe('when called with named options', function () {
            it('creates a new `CnpjGeneratorOptions` instance from the provided values', function () {
                $options = [
                    'format' => true,
                    'prefix' => '12345',
                    'type' => CnpjType::Numeric,
                ];

                $generator = new CnpjGenerator(...$options);

                expect($generator->getOptions())->toBeInstanceOf(CnpjGeneratorOptions::class);
                expect($generator->getOptions()->format)->toBe(true);
                expect($generator->getOptions()->prefix)->toBe('12345');
                expect($generator->getOptions()->type)->toBe(CnpjType::Numeric);
            });
        });

        describe('when called with invalid options', function () {
            it('throws `CnpjGeneratorOptionPrefixInvalidException` for invalid prefix', function () {
                expect(function () {
                    new CnpjGenerator(prefix: '00000000');
                })->toThrow(CnpjGeneratorOptionPrefixInvalidException::class, 'CNPJ generator option "prefix" with value "00000000" is invalid. Zeroed base ID is not eligible.');
            });

            it('throws `CnpjGeneratorOptionTypeInvalidException` for invalid type', function () {
                expect(function () {
                    new CnpjGenerator(type: 'invalid');
                })->toThrow(CnpjGeneratorOptionTypeInvalidException::class, 'CNPJ generator option "type" accepts only the following values: "alphanumeric", "alphabetic", "numeric". Got "invalid".');
            });

            it('throws `CnpjGeneratorOptionsTypeError` for non-string prefix', function () {
                expect(function () {
                    new CnpjGenerator(prefix: 123);
                })->toThrow(CnpjGeneratorOptionsTypeError::class, 'CNPJ generator option "prefix" must be of type string. Got integer number.');
            });
        });
    });

    describe('`generate` method', function () {
        $generateWithNamedOptionsInConstructor = function ($format = null, $prefix = null, $type = null): string {
            $generator = new CnpjGenerator(format: $format, prefix: $prefix, type: $type);

            return $generator->generate();
        };

        $generateWithCnpjGeneratorOptionsInstanceInConstructor = function ($format = null, $prefix = null, $type = null): string {
            $options = new CnpjGeneratorOptions(format: $format, prefix: $prefix, type: $type);
            $generator = new CnpjGenerator($options);

            return $generator->generate();
        };

        $generateWithNamedOptionsInMethod = function ($format = null, $prefix = null, $type = null): string {
            $generator = new CnpjGenerator();

            return $generator->generate(format: $format, prefix: $prefix, type: $type);
        };

        $generateWithCnpjGeneratorOptionsInstanceInMethod = function ($format = null, $prefix = null, $type = null): string {
            $generator = new CnpjGenerator();
            $options = new CnpjGeneratorOptions(format: $format, prefix: $prefix, type: $type);

            return $generator->generate($options);
        };

        $generateContexts = [
            [
              'when options are passed to constructor as named arguments',
              $generateWithNamedOptionsInConstructor,
            ],
            [
              'when options are passed to constructor as CnpjGeneratorOptions instance',
              $generateWithCnpjGeneratorOptionsInstanceInConstructor,
            ],
            [
              'when options are passed to method as named arguments',
              $generateWithNamedOptionsInMethod,
            ],
            [
              'when options are passed to method as CnpjGeneratorOptions instance',
              $generateWithCnpjGeneratorOptionsInstanceInMethod,
            ],
        ];

        foreach ($generateContexts as $generateContext) {
            [$description, $generate] = $generateContext;

            describe($description, function () use ($generate) {
                $typeContexts = [
                    [CnpjType::Numeric, '\\d'],
                    [CnpjType::Alphabetic, '[A-Z]'],
                    [CnpjType::Alphanumeric, '[0-9A-Z]'],
                ];

                describe('when no options are passed', function () use ($generate) {
                    it('returns a 14-character string with only numbers and uppercase letters', function () use ($generate) {
                        for ($i = 0; $i < 100; $i++) {
                            $result = $generate();

                            expect($result)->toHaveLength(14);
                            expect($result)->not->toMatch('/[a-z]/');
                            expect($result)->not->toMatch('/[.\/-]/');
                            expect($result)->toMatch('/^[0-9A-Z]+$/');
                        }
                    });

                    it('contains 2 numeric check digits', function () use ($generate) {
                        for ($i = 0; $i < 100; $i++) {
                            $result = $generate();

                            expect($result)->toMatch('/\d{2}$/');
                        }
                    });

                    it('returns different values on successive calls', function () use ($generate) {
                        // In 100 calls, 1 value can repeat at most.
                        $expectedDifferentValues = 99;

                        /** @var list<string> $results */
                        $results = [];

                        for ($i = 0; $i < 100; $i++) {
                            $results[] = $generate();
                        }

                        $unique = array_unique($results);
                        $count = count($unique);

                        expect($count)->toBeGreaterThanOrEqual($expectedDifferentValues);
                    });
                });

                describe('when `format` option is `true`', function () use ($generate) {
                    it('returns an 18-character string with numbers, uppercase letters and punctuation', function () use ($generate) {
                        for ($i = 0; $i < 100; $i++) {
                            $result = $generate(format: true);

                            expect($result)->toHaveLength(18);
                            expect($result)->not->toMatch('/[a-z]/');
                            expect($result)->toMatch('/[.\/-]/');
                            expect($result)->toMatch('/[0-9A-Z]{2,4}/');
                        }
                    });

                    it('contains 2 numeric check digits', function () use ($generate) {
                        for ($i = 0; $i < 100; $i++) {
                            $result = $generate(format: true);

                            expect($result)->toMatch('/\d{2}$/');
                        }
                    });

                    it('returns a string with standard CNPJ formatting', function () use ($generate) {
                        for ($i = 0; $i < 100; $i++) {
                            $result = $generate(format: true);

                            expect($result)->toMatch(
                                '/^[0-9A-Z]{2}\.[0-9A-Z]{3}\.[0-9A-Z]{3}\/[0-9A-Z]{4}-[0-9A-Z]{2}$/i',
                            );
                        }
                    });

                    it('returns different values on successive calls', function () use ($generate) {
                        // In 100 calls, 1 value can repeat at most.
                        $expectedDifferentValues = 99;

                        /** @var list<string> $results */
                        $results = [];

                        for ($i = 0; $i < 100; $i++) {
                            $results[] = $generate(format: true);
                        }

                        $unique = array_unique($results);
                        $count = count($unique);

                        expect($count)->toBeGreaterThanOrEqual($expectedDifferentValues);
                    });
                });

                describe('when `prefix` option is passed', function () use ($generate) {
                    it('returns a 14-character string with prefix "%s"', function (string $prefix) use ($generate) {
                        for ($i = 0; $i < 100; $i++) {
                            $result = $generate(prefix: $prefix);

                            expect($result)->toHaveLength(14);
                            expect($result)->toMatch('/^[0-9A-Z]+$/');
                            expect($result)->toMatch('/^' . preg_quote($prefix, '/') . '/');
                        }
                    })->with([
                        '1',
                        '12',
                        '123',
                        '1234',
                        '12345',
                        '123456',
                        '1234567',
                        '12345678',
                        '123456789',
                        '1234567890',
                        '12345678910',
                        '123456780009',
                        'A',
                        'AB',
                        'ABC',
                        'ABCD',
                        'ABCDE',
                        'ABCDEF',
                        'ABCDEFG',
                        'ABCDEFGH',
                        'ABCDEFGHI',
                        'ABCDEFGHIJ',
                        'ABCDEFGHIJK',
                        'ABCDEFGHIJKL',
                        'AB123CDE0001',
                    ]);

                    it('ignores characters after the 12th position with numeric prefix', function () use ($generate) {
                        $prefix = '123456780009';

                        $result = $generate(prefix: "{$prefix}XY");

                        expect($result)->toHaveLength(14);
                        expect($result)->not->toMatch('/XY$/');
                        expect($result)->toMatch('/^' . preg_quote($prefix, '/') . '\d{2}$/');
                    });

                    it('ignores characters after the 12th position with alphabetic prefix', function () use ($generate) {
                        $prefix = 'ABCDEFGHIJKL';

                        $result = $generate(prefix: "{$prefix}XY");

                        expect($result)->toHaveLength(14);
                        expect($result)->not->toMatch('/XY$/');
                        expect($result)->toMatch('/^' . preg_quote($prefix, '/') . '\d{2}$/');
                    });

                    it('ignores characters after the 12th position with alphanumeric prefix', function () use ($generate) {
                        $prefix = 'AB123CDE0001';

                        $result = $generate(prefix: "{$prefix}XY");

                        expect($result)->toHaveLength(14);
                        expect($result)->not->toMatch('/XY$/');
                        expect($result)->toMatch('/^' . preg_quote($prefix, '/') . '\d{2}$/');
                    });

                    it('always generates the same CNPJ with the same 12-character numeric prefix', function () use ($generate) {
                        $prefix = '123456780009';

                        /** @var list<string> $results */
                        $results = [];

                        for ($i = 0; $i < 100; $i++) {
                            $results[] = $generate(prefix: $prefix);
                        }

                        $unique = array_unique($results);
                        $count = count($unique);

                        expect($count)->toBe(1);
                    });

                    it('always generates the same CNPJ with the same 12-character alphabetic prefix', function () use ($generate) {
                        $prefix = 'ABCDEFGHIJKL';

                        /** @var list<string> $results */
                        $results = [];

                        for ($i = 0; $i < 100; $i++) {
                            $results[] = $generate(prefix: $prefix);
                        }

                        $unique = array_unique($results);
                        $count = count($unique);

                        expect($count)->toBe(1);
                    });

                    it('always generates the same CNPJ with the same 12-character alphanumeric prefix', function () use ($generate) {
                        $prefix = 'AB123CDE0001';

                        /** @var list<string> $results */
                        $results = [];

                        for ($i = 0; $i < 100; $i++) {
                            $results[] = $generate(prefix: $prefix);
                        }

                        $unique = array_unique($results);
                        $count = count($unique);

                        expect($count)->toBe(1);
                    });

                    it('strips non-alphanumeric characters from prefix before generating', function () use ($generate) {
                        $result = $generate(
                            prefix: 'AB.12.CDE/0001',
                            format: false,
                        );

                        expect($result)->toMatch('/^AB12CDE0001/');
                    });
                });

                foreach ($typeContexts as $typeContext) {
                    [$type, $pattern] = $typeContext;

                    describe("when `type` option is \"{$type->value}\"", function () use ($generate, $type, $pattern) {
                        it('returns a 14-character string', function () use ($generate, $pattern, $type) {
                            for ($i = 0; $i < 100; $i++) {
                                $result = $generate(type: $type);

                                expect($result)->toHaveLength(14);
                                expect($result)->not->toMatch('/[a-z]/');
                                expect($result)->not->toMatch('/[.\/-]/');
                                expect($result)->toMatch('/^' . $pattern . '{12}\d{2}$/');
                            }
                        });

                        it('returns different values on successive calls', function () use ($generate, $type) {
                            // In 100 calls, 2 value can repeat at most.
                            $expectedDifferentValues = 98;

                            /** @var list<string> $results */
                            $results = [];

                            for ($i = 0; $i < 100; $i++) {
                                $results[] = $generate(type: $type);
                            }

                            $unique = array_unique($results);
                            $count = count($unique);

                            expect($count)->toBeGreaterThanOrEqual($expectedDifferentValues);
                        });
                    });
                }

                describe('when different options are passed', function () use ($generate, $typeContexts) {
                    describe('when `format = true` and `prefix = "AB123CDE000"`', function () use ($generate) {
                        it('returns an 18-character CNPJ', function () use ($generate) {
                            $result = $generate(
                                format: true,
                                prefix: 'AB123CDE000',
                            );

                            expect($result)->toHaveLength(18);
                            expect($result)->not->toMatch('/[a-z]/');
                            expect($result)->toMatch('/^AB\.123\.CDE\/000[0-9A-Z]-\d{2}$/');
                        });
                    });

                    foreach ($typeContexts as $typeContext) {
                        [$type, $pattern] = $typeContext;

                        describe("when `format = true` and `type = \"{$type->value}\"`", function () use ($generate, $type, $pattern) {
                            it('returns an 18-character CNPJ', function () use ($generate, $type, $pattern) {
                                $result = $generate(
                                    format: true,
                                    type: $type,
                                );

                                expect($result)->toHaveLength(18);
                                expect($result)->not->toMatch('/[a-z]/');
                                expect($result)->toMatch("/^{$pattern}{2}\.{$pattern}{3}\.{$pattern}{3}\/{$pattern}{4}-\d{2}$/");
                            });
                        });

                        describe("when `prefix = \"AB123CDE\"` and `type = \"{$type->value}\"`", function () use ($generate, $type, $pattern) {
                            it('returns a 14-character CNPJ', function () use ($generate, $type, $pattern) {
                                $result = $generate(
                                    prefix: 'AB123CDE',
                                    type: $type,
                                );

                                expect($result)->toHaveLength(14);
                                expect($result)->not->toMatch('/[a-z]/');
                                expect($result)->not->toMatch('/[.\/-]/');
                                expect($result)->toMatch("/^AB123CDE{$pattern}{4}\d{2}$/");
                            });
                        });

                        describe("when `format = true`, `prefix = \"AB123CDE\"` and `type = \"{$type->value}\"`", function () use ($generate, $type, $pattern) {
                            it('returns an 18-character CNPJ', function () use ($generate, $type, $pattern) {
                                $result = $generate(format: true, prefix: 'AB123CDE', type: $type);

                                expect($result)->toHaveLength(18);
                                expect($result)->not->toMatch('/[a-z]/');
                                expect($result)->toMatch("/^AB\.123\.CDE\/{$pattern}{4}-\d{2}$/");
                            });
                        });
                    }
                });
            });
        }
    });
});
