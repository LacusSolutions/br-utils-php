<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests\Cnpj;

use Lacus\BrUtils\Cnpj\CnpjValidator;
use Lacus\BrUtils\Cnpj\CnpjValidatorOptions;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorInputTypeError;
use stdClass;

describe('CnpjValidator', function () {
    describe('constructor', function () {
        describe('when called with no arguments', function () {
            it('creates an instance with default options', function () {
                $defaultOptions = new CnpjValidatorOptions();

                $Validator = new CnpjValidator();

                expect($Validator->getOptions()->getAll())->toBe($defaultOptions->getAll());
            });
        });

        describe('when called with arguments', function () {
            it('uses the provided options instance', function () {
                $options = new CnpjValidatorOptions();

                $Validator = new CnpjValidator($options);

                expect($Validator->getOptions())->toBe($options);
            });

            it('overrides the default options with the provided ones (named arguments)', function () {
                $options = [
                    'type' => CnpjValidationType::Numeric,
                    'caseSensitive' => false,
                ];

                $Validator = new CnpjValidator(...$options);

                expect($Validator->getOptions()->getAll())->toMatchArray($options);
            });

            it('overrides the default options with the provided ones (`CnpjValidatorOptions` instance)', function () {
                $options = new CnpjValidatorOptions(
                    type: CnpjValidationType::Numeric,
                    caseSensitive: true,
                );

                $Validator = new CnpjValidator($options);

                expect($Validator->getOptions()->getAll())->toBe($options->getAll());
            });
        });
    });

    describe('`isValid` method', function () {
        $isValidWithNamedOptionsInConstructor = function ($cnpj, $type = null, $caseSensitive = null): bool {
            $validator = new CnpjValidator(type: $type, caseSensitive: $caseSensitive);

            return $validator->isValid($cnpj);
        };

        $isValidWithCnpjValidatorOptionsInstanceInConstructor = function ($cnpj, $type = null, $caseSensitive = null): bool {
            $options = new CnpjValidatorOptions(type: $type, caseSensitive: $caseSensitive);
            $validator = new CnpjValidator($options);

            return $validator->isValid($cnpj);
        };

        $isValidWithNamedOptionsInMethod = function ($cnpj, $type = null, $caseSensitive = null): bool {
            $validator = new CnpjValidator();

            return $validator->isValid($cnpj, type: $type, caseSensitive: $caseSensitive);
        };

        $isValidWithCnpjValidatorOptionsInstanceInMethod = function ($cnpj, $type = null, $caseSensitive = null): bool {
            $validator = new CnpjValidator();
            $options = new CnpjValidatorOptions(type: $type, caseSensitive: $caseSensitive);

            return $validator->isValid($cnpj, $options);
        };

        $isValidContexts = [
            [
              'when options are passed to constructor as named arguments',
              $isValidWithNamedOptionsInConstructor,
            ],
            [
              'when options are passed to constructor as `CnpjValidatorOptions` instance',
              $isValidWithCnpjValidatorOptionsInstanceInConstructor,
            ],
            [
              'when options are passed to method as named arguments',
              $isValidWithNamedOptionsInMethod,
            ],
            [
              'when options are passed to method as `CnpjValidatorOptions` instance',
              $isValidWithCnpjValidatorOptionsInstanceInMethod,
            ],
        ];

        /**
         * @return array<string, string|list<string>>
         */
        function create_inputs_set(string $cnpj)
        {
            $unformattedString = $cnpj;
            $formattedString = preg_replace(
                '/([0-9A-Z]{2})([0-9A-Z]{3})([0-9A-Z]{3})([0-9A-Z]{4})(\d+)/i',
                '$1.$2.$3/$4-$5',
                $cnpj,
            ) ?? '';
            $unformattedArray = str_split($unformattedString);
            $formattedArray = str_split($formattedString);
            $groupedArray = preg_split('/[.\/\-]/', $formattedString) ?: [];

            return [
              'string'           => $unformattedString,
              'formatted string' => $formattedString,
              'array'            => $unformattedArray,
              'formatted array'  => $formattedArray,
              'grouped array'    => $groupedArray,
            ];
        }

        foreach ($isValidContexts as $isValidContext) {
            [$description, $isValid] = $isValidContext;

            describe($description, function () use ($isValid) {
                describe('when no options are passed', function () use ($isValid) {
                    $inputsSet = create_inputs_set('1QB5UKALPYFP59');

                    foreach ($inputsSet as $type => $input) {
                        it("returns `true` for a valid CNPJ {$type} with numbers and uppercase letters", function () use ($isValid, $input) {
                            $result = $isValid($input);

                            expect($result)->toBeTrue();
                        });
                    }

                    $inputsSet = create_inputs_set('96206256120884');

                    foreach ($inputsSet as $type => $input) {
                        it("returns `true` for a valid CNPJ {$type} with only numbers", function () use ($isValid, $input) {
                            $result = $isValid($input);

                            expect($result)->toBeTrue();
                        });
                    }

                    $inputsSet = create_inputs_set('AB123CDE00015');

                    foreach ($inputsSet as $type => $input) {
                        it("returns `false` for a CNPJ {$type} with less than 14 digits", function () use ($isValid, $input) {
                            $result = $isValid($input);

                            expect($result)->toBeFalse();
                        });
                    }

                    $inputsSet = create_inputs_set('AB123CDE0001555');

                    foreach ($inputsSet as $type => $input) {
                        it("returns `false` for a CNPJ {$type} with more than 14 digits", function () use ($isValid, $input) {
                            $result = $isValid($input);

                            expect($result)->toBeFalse();
                        });
                    }

                    it('returns `false` for a CNPJ with base ID "00000000"', function () use ($isValid) {
                        for ($i = 0; $i < 100; $i++) {
                            $input = '00000000' . 'A001' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);

                            $result = $isValid($input);

                            expect($result)->toBeFalse();
                        }
                    });

                    it('returns `false` for a CNPJ with branch ID "0000"', function () use ($isValid) {
                        for ($i = 0; $i < 100; $i++) {
                            $input = 'AB123CDE' . '0000' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);

                            $result = $isValid($input);

                            expect($result)->toBeFalse();
                        }
                    });

                    it('returns `false` for a CNPJ with all digits the same', function (string $prefix) use ($isValid) {
                        for ($i = 0; $i < 100; $i++) {
                            $input = $prefix . str_pad((string) $i, 2, '0', STR_PAD_LEFT);

                            $result = $isValid($input);

                            expect($result)->toBeFalse();
                        }
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
                });

                describe('when `caseSensitive` option is `false`', function () use ($isValid) {
                    $inputsSet = create_inputs_set('1QB5UKALPYFP59');

                    foreach ($inputsSet as $type => $input) {
                        it("returns `true` for a valid CNPJ {$type} with numbers and lowercase letters", function () use ($isValid, $input) {
                            $result = $isValid($input, caseSensitive: false);

                            expect($result)->toBeTrue();
                        });
                    }
                });

                describe('when `type` option is `"numeric"`', function () use ($isValid) {
                    $numericInputsSet = create_inputs_set('96206256120884');

                    foreach ($numericInputsSet as $type => $input) {
                        it("returns `true` for a valid CNPJ {$type} with only numbers", function () use ($isValid, $input) {
                            $result = $isValid($input, type: CnpjValidationType::Numeric);

                            expect($result)->toBeTrue();
                        });
                    }

                    $alphabeticInputsSet = create_inputs_set('1QB5UKALPYFP59');

                    foreach ($alphabeticInputsSet as $type => $input) {
                        it("returns `false` for a valid CNPJ {$type} with numbers and uppercase letters", function () use ($isValid, $input) {
                            $result = $isValid($input, type: CnpjValidationType::Numeric);

                            expect($result)->toBeFalse();
                        });
                    }
                });
            });
        }

        describe('when called with invalid arguments', function () {
            it('throws a `CnpjValidatorInputTypeError` with `null`', function () {
                $validator = new CnpjValidator();

                expect(fn () => $validator->isValid(null))
                    ->toThrow(CnpjValidatorInputTypeError::class)
                    ->toThrow('CNPJ input must be of type string or string[]. Got null.');
            });

            it('throws a `CnpjValidatorInputTypeError` with integer number', function () {
                $validator = new CnpjValidator();

                expect(fn () => $validator->isValid(42))
                    ->toThrow(CnpjValidatorInputTypeError::class)
                    ->toThrow('CNPJ input must be of type string or string[]. Got integer number.');
            });

            it('throws a `CnpjValidatorInputTypeError` with float number', function () {
                $validator = new CnpjValidator();

                expect(fn () => $validator->isValid(3.14))
                    ->toThrow(CnpjValidatorInputTypeError::class)
                    ->toThrow('CNPJ input must be of type string or string[]. Got float number.');
            });

            it('throws a `CnpjValidatorInputTypeError` with boolean', function () {
                $validator = new CnpjValidator();

                expect(fn () => $validator->isValid(true))
                    ->toThrow(CnpjValidatorInputTypeError::class)
                    ->toThrow('CNPJ input must be of type string or string[]. Got boolean.');
            });

            it('throws a `CnpjValidatorInputTypeError` with object', function () {
                $validator = new CnpjValidator();

                expect(fn () => $validator->isValid(new stdClass()))
                    ->toThrow(CnpjValidatorInputTypeError::class)
                    ->toThrow('CNPJ input must be of type string or string[]. Got object.');
            });

            it('throws a `CnpjValidatorInputTypeError` with array of numbers', function () {
                $validator = new CnpjValidator();

                expect(fn () => $validator->isValid([1, 2, 3]))
                    ->toThrow(CnpjValidatorInputTypeError::class)
                    ->toThrow('CNPJ input must be of type string or string[]. Got number[].');
            });
        });
    });
});
