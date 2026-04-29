<?php

declare(strict_types=1);

use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionPrefixInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionTypeInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorTypeError;

describe('CnpjGeneratorTypeError', function () {
    describe('when instantiated through a subclass', function () {
        final class TestTypeError extends CnpjGeneratorTypeError
        {
        }

        it('is an instance of TypeError', function () {
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error)->toBeInstanceOf(TypeError::class);
        });

        it('is an instance of CnpjGeneratorTypeError', function () {
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error)->toBeInstanceOf(CnpjGeneratorTypeError::class);
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
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error->getMessage())->toBe('some error');
        });

        it('has the correct name', function () {
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error->getName())->toBe('TestTypeError');
        });
    });
});

describe('CnpjGeneratorOptionsTypeError', function () {
    describe('when instantiated', function () {
        it('is an instance of TypeError', function () {
            $error = new CnpjGeneratorOptionsTypeError('format', 123, 'boolean');

            expect($error)->toBeInstanceOf(TypeError::class);
        });

        it('is an instance of CnpjGeneratorTypeError', function () {
            $error = new CnpjGeneratorOptionsTypeError('format', 123, 'boolean');

            expect($error)->toBeInstanceOf(CnpjGeneratorTypeError::class);
        });

        it('sets the `optionName` property', function () {
            $error = new CnpjGeneratorOptionsTypeError('format', 123, 'boolean');

            expect($error->optionName)->toBe('format');
        });

        it('sets the `actualInput` property', function () {
            $error = new CnpjGeneratorOptionsTypeError('format', 123, 'boolean');

            expect($error->actualInput)->toBe(123);
        });

        it('sets the `actualType` property', function () {
            $error = new CnpjGeneratorOptionsTypeError('format', 123, 'boolean');

            expect($error->actualType)->toBe('integer number');
        });

        it('sets the `expectedType` property', function () {
            $error = new CnpjGeneratorOptionsTypeError('format', 123, 'boolean');

            expect($error->expectedType)->toBe('boolean');
        });

        it('has the correct message', function () {
            $optionName = 'format';
            $actualInput = 123;
            $actualInputType = 'integer number';
            $expectedType = 'boolean';
            $message = "CNPJ generating option \"{$optionName}\" must be of type {$expectedType}. Got {$actualInputType}.";

            $error = new CnpjGeneratorOptionsTypeError($optionName, $actualInput, $expectedType);

            expect($error->getMessage())->toBe($message);
        });

        it('has the correct name', function () {
            $error = new CnpjGeneratorOptionsTypeError('format', 123, 'boolean');

            expect($error->getName())->toBe('CnpjGeneratorOptionsTypeError');
        });
    });
});

describe('CnpjGeneratorException', function () {
    describe('when instantiated through a subclass', function () {
        final class TestException extends CnpjGeneratorException
        {
        }

        it('is an instance of Exception', function () {
            $exception = new TestException('some error');

            expect($exception)->toBeInstanceOf(Exception::class);
        });

        it('is an instance of CnpjGeneratorException', function () {
            $exception = new TestException('some error');

            expect($exception)->toBeInstanceOf(CnpjGeneratorException::class);
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

describe('CnpjGeneratorOptionPrefixInvalidException', function () {
    describe('when instantiated', function () {
        it('is an instance of Exception', function () {
            $exception = new CnpjGeneratorOptionPrefixInvalidException('1.2.3.4.5', 'some reason');

            expect($exception)->toBeInstanceOf(Exception::class);
        });

        it('is an instance of CnpjGeneratorException', function () {
            $exception = new CnpjGeneratorOptionPrefixInvalidException('1.2.3.4.5', 'some reason');

            expect($exception)->toBeInstanceOf(CnpjGeneratorException::class);
        });

        it('sets the `actualInput` property', function () {
            $exception = new CnpjGeneratorOptionPrefixInvalidException('1.2.3.4.5', 'some reason');

            expect($exception->actualInput)->toBe('1.2.3.4.5');
        });

        it('sets the `reason` property', function () {
            $exception = new CnpjGeneratorOptionPrefixInvalidException('1.2.3.4.5', 'some reason');

            expect($exception->reason)->toBe('some reason');
        });

        it('has the correct message', function () {
            $actualInput = '1.2.3.4.5';
            $reason = 'some reason';
            $message = "CNPJ generator option \"prefix\" with value \"{$actualInput}\" is invalid. {$reason}";

            $exception = new CnpjGeneratorOptionPrefixInvalidException($actualInput, $reason);

            expect($exception->getMessage())->toBe($message);
        });

        it('has the correct name', function () {
            $exception = new CnpjGeneratorOptionPrefixInvalidException('1.2.3.4.5', 'some reason');

            expect($exception->getName())->toBe('CnpjGeneratorOptionPrefixInvalidException');
        });
    });
});

describe('CnpjGeneratorOptionTypeInvalidException', function () {
    describe('when instantiated', function () {
        it('is an instance of Exception', function () {
            $exception = new CnpjGeneratorOptionTypeInvalidException('test', ['foo', 'bar', 'baz']);

            expect($exception)->toBeInstanceOf(Exception::class);
        });

        it('is an instance of CnpjGeneratorException', function () {
            $exception = new CnpjGeneratorOptionTypeInvalidException('test', ['foo', 'bar', 'baz']);

            expect($exception)->toBeInstanceOf(CnpjGeneratorException::class);
        });

        it('sets the `actualInput` property', function () {
            $exception = new CnpjGeneratorOptionTypeInvalidException('test', ['foo', 'bar', 'baz']);

            expect($exception->actualInput)->toBe('test');
        });

        it('sets the `expectedValues` property', function () {
            $exception = new CnpjGeneratorOptionTypeInvalidException('test', ['foo', 'bar', 'baz']);

            expect($exception->expectedValues)->toBe(['foo', 'bar', 'baz']);
        });

        it('has the correct message', function () {
            $actualInput = 'test';
            $expectedValues = ['foo', 'bar', 'baz'];
            $expectedValuesString = implode('", "', $expectedValues);
            $message = "CNPJ generator option \"type\" accepts only the following values: \"{$expectedValuesString}\". Got \"{$actualInput}\".";

            $exception = new CnpjGeneratorOptionTypeInvalidException($actualInput, $expectedValues);

            expect($exception->getMessage())->toBe($message);
        });

        it('has the correct name', function () {
            $exception = new CnpjGeneratorOptionTypeInvalidException('test', ['foo', 'bar', 'baz']);

            expect($exception->getName())->toBe('CnpjGeneratorOptionTypeInvalidException');
        });
    });
});
