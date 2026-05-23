<?php

declare(strict_types=1);

use Lacus\BrUtils\Cnpj\CnpjValidatorOptions;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionTypeInvalidException;

describe('CnpjValidatorOptions', function () {
    $defaultParameters = [
        'type' => CnpjValidatorOptions::DEFAULT_TYPE,
        'caseSensitive' => CnpjValidatorOptions::DEFAULT_CASE_SENSITIVE,
    ];

    describe('constructor', function () use ($defaultParameters) {
        describe('when called with no parameters', function () use ($defaultParameters) {
            it('sets all options to default values', function () use ($defaultParameters) {
                $options = new CnpjValidatorOptions();

                expect($options->getAll())->toBe($defaultParameters);
            });
        });

        describe('when called with all parameters with null values', function () use ($defaultParameters) {
            it('sets all options to default values', function () use ($defaultParameters) {
                $options = new CnpjValidatorOptions(
                    type: null,
                    caseSensitive: null,
                );

                expect($options->getAll())->toBe($defaultParameters);
            });
        });

        describe('when called with all parameters', function () {
            it('sets all options to the provided values', function () {
                $parameters = [
                    'type' => CnpjValidationType::Numeric,
                    'caseSensitive' => false,
                ];

                $options = new CnpjValidatorOptions(...$parameters);

                expect($options->getAll())->toBe($parameters);
            });
        });

        describe('when called with some parameters', function () use ($defaultParameters) {
            it('sets only the provided non-nullish values', function () use ($defaultParameters) {
                $options = new CnpjValidatorOptions(
                    type: CnpjValidationType::Numeric,
                );

                expect($options->getAll())->toBe([
                    ...$defaultParameters,
                    'type' => CnpjValidationType::Numeric,
                ]);
            });
        });

        describe('when called with overrides parameters', function () {
            it('uses last param option with 2 params', function () {
                $options = new CnpjValidatorOptions(
                    overrides: [
                        ['caseSensitive' => false],
                        ['caseSensitive' => true],
                    ],
                );

                expect($options->caseSensitive)->toBeTrue();
            });

            it('uses last param option with 1 array and 1 object instance', function () {
                $options = new CnpjValidatorOptions(
                    overrides: [
                        ['caseSensitive' => false],
                        new CnpjValidatorOptions(caseSensitive: true),
                    ],
                );

                expect($options->caseSensitive)->toBeTrue();
            });

            it('uses last param option with 5 params', function () {
                $options = new CnpjValidatorOptions(
                    overrides: [
                        ['caseSensitive' => false],
                        new CnpjValidatorOptions(caseSensitive: true),
                        ['caseSensitive' => false],
                        new CnpjValidatorOptions(caseSensitive: true),
                        ['caseSensitive' => false],
                    ],
                );

                expect($options->caseSensitive)->toBeFalse();
            });
        });
    });

    describe('`caseSensitive` property', function () use ($defaultParameters) {
        describe('when setting to a boolean value', function () {
            it('sets `caseSensitive` to `true`', function () {
                $options = new CnpjValidatorOptions(caseSensitive: false);

                $options->caseSensitive = true;

                expect($options->caseSensitive)->toBeTrue();
            });

            it('sets `caseSensitive` to `false`', function () {
                $options = new CnpjValidatorOptions(caseSensitive: true);

                $options->caseSensitive = false;

                expect($options->caseSensitive)->toBeFalse();
            });
        });

        describe('when setting to a nullish value', function () use ($defaultParameters) {
            it('sets default value for `null`', function () use ($defaultParameters) {
                $options = new CnpjValidatorOptions(caseSensitive: !CnpjValidatorOptions::DEFAULT_CASE_SENSITIVE);

                $options->caseSensitive = null;

                expect($options->caseSensitive)->toBe($defaultParameters['caseSensitive']);
            });
        });

        describe('when setting to a non-boolean value', function () {
            it('coerces object value to `true`', function () {
                $options = new CnpjValidatorOptions(caseSensitive: false);

                $options->caseSensitive = (object) ['not' => 'a boolean'];

                expect($options->caseSensitive)->toBeTrue();
            });

            it('coerces truthy string value to `true`', function () {
                $options = new CnpjValidatorOptions(caseSensitive: false);

                $options->caseSensitive = 'not a boolean';

                expect($options->caseSensitive)->toBeTrue();
            });

            it('coerces truthy number value to `true`', function () {
                $options = new CnpjValidatorOptions(caseSensitive: false);

                $options->caseSensitive = 123;

                expect($options->caseSensitive)->toBeTrue();
            });

            it('coerces empty string value to `false`', function () {
                $options = new CnpjValidatorOptions(caseSensitive: false);

                $options->caseSensitive = '';

                expect($options->caseSensitive)->toBeFalse();
            });

            it('coerces zero number value to `false`', function () {
                $options = new CnpjValidatorOptions(caseSensitive: false);

                $options->caseSensitive = 0;

                expect($options->caseSensitive)->toBeFalse();
            });
        });
    });

    describe('`type` property', function () use ($defaultParameters) {
        describe('when setting to a `CnpjValidationType` enum', function () {
            it("sets `type` to the `CnpjValidationType::Alphanumeric` value", function () {
                $options = new CnpjValidatorOptions(type: CnpjValidationType::Numeric);

                $options->type = CnpjValidationType::Alphanumeric;

                expect($options->type)->toBe(CnpjValidationType::Alphanumeric);
            });

            it("sets `type` to the 'CnpjType::Numeric' value", function () {
                $options = new CnpjValidatorOptions(type: CnpjValidationType::Alphanumeric);

                $options->type = CnpjValidationType::Numeric;

                expect($options->type)->toBe(CnpjValidationType::Numeric);
            });
        });

        describe('when setting to a string value', function () {
            it('sets `type` to the "alphanumeric" value', function () {
                $options = new CnpjValidatorOptions(type: CnpjValidationType::Numeric);

                $options->type = 'alphanumeric';

                expect($options->type)->toBe(CnpjValidationType::Alphanumeric);
            });

            it('sets `type` to the "numeric" value', function () {
                $options = new CnpjValidatorOptions(type: 'alphanumeric');

                $options->type = 'numeric';

                expect($options->type)->toBe(CnpjValidationType::Numeric);
            });
        });

        describe('when setting to a nullish value', function () use ($defaultParameters) {
            it('sets default value for `null`', function () use ($defaultParameters) {
                $options = new CnpjValidatorOptions(type: 'numeric');

                $options->type = null;

                expect($options->type)->toBe($defaultParameters['type']);
            });
        });

        describe('when setting to an invalid string value', function () {
            it('throws `CnpjValidatorOptionTypeInvalidException` with an invalid string value', function () {
                $options = new CnpjValidatorOptions();

                expect(function () use ($options) {
                    $options->type = 'invalid';
                })->toThrow(CnpjValidatorOptionTypeInvalidException::class, 'CNPJ validator option "type" accepts only the following values: "alphanumeric", "numeric". Got "invalid".');
            });
        });

        describe('when setting to an invalid value type', function () {
            it('throws `CnpjValidatorOptionsTypeError` with an object', function () {
                $options = new CnpjValidatorOptions();

                expect(function () use ($options) {
                    $options->type = (object) ['not' => 'a string'];
                })->toThrow(CnpjValidatorOptionsTypeError::class, 'CNPJ validator option "type" must be of type CnpjValidationType or string. Got object.');
            });

            it('throws `CnpjValidatorOptionsTypeError` with a number', function () {
                $options = new CnpjValidatorOptions();

                expect(function () use ($options) {
                    $options->type = 123;
                })->toThrow(CnpjValidatorOptionsTypeError::class, 'CNPJ validator option "type" must be of type CnpjValidationType or string. Got integer number.');
            });

            it('throws `CnpjValidatorOptionsTypeError` with a boolean', function () {
                $options = new CnpjValidatorOptions();

                expect(function () use ($options) {
                    $options->type = true;
                })->toThrow(CnpjValidatorOptionsTypeError::class, 'CNPJ validator option "type" must be of type CnpjValidationType or string. Got boolean.');
            });
        });
    });

    describe('`getAll` method', function () {
        it('returns the all properties with expected types', function () {
            $all = (new CnpjValidatorOptions())->getAll();

            expect($all['caseSensitive'])->toBeBool();
            expect($all['type'])->toBeInstanceOf(CnpjValidationType::class);
        });
    });
});
