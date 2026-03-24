<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf\Tests\Specs;

use const Lacus\BrUtils\Cpf\CPF_MAX_LENGTH;
use const Lacus\BrUtils\Cpf\CPF_MIN_LENGTH;

use Lacus\BrUtils\Cpf\CpfCheckDigits;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputInvalidException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputLengthException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputTypeError;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsTypeError;
use ReflectionClass;

describe('the cpf-dv package surface', function () {
    describe('when inspecting constants', function () {
        it('exposes CPF_MIN_LENGTH on the class and as a global constant', function () {
            expect(CpfCheckDigits::CPF_MIN_LENGTH)->toBe(9)
                ->and(CPF_MIN_LENGTH)->toBe(9);
        });

        it('exposes CPF_MAX_LENGTH on the class and as a global constant', function () {
            expect(CpfCheckDigits::CPF_MAX_LENGTH)->toBe(11)
                ->and(CPF_MAX_LENGTH)->toBe(11);
        });
    });

    describe('when inspecting public types', function () {
        it('exposes CpfCheckDigits as an instantiable class', function () {
            $instance = new CpfCheckDigits('123456789');

            expect($instance)->toBeInstanceOf(CpfCheckDigits::class)
                ->and($instance->first)->toBe('0')
                ->and($instance->second)->toBe('9')
                ->and($instance->cpf)->toBe('12345678909');
        });

        it('exposes CpfCheckDigitsTypeError as an abstract type', function () {
            expect((new ReflectionClass(CpfCheckDigitsTypeError::class))->isAbstract())->toBeTrue();
        });

        it('exposes CpfCheckDigitsInputTypeError as instantiable', function () {
            $instance = new CpfCheckDigitsInputTypeError(123, 'string');

            expect($instance->actualInput)->toBe(123)
                ->and($instance->getMessage())->toBe('CPF input must be of type string. Got integer number.');
        });

        it('exposes CpfCheckDigitsException as an abstract type', function () {
            expect((new ReflectionClass(CpfCheckDigitsException::class))->isAbstract())->toBeTrue();
        });

        it('exposes CpfCheckDigitsInputInvalidException as instantiable', function () {
            $instance = new CpfCheckDigitsInputInvalidException('123', 'some reason');

            expect($instance->actualInput)->toBe('123')
                ->and($instance->reason)->toBe('some reason')
                ->and($instance->getMessage())->toBe('CPF input "123" is invalid. some reason');
        });

        it('exposes CpfCheckDigitsInputLengthException as instantiable', function () {
            $instance = new CpfCheckDigitsInputLengthException('x', '1', 9, 11);

            expect($instance->minExpectedLength)->toBe(9)
                ->and($instance->maxExpectedLength)->toBe(11);
        });
    });
});
