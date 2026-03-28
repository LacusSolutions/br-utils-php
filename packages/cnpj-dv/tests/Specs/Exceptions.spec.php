<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Tests\Specs;

use Exception;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputLengthException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsTypeError;
use TypeError;

describe('CnpjCheckDigitsTypeError', function () {
    final class TestTypeError extends CnpjCheckDigitsTypeError
    {
    }

    describe('when instantiated through a subclass', function () {
        it('is an instance of TypeError', function () {
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error)->toBeInstanceOf(TypeError::class);
        });

        it('is an instance of CnpjCheckDigitsTypeError', function () {
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error)->toBeInstanceOf(CnpjCheckDigitsTypeError::class);
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
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error->expectedType)->toBe('string');
        });

        it('has the correct message', function () {
            $exception = new TestTypeError(123, 'number', 'string', 'some error');

            expect($exception->getMessage())->toBe('some error');
        });

        it('has the correct name', function () {
            $exception = new TestTypeError(123, 'number', 'string', 'some error');

            expect($exception->getName())->toBe('TestTypeError');
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

        it('sets the `actualInput` property', function () {
            $error = new CnpjCheckDigitsInputTypeError(123, 'string');

            expect($error->actualInput)->toBe(123);
        });

        it('sets the `actualType` property', function () {
            $error = new CnpjCheckDigitsInputTypeError(123, 'string');

            expect($error->actualType)->toBe('integer number');
        });

        it('sets the `expectedType` property', function () {
            $error = new CnpjCheckDigitsInputTypeError(123, 'string or string[]');

            expect($error->expectedType)->toBe('string or string[]');
        });

        it('has the correct message', function () {
            $actualInput = 123;
            $actualType = 'integer number';
            $expectedType = 'string[]';
            $actualMessage = "CNPJ input must be of type {$expectedType}. Got {$actualType}.";

            $error = new CnpjCheckDigitsInputTypeError(
                $actualInput,
                $expectedType,
            );

            expect($error->getMessage())->toBe($actualMessage);
        });

        it('has the correct name', function () {
            $error = new CnpjCheckDigitsInputTypeError(123, 'string');

            expect($error->getName())->toBe('CnpjCheckDigitsInputTypeError');
        });
    });
});

describe('CnpjCheckDigitsException', function () {
    final class TestException extends CnpjCheckDigitsException
    {
    }

    describe('when instantiated through a subclass', function () {
        it('is an instance of Exception', function () {
            $exception = new TestException('some error');

            expect($exception)->toBeInstanceOf(Exception::class);
        });

        it('is an instance of CnpjCheckDigitsException', function () {
            $exception = new TestException('some error');

            expect($exception)->toBeInstanceOf(CnpjCheckDigitsException::class);
        });

        it('has the correct message', function () {
            $exception = new TestException('some exception');

            expect($exception->getMessage())->toBe('some exception');
        });

        it('has the correct name', function () {
            $exception = new TestException('some error');

            expect($exception->getName())->toBe('TestException');
        });
    });
});

describe('CnpjCheckDigitsInputLengthException', function () {
    describe('when instantiated', function () {
        it('is an instance of Exception', function () {
            $exception = new CnpjCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception)->toBeInstanceOf(Exception::class);
        });

        it('is an instance of CnpjCheckDigitsException', function () {
            $exception = new CnpjCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception)->toBeInstanceOf(CnpjCheckDigitsException::class);
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

        it('has the correct message', function () {
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

        it('has the correct name', function () {
            $exception = new CnpjCheckDigitsInputLengthException('1.2.3.4.5', '12345', 12, 14);

            expect($exception->getName())->toBe('CnpjCheckDigitsInputLengthException');
        });
    });
});

describe('CnpjCheckDigitsInputInvalidException', function () {
    describe('when instantiated', function () {
        it('is an instance of Exception', function () {
            $exception = new CnpjCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception)->toBeInstanceOf(Exception::class);
        });

        it('is an instance of CnpjCheckDigitsException', function () {
            $exception = new CnpjCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception)->toBeInstanceOf(CnpjCheckDigitsException::class);
        });

        it('sets the `actualInput` property', function () {
            $exception = new CnpjCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception->actualInput)->toBe('1.2.3.4.5');
        });

        it('sets the `reason` property', function () {
            $exception = new CnpjCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception->reason)->toBe('repeated digits');
        });

        it('has the correct message', function () {
            $actualInput = '1.2.3.4.5';
            $reason = 'repeated digits';
            $actualMessage = 'CNPJ input "'.$actualInput.'" is invalid. '.$reason;

            $exception = new CnpjCheckDigitsInputInvalidException(
                $actualInput,
                $reason,
            );

            expect($exception->getMessage())->toBe($actualMessage);
        });

        it('has the correct name', function () {
            $exception = new CnpjCheckDigitsInputInvalidException('1.2.3.4.5', 'repeated digits');

            expect($exception->getName())->toBe('CnpjCheckDigitsInputInvalidException');
        });
    });
});
