<?php

declare(strict_types=1);

use Lacus\BrUtils\Cnpj\CnpjGeneratorOptions;
use Lacus\BrUtils\Cnpj\Enums\CnpjType;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionPrefixInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionTypeInvalidException;
use Throwable;

describe('CnpjGeneratorOptions', function () {
    $defaultParameters = [
        'format' => CnpjGeneratorOptions::DEFAULT_FORMAT,
        'prefix' => CnpjGeneratorOptions::DEFAULT_PREFIX,
        'type' => CnpjGeneratorOptions::DEFAULT_TYPE,
    ];

    describe('constructor', function () use ($defaultParameters) {
        describe('when called with no parameters', function () use ($defaultParameters) {
            it('sets all options to default values', function () use ($defaultParameters) {
                $options = new CnpjGeneratorOptions();

                expect($options->getAll())->toBe($defaultParameters);
            });
        });

        describe('when called with all parameters with `null` values', function () use ($defaultParameters) {
            it('sets all options to default values', function () use ($defaultParameters) {
                $options = new CnpjGeneratorOptions(
                    format: null,
                    prefix: null,
                    type: null,
                );

                expect($options->getAll())->toBe($defaultParameters);
            });
        });

        describe('when called with all parameters', function () {
            it('sets all options to the provided values', function () {
                $parameters = [
                    'format' => true,
                    'prefix' => '12345',
                    'type' => CnpjType::Numeric,
                ];

                $options = new CnpjGeneratorOptions(...$parameters);

                expect($options->getAll())->toBe($parameters);
            });
        });

        describe('when called with some parameters', function () use ($defaultParameters) {
            it('sets only the provided non-nullish values', function () use ($defaultParameters) {
                $options = new CnpjGeneratorOptions(
                    prefix: null,
                    type: CnpjType::Numeric,
                );

                expect($options->getAll())->toBe([
                    ...$defaultParameters,
                    'type' => CnpjType::Numeric,
                ]);
            });
        });

        describe('when called with overrides parameters', function () {
            it('uses last param option with 2 params', function () {
                $options = new CnpjGeneratorOptions(
                    overrides: [
                        ['prefix' => '12345'],
                        ['prefix' => '11222333'],
                    ],
                );

                expect($options->prefix)->toBe('11222333');
            });

            it('uses last param option with 1 array and 1 object instance', function () {
                $options = new CnpjGeneratorOptions(
                    overrides: [
                        ['prefix' => '12345'],
                        new CnpjGeneratorOptions(prefix: '11222333'),
                    ],
                );

                expect($options->prefix)->toBe('11222333');
            });

            it('uses last param option with 5 params', function () {
                $options = new CnpjGeneratorOptions(
                    overrides: [
                        ['prefix' => '11111111'],
                        new CnpjGeneratorOptions(prefix: '22222222'),
                        ['prefix' => '33333333'],
                        new CnpjGeneratorOptions(prefix: '44444444'),
                        ['prefix' => '55555555'],
                    ],
                );

                expect($options->prefix)->toBe('55555555');
            });
        });
    });

    describe('`format` property', function () use ($defaultParameters) {
        describe('when setting to a boolean value', function () {
            it('sets `format` to `true`', function () {
                $options = new CnpjGeneratorOptions(format: false);

                $options->format = true;

                expect($options->format)->toBeTrue();
            });

            it('sets `format` to `false`', function () {
                $options = new CnpjGeneratorOptions(format: true);

                $options->format = false;

                expect($options->format)->toBeFalse();
            });
        });

        describe('when setting to a nullish value', function () use ($defaultParameters) {
            it('sets default value for `null`', function () use ($defaultParameters) {
                $options = new CnpjGeneratorOptions(format: !CnpjGeneratorOptions::DEFAULT_FORMAT);

                $options->format = null;

                expect($options->format)->toBe($defaultParameters['format']);
            });
        });

        describe('when setting to a non-boolean value', function () {
            it('coerces object value to `true`', function () {
                $options = new CnpjGeneratorOptions(format: false);

                $options->format = (object) ['not' => 'a boolean'];

                expect($options->format)->toBeTrue();
            });

            it('coerces truthy string value to `true`', function () {
                $options = new CnpjGeneratorOptions(format: false);

                $options->format = 'not a boolean';

                expect($options->format)->toBeTrue();
            });

            it('coerces truthy number value to `true`', function () {
                $options = new CnpjGeneratorOptions(format: false);

                $options->format = 123;

                expect($options->format)->toBeTrue();
            });

            it('coerces empty string value to `false`', function () {
                $options = new CnpjGeneratorOptions(format: false);

                $options->format = '';

                expect($options->format)->toBeFalse();
            });

            it('coerces zero number value to `false`', function () {
                $options = new CnpjGeneratorOptions(format: false);

                $options->format = 0;

                expect($options->format)->toBeFalse();
            });
        });
    });

    describe('`prefix` property', function () use ($defaultParameters) {
        describe('when setting to a string value', function () {
            it('sets `prefix` to the provided value', function () {
                $options = new CnpjGeneratorOptions(prefix: '12345');

                $options->prefix = '11222333';

                expect($options->prefix)->toBe('11222333');
            });
        });

        describe('when setting to a nullish value', function () use ($defaultParameters) {
            it('sets default value for `null`', function () use ($defaultParameters) {
                $options = new CnpjGeneratorOptions(prefix: '12345');

                $options->prefix = null;

                expect($options->prefix)->toBe($defaultParameters['prefix']);
            });
        });

        describe('when setting to a non-string value', function () {
            it('throws `CnpjGeneratorOptionsTypeError` with an object', function () {
                $options = new CnpjGeneratorOptions();

                expect(function () use ($options) {
                    $options->prefix = (object) ['not' => 'a string'];
                })->toThrow(CnpjGeneratorOptionsTypeError::class, 'CNPJ generator option "prefix" must be of type string. Got object.');
            });

            it('throws `CnpjGeneratorOptionsTypeError` with a number', function () {
                $options = new CnpjGeneratorOptions();

                expect(function () use ($options) {
                    $options->prefix = 123;
                })->toThrow(CnpjGeneratorOptionsTypeError::class, 'CNPJ generator option "prefix" must be of type string. Got integer number.');
            });

            it('throws `CnpjGeneratorOptionsTypeError` with a boolean', function () {
                $options = new CnpjGeneratorOptions();

                expect(function () use ($options) {
                    $options->prefix = true;
                })->toThrow(CnpjGeneratorOptionsTypeError::class, 'CNPJ generator option "prefix" must be of type string. Got boolean.');
            });
        });

        describe('when setting to an invalid string', function () {
            it("throws `CnpjGeneratorOptionPrefixInvalidException` with base ID all zeros", function () {
                $options = new CnpjGeneratorOptions();

                expect(function () use ($options) {
                    $options->prefix = '00000000';
                })->toThrow(CnpjGeneratorOptionPrefixInvalidException::class, 'CNPJ generator option "prefix" with value "00000000" is invalid. Zeroed base ID is not eligible.');
            });

            it("throws `CnpjGeneratorOptionPrefixInvalidException` with branch ID all zeros", function () {
                $options = new CnpjGeneratorOptions();

                expect(function () use ($options) {
                    $options->prefix = '123456780000';
                })->toThrow(CnpjGeneratorOptionPrefixInvalidException::class, 'CNPJ generator option "prefix" with value "123456780000" is invalid. Zeroed branch ID is not eligible.');
            });

            it("throws `CnpjGeneratorOptionPrefixInvalidException` with repeated digits", function (string $prefix) {
                $options = new CnpjGeneratorOptions();

                expect(function () use ($options, $prefix) {
                    $options->prefix = $prefix;
                })->toThrow(CnpjGeneratorOptionPrefixInvalidException::class, "CNPJ generator option \"prefix\" with value \"{$prefix}\" is invalid. Repeated digits are not considered valid.");
            })->with([
                '111111111111',
                '222222222222',
                '333333333333',
                '444444444444',
                '555555555555',
                '666666666666',
                '777777777777',
                '888888888888',
                '999999999999',
            ]);

            it("does not throw exception with repeated letters", function (string $prefix) {
                $options = new CnpjGeneratorOptions();

                expect(function () use ($options, $prefix) {
                    $options->prefix = $prefix;
                })->not->toThrow(Throwable::class);
            })->with([
                'AAAAAAAAAAAA',
                'BBBBBBBBBBBB',
                'CCCCCCCCCCCC',
                'DDDDDDDDDDDD',
                'EEEEEEEEEEEE',
                'FFFFFFFFFFFF',
                'GGGGGGGGGGGG',
                'HHHHHHHHHHHH',
                'IIIIIIIIIIII',
                'JJJJJJJJJJJJ',
                'KKKKKKKKKKKK',
                'LLLLLLLLLLLL',
                'MMMMMMMMMMMM',
                'NNNNNNNNNNNN',
                'OOOOOOOOOOOO',
                'PPPPPPPPPPPP',
                'QQQQQQQQQQQQ',
                'RRRRRRRRRRRR',
                'SSSSSSSSSSSS',
                'TTTTTTTTTTTT',
                'UUUUUUUUUUUU',
                'VVVVVVVVVVVV',
                'WWWWWWWWWWWW',
                'XXXXXXXXXXXX',
                'YYYYYYYYYYYY',
                'ZZZZZZZZZZZZ',
            ]);
        });
    });

    describe('`type` property', function () use ($defaultParameters) {
        describe('when setting to a `CnpjType` enum', function () {
            it("sets `type` to the `CnpjType::Alphanumeric` value", function () {
                $options = new CnpjGeneratorOptions(type: CnpjType::Alphabetic);

                $options->type = CnpjType::Alphanumeric;

                expect($options->type)->toBe(CnpjType::Alphanumeric);
            });

            it("sets `type` to the 'CnpjType::Alphabetic' value", function () {
                $options = new CnpjGeneratorOptions(type: CnpjType::Numeric);

                $options->type = CnpjType::Alphabetic;

                expect($options->type)->toBe(CnpjType::Alphabetic);
            });

            it("sets `type` to the 'CnpjType::Numeric' value", function () {
                $options = new CnpjGeneratorOptions(type: CnpjType::Alphanumeric);

                $options->type = CnpjType::Numeric;

                expect($options->type)->toBe(CnpjType::Numeric);
            });
        });

        describe('when setting to a string value', function () {
            it('sets `type` to the "alphanumeric" value', function () {
                $options = new CnpjGeneratorOptions(type: 'alphabetic');

                $options->type = 'alphanumeric';

                expect($options->type)->toBe(CnpjType::Alphanumeric);
            });

            it('sets `type` to the "alphabetic" value', function () {
                $options = new CnpjGeneratorOptions(type: 'numeric');

                $options->type = 'alphabetic';

                expect($options->type)->toBe(CnpjType::Alphabetic);
            });

            it('sets `type` to the "numeric" value', function () {
                $options = new CnpjGeneratorOptions(type: 'alphanumeric');

                $options->type = 'numeric';

                expect($options->type)->toBe(CnpjType::Numeric);
            });
        });

        describe('when setting to a nullish value', function () use ($defaultParameters) {
            it('sets default value for `null`', function () use ($defaultParameters) {
                $options = new CnpjGeneratorOptions(type: 'numeric');

                $options->type = null;

                expect($options->type)->toBe($defaultParameters['type']);
            });
        });

        describe('when setting to an invalid string value', function () {
            it('throws `CnpjGeneratorOptionTypeInvalidException` with an invalid string value', function () {
                $options = new CnpjGeneratorOptions();

                expect(function () use ($options) {
                    $options->type = 'invalid';
                })->toThrow(CnpjGeneratorOptionTypeInvalidException::class, 'CNPJ generator option "type" accepts only the following values: "alphanumeric", "alphabetic", "numeric". Got "invalid".');
            });
        });

        describe('when setting to an invalid value type', function () {
            it('throws `CnpjGeneratorOptionsTypeError` with an object', function () {
                $options = new CnpjGeneratorOptions();

                expect(function () use ($options) {
                    $options->type = (object) ['not' => 'a string'];
                })->toThrow(CnpjGeneratorOptionsTypeError::class, 'CNPJ generator option "type" must be of type CnpjType or string. Got object.');
            });

            it('throws `CnpjGeneratorOptionsTypeError` with a number', function () {
                $options = new CnpjGeneratorOptions();

                expect(function () use ($options) {
                    $options->type = 123;
                })->toThrow(CnpjGeneratorOptionsTypeError::class, 'CNPJ generator option "type" must be of type CnpjType or string. Got integer number.');
            });

            it('throws `CnpjGeneratorOptionsTypeError` with a boolean', function () {
                $options = new CnpjGeneratorOptions();

                expect(function () use ($options) {
                    $options->type = true;
                })->toThrow(CnpjGeneratorOptionsTypeError::class, 'CNPJ generator option "type" must be of type CnpjType or string. Got boolean.');
            });
        });
    });

    describe('`getAll` method', function () {
        it('returns the all properties with expected types', function () {
            $all = (new CnpjGeneratorOptions())->getAll();

            expect($all['format'])->toBeBool();
            expect($all['prefix'])->toBeString();
            expect($all['type'])->toBeInstanceOf(CnpjType::class);
        });
    });
});
