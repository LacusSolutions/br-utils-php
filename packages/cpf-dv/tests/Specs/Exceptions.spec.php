<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf\Tests\Specs;

use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputInvalidException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputLengthException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputTypeError;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsTypeError;
use TypeError;

final class TestCpfCheckDigitsTypeError extends CpfCheckDigitsTypeError
{
    public function __construct()
    {
        parent::__construct(123, 'number', 'string', 'some error');
    }
}

final class TestCpfCheckDigitsException extends CpfCheckDigitsException
{
}

describe('CpfCheckDigitsTypeError', function () {
    describe('when instantiated through a subclass', function () {
        it('is an instance of TypeError', function () {
            $error = new TestCpfCheckDigitsTypeError();

            expect($error)->toBeInstanceOf(TypeError::class);
        });

        it('is an instance of CpfCheckDigitsTypeError', function () {
            $error = new TestCpfCheckDigitsTypeError();

            expect($error)->toBeInstanceOf(CpfCheckDigitsTypeError::class);
        });

        it('has the correct class name', function () {
            $error = new TestCpfCheckDigitsTypeError();

            expect($error::class)->toBe(TestCpfCheckDigitsTypeError::class);
        });

        it('sets the `actualInput` property', function () {
            $error = new TestCpfCheckDigitsTypeError();

            expect($error->actualInput)->toBe(123);
        });

        it('sets the `actualType` property', function () {
            $error = new TestCpfCheckDigitsTypeError();

            expect($error->actualType)->toBe('number');
        });

        it('sets the `expectedType` property', function () {
            $error = new TestCpfCheckDigitsTypeError();

            expect($error->expectedType)->toBe('string');
        });

        it('has a `message` property', function () {
            $error = new TestCpfCheckDigitsTypeError();

            expect($error->getMessage())->toBe('some error');
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

        it('has the correct class name', function () {
            $error = new CpfCheckDigitsInputTypeError(123, 'string');

            expect($error::class)->toBe(CpfCheckDigitsInputTypeError::class);
        });

        it('sets the `actualInput` property', function () {
            $input = 123;
            $error = new CpfCheckDigitsInputTypeError($input, 'string');

            expect($error->actualInput)->toBe($input);
        });

        it('sets the `actualType` property', function () {
            $error = new CpfCheckDigitsInputTypeError(123, 'string');

            expect($error->actualType)->toBe('integer number');
        });

        it('sets the `expectedType` property', function () {
            $error = new CpfCheckDigitsInputTypeError(123, 'string or string[]');

            expect($error->expectedType)->toBe('string or string[]');
        });

        it('generates a message describing the error', function () {
            $actualInput = 123;
            $actualType = 'integer number';
            $expectedType = 'string[]';
            $actualMessage = "CPF input must be of type {$expectedType}. Got {$actualType}.";

            $error = new CpfCheckDigitsInputTypeError($actualInput, $expectedType);

            expect($error->getMessage())->toBe($actualMessage);
        });
    });
});

describe('CpfCheckDigitsException', function () {
    describe('when instantiated through a subclass', function () {
        it('is an instance of Exception', function () {
            $exception = new TestCpfCheckDigitsException('some error');

            expect($exception)->toBeInstanceOf(\Exception::class);
        });

        it('is an instance of CpfCheckDigitsException', function () {
            $exception = new TestCpfCheckDigitsException('some error');

            expect($exception)->toBeInstanceOf(CpfCheckDigitsException::class);
        });

        it('has the correct class name', function () {
            $exception = new TestCpfCheckDigitsException('some error');

            expect($exception::class)->toBe(TestCpfCheckDigitsException::class);
        });

        it('has a `message` property', function () {
            $exception = new TestCpfCheckDigitsException('some error');

            expect($exception->getMessage())->toBe('some error');
        });
    });
});

describe('CpfCheckDigitsInputLengthException', function () {
    describe('when instantiated', function () {
        it('is an instance of Exception', function () {
            $exception = new CpfCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception)->toBeInstanceOf(\Exception::class);
        });

        it('is an instance of CpfCheckDigitsException', function () {
            $exception = new CpfCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception)->toBeInstanceOf(CpfCheckDigitsException::class);
        });

        it('has the correct class name', function () {
            $exception = new CpfCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception::class)->toBe(CpfCheckDigitsInputLengthException::class);
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

        it('generates a message describing the exception', function () {
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
    });
});

describe('CpfCheckDigitsInputInvalidException', function () {
    describe('when instantiated', function () {
        it('is an instance of Exception', function () {
            $exception = new CpfCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception)->toBeInstanceOf(\Exception::class);
        });

        it('is an instance of CpfCheckDigitsException', function () {
            $exception = new CpfCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception)->toBeInstanceOf(CpfCheckDigitsException::class);
        });

        it('has the correct class name', function () {
            $exception = new CpfCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception::class)->toBe(CpfCheckDigitsInputInvalidException::class);
        });

        it('sets the `actualInput` property', function () {
            $exception = new CpfCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception->actualInput)->toBe('1.2.3.4.5');
        });

        it('sets the `reason` property', function () {
            $exception = new CpfCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception->reason)->toBe('repeated digits');
        });

        it('generates a message describing the exception', function () {
            $actualInput = '1.2.3.4.5';
            $reason = 'repeated digits';
            $actualMessage = 'CPF input "'.$actualInput.'" is invalid. '.$reason;

            $exception = new CpfCheckDigitsInputInvalidException($actualInput, $reason);

            expect($exception->getMessage())->toBe($actualMessage);
        });
    });
});
