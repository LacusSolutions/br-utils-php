<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Tests\Specs;

use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputLengthException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsTypeError;
use TypeError;

final class TestCnpjCheckDigitsTypeError extends CnpjCheckDigitsTypeError
{
    public function __construct()
    {
        parent::__construct(123, 'number', 'string', 'some error');
    }
}

final class TestCnpjCheckDigitsException extends CnpjCheckDigitsException
{
}

describe('CnpjCheckDigitsTypeError', function () {
    describe('when instantiated through a subclass', function () {
        it('is an instance of TypeError', function () {
            $error = new TestCnpjCheckDigitsTypeError();

            expect($error)->toBeInstanceOf(TypeError::class);
        });

        it('is an instance of CnpjCheckDigitsTypeError', function () {
            $error = new TestCnpjCheckDigitsTypeError();

            expect($error)->toBeInstanceOf(CnpjCheckDigitsTypeError::class);
        });

        it('has the correct class name', function () {
            $error = new TestCnpjCheckDigitsTypeError();

            expect($error::class)->toBe(TestCnpjCheckDigitsTypeError::class);
        });

        it('sets the `actualInput` property', function () {
            $error = new TestCnpjCheckDigitsTypeError();

            expect($error->actualInput)->toBe(123);
        });

        it('sets the `actualType` property', function () {
            $error = new TestCnpjCheckDigitsTypeError();

            expect($error->actualType)->toBe('number');
        });

        it('sets the `expectedType` property', function () {
            $error = new TestCnpjCheckDigitsTypeError();

            expect($error->expectedType)->toBe('string');
        });

        it('has a `message` property', function () {
            $error = new TestCnpjCheckDigitsTypeError();

            expect($error->getMessage())->toBe('some error');
        });
    });
});

describe('CnpjCheckDigitsInputTypeError', function () {
    describe('when instantiated', function () {
        it('is an instance of TypeError', function () {
            $error = new CnpjCheckDigitsInputTypeError(123, 'string');

            expect($error)->toBeInstanceOf(TypeError::class);
        });

        it('is an instance of CnpjCheckDigitsTypeError', function () {
            $error = new CnpjCheckDigitsInputTypeError(123, 'string');

            expect($error)->toBeInstanceOf(CnpjCheckDigitsTypeError::class);
        });

        it('has the correct class name', function () {
            $error = new CnpjCheckDigitsInputTypeError(123, 'string');

            expect($error::class)->toBe(CnpjCheckDigitsInputTypeError::class);
        });

        it('sets the `actualInput` property', function () {
            $input = 123;
            $error = new CnpjCheckDigitsInputTypeError($input, 'string');

            expect($error->actualInput)->toBe($input);
        });

        it('sets the `actualType` property', function () {
            $error = new CnpjCheckDigitsInputTypeError(123, 'string');

            expect($error->actualType)->toBe('integer number');
        });

        it('sets the `expectedType` property', function () {
            $error = new CnpjCheckDigitsInputTypeError(123, 'string or string[]');

            expect($error->expectedType)->toBe('string or string[]');
        });

        it('generates a message describing the error', function () {
            $actualInput = 123;
            $actualType = 'integer number';
            $expectedType = 'string[]';
            $actualMessage = "CNPJ input must be of type {$expectedType}. Got {$actualType}.";

            $error = new CnpjCheckDigitsInputTypeError($actualInput, $expectedType);

            expect($error->getMessage())->toBe($actualMessage);
        });
    });
});

describe('CnpjCheckDigitsException', function () {
    describe('when instantiated through a subclass', function () {
        it('is an instance of Exception', function () {
            $exception = new TestCnpjCheckDigitsException('some error');

            expect($exception)->toBeInstanceOf(\Exception::class);
        });

        it('is an instance of CnpjCheckDigitsException', function () {
            $exception = new TestCnpjCheckDigitsException('some error');

            expect($exception)->toBeInstanceOf(CnpjCheckDigitsException::class);
        });

        it('has the correct class name', function () {
            $exception = new TestCnpjCheckDigitsException('some error');

            expect($exception::class)->toBe(TestCnpjCheckDigitsException::class);
        });

        it('has a `message` property', function () {
            $exception = new TestCnpjCheckDigitsException('some error');

            expect($exception->getMessage())->toBe('some error');
        });
    });
});

describe('CnpjCheckDigitsInputLengthException', function () {
    describe('when instantiated', function () {
        it('is an instance of Exception', function () {
            $exception = new CnpjCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception)->toBeInstanceOf(\Exception::class);
        });

        it('is an instance of CnpjCheckDigitsException', function () {
            $exception = new CnpjCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception)->toBeInstanceOf(CnpjCheckDigitsException::class);
        });

        it('has the correct class name', function () {
            $exception = new CnpjCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception::class)->toBe(CnpjCheckDigitsInputLengthException::class);
        });

        it('sets the `actualInput` property', function () {
            $exception = new CnpjCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception->actualInput)->toBe('1.2.3.4.5');
        });

        it('sets the `evaluatedInput` property', function () {
            $exception = new CnpjCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception->evaluatedInput)->toBe('12345');
        });

        it('sets the `minExpectedLength` property', function () {
            $exception = new CnpjCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception->minExpectedLength)->toBe(12);
        });

        it('sets the `maxExpectedLength` property', function () {
            $exception = new CnpjCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception->maxExpectedLength)->toBe(14);
        });

        it('generates a message describing the exception', function () {
            $actualInput = '1.2.3.4.5';
            $evaluatedInput = '12345';
            $minExpectedLength = 12;
            $maxExpectedLength = 14;
            $actualMessage = 'CNPJ input "'.$actualInput.'" does not contain '.$minExpectedLength.' to '.$maxExpectedLength.' digits. Got '.strlen($evaluatedInput).' in "'.$evaluatedInput.'".';

            $exception = new CnpjCheckDigitsInputLengthException(
                $actualInput,
                $evaluatedInput,
                $minExpectedLength,
                $maxExpectedLength,
            );

            expect($exception->getMessage())->toBe($actualMessage);
        });
    });
});

describe('CnpjCheckDigitsInputInvalidException', function () {
    describe('when instantiated', function () {
        it('is an instance of Exception', function () {
            $exception = new CnpjCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception)->toBeInstanceOf(\Exception::class);
        });

        it('is an instance of CnpjCheckDigitsException', function () {
            $exception = new CnpjCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception)->toBeInstanceOf(CnpjCheckDigitsException::class);
        });

        it('has the correct class name', function () {
            $exception = new CnpjCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception::class)->toBe(CnpjCheckDigitsInputInvalidException::class);
        });

        it('sets the `actualInput` property', function () {
            $exception = new CnpjCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception->actualInput)->toBe('1.2.3.4.5');
        });

        it('sets the `reason` property', function () {
            $exception = new CnpjCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception->reason)->toBe('repeated digits');
        });

        it('generates a message describing the exception', function () {
            $actualInput = '1.2.3.4.5';
            $reason = 'repeated digits';
            $actualMessage = 'CNPJ input "'.$actualInput.'" is invalid. '.$reason;

            $exception = new CnpjCheckDigitsInputInvalidException($actualInput, $reason);

            expect($exception->getMessage())->toBe($actualMessage);
        });
    });
});
