<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf\Tests\Specs;

use Exception;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputInvalidException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputLengthException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputTypeError;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsTypeError;
use TypeError;

describe('CpfCheckDigitsTypeError', function () {
    final class TestTypeError extends CpfCheckDigitsTypeError
    {
    }

    describe('when instantiated through a subclass', function () {
        it('is an instance of TypeError', function () {
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error)->toBeInstanceOf(TypeError::class);
        });

        it('is an instance of CpfCheckDigitsTypeError', function () {
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error)->toBeInstanceOf(CpfCheckDigitsTypeError::class);
        });

        it('sets the `actualInput` property', function () {
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error->actualInput)->toBe(123);
        });

        it('sets the `actualType` property', function () {
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error->actualType)->toBe('number');
        });

        it('sets the `expectedType` property', function () {
            $error = new TestTypeError(123, 'number', 'string or string[]', 'some error');

            expect($error->expectedType)->toBe('string or string[]');
        });

        it('has the correct message', function () {
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error->getMessage())->toBe('some error');
        });

        it('has the correct name', function () {
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error->getName())->toBe('TestTypeError');
        });
    });
});

describe('CpfCheckDigitsInputTypeError', function () {
    describe('when instantiated', function () {
        it('is an instance of TypeError', function () {
            $error = new CpfCheckDigitsInputTypeError(123, 'string');

            expect($error)->toBeInstanceOf(TypeError::class);
        });

        it('is an instance of CpfCheckDigitsTypeError', function () {
            $error = new CpfCheckDigitsInputTypeError(123, 'string');

            expect($error)->toBeInstanceOf(CpfCheckDigitsTypeError::class);
        });

        it('sets the `actualInput` property', function () {
            $error = new CpfCheckDigitsInputTypeError(123, 'string');

            expect($error->actualInput)->toBe(123);
        });

        it('sets the `actualType` property', function () {
            $error = new CpfCheckDigitsInputTypeError(123, 'string');

            expect($error->actualType)->toBe('integer number');
        });

        it('sets the `expectedType` property', function () {
            $error = new CpfCheckDigitsInputTypeError(123, 'string or string[]');

            expect($error->expectedType)->toBe('string or string[]');
        });

        it('has the correct message', function () {
            $actualInput = 123;
            $actualType = 'integer number';
            $expectedType = 'string[]';
            $actualMessage = "CPF input must be of type {$expectedType}. Got {$actualType}.";

            $error = new CpfCheckDigitsInputTypeError(
                $actualInput,
                $expectedType,
            );

            expect($error->getMessage())->toBe($actualMessage);
        });

        it('has the correct name', function () {
            $error = new CpfCheckDigitsInputTypeError(123, 'string');

            expect($error->getName())->toBe('CpfCheckDigitsInputTypeError');
        });
    });
});

describe('CpfCheckDigitsException', function () {
    final class TestException extends CpfCheckDigitsException
    {
    }

    describe('when instantiated through a subclass', function () {
        it('is an instance of Exception', function () {
            $exception = new TestException('some error');

            expect($exception)->toBeInstanceOf(Exception::class);
        });

        it('is an instance of CpfCheckDigitsException', function () {
            $exception = new TestException('some error');

            expect($exception)->toBeInstanceOf(CpfCheckDigitsException::class);
        });

        it('has the correct message', function () {
            $exception = new TestException('some error');

            expect($exception->getMessage())->toBe('some error');
        });

        it('has the correct name', function () {
            $exception = new TestException('some error');

            expect($exception->getName())->toBe('TestException');
        });
    });
});

describe('CpfCheckDigitsInputLengthException', function () {
    describe('when instantiated', function () {
        it('is an instance of Exception', function () {
            $exception = new CpfCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception)->toBeInstanceOf(Exception::class);
        });

        it('is an instance of CpfCheckDigitsException', function () {
            $exception = new CpfCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception)->toBeInstanceOf(CpfCheckDigitsException::class);
        });

        it('sets the `actualInput` property', function () {
            $exception = new CpfCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception->actualInput)->toBe('1.2.3.4.5');
        });

        it('sets the `evaluatedInput` property', function () {
            $exception = new CpfCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception->evaluatedInput)->toBe('12345');
        });

        it('sets the `minExpectedLength` property', function () {
            $exception = new CpfCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception->minExpectedLength)->toBe(12);
        });

        it('sets the `maxExpectedLength` property', function () {
            $exception = new CpfCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception->maxExpectedLength)->toBe(14);
        });

        it('has the correct message', function () {
            $actualInput = '1.2.3.4.5';
            $evaluatedInput = '12345';
            $minExpectedLength = 12;
            $maxExpectedLength = 14;
            $actualMessage = 'CPF input "'.$actualInput.'" does not contain '.$minExpectedLength.' to '.$maxExpectedLength.' digits. Got '.strlen($evaluatedInput).' in "'.$evaluatedInput.'".';

            $exception = new CpfCheckDigitsInputLengthException(
                $actualInput,
                $evaluatedInput,
                $minExpectedLength,
                $maxExpectedLength,
            );

            expect($exception->getMessage())->toBe($actualMessage);
        });

        it('has the correct name', function () {
            $exception = new CpfCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception->getName())->toBe('CpfCheckDigitsInputLengthException');
        });
    });
});

describe('CpfCheckDigitsInputInvalidException', function () {
    describe('when instantiated', function () {
        it('is an instance of Exception', function () {
            $exception = new CpfCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception)->toBeInstanceOf(Exception::class);
        });

        it('is an instance of CpfCheckDigitsException', function () {
            $exception = new CpfCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception)->toBeInstanceOf(CpfCheckDigitsException::class);
        });

        it('sets the `actualInput` property', function () {
            $exception = new CpfCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception->actualInput)->toBe('1.2.3.4.5');
        });

        it('sets the `reason` property', function () {
            $exception = new CpfCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception->reason)->toBe('repeated digits');
        });

        it('has the correct message', function () {
            $actualInput = '1.2.3.4.5';
            $reason = 'repeated digits';
            $actualMessage = 'CPF input "'.$actualInput.'" is invalid. '.$reason;

            $exception = new CpfCheckDigitsInputInvalidException(
                $actualInput,
                $reason,
            );

            expect($exception->getMessage())->toBe($actualMessage);
        });

        it('has the correct name', function () {
            $exception = new CpfCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception->getName())->toBe('CpfCheckDigitsInputInvalidException');
        });
    });
});
