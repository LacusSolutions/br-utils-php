<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Tests\Specs;

use const Lacus\BrUtils\Cnpj\CNPJ_MAX_LENGTH;
use const Lacus\BrUtils\Cnpj\CNPJ_MIN_LENGTH;

use Lacus\BrUtils\Cnpj\CnpjCheckDigits;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputLengthException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsTypeError;
use ReflectionClass;

describe('the cnpj-dv package surface', function () {
    describe('when inspecting constants', function () {
        it('exposes CNPJ_MIN_LENGTH on the class and as a global constant', function () {
            expect(CnpjCheckDigits::CNPJ_MIN_LENGTH)->toBe(12)
                ->and(CNPJ_MIN_LENGTH)->toBe(12);
        });

        it('exposes CNPJ_MAX_LENGTH on the class and as a global constant', function () {
            expect(CnpjCheckDigits::CNPJ_MAX_LENGTH)->toBe(14)
                ->and(CNPJ_MAX_LENGTH)->toBe(14);
        });
    });

    describe('when inspecting public types', function () {
        it('exposes CnpjCheckDigits as an instantiable class', function () {
            $instance = new CnpjCheckDigits('914157320007');

            expect($instance)->toBeInstanceOf(CnpjCheckDigits::class)
                ->and($instance->first)->toBe('9')
                ->and($instance->second)->toBe('3')
                ->and($instance->cnpj)->toBe('91415732000793');
        });

        it('exposes CnpjCheckDigitsTypeError as an abstract type', function () {
            expect((new ReflectionClass(CnpjCheckDigitsTypeError::class))->isAbstract())->toBeTrue();
        });

        it('exposes CnpjCheckDigitsInputTypeError as instantiable', function () {
            $instance = new CnpjCheckDigitsInputTypeError(123, 'string');

            expect($instance->actualInput)->toBe(123)
                ->and($instance->getMessage())->toBe('CNPJ input must be of type string. Got integer number.');
        });

        it('exposes CnpjCheckDigitsException as an abstract type', function () {
            expect((new ReflectionClass(CnpjCheckDigitsException::class))->isAbstract())->toBeTrue();
        });

        it('exposes CnpjCheckDigitsInputInvalidException as instantiable', function () {
            $instance = new CnpjCheckDigitsInputInvalidException('123', 'some reason');

            expect($instance->actualInput)->toBe('123')
                ->and($instance->reason)->toBe('some reason')
                ->and($instance->getMessage())->toBe('CNPJ input "123" is invalid. some reason');
        });

        it('exposes CnpjCheckDigitsInputLengthException as instantiable', function () {
            $instance = new CnpjCheckDigitsInputLengthException('x', '1', 12, 14);

            expect($instance->minExpectedLength)->toBe(12)
                ->and($instance->maxExpectedLength)->toBe(14);
        });
    });
});
