<?php

declare(strict_types=1);

use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorInputTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionTypeInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorTypeError;

describe('CnpjValidatorTypeError', function () {
    describe('when instantiated through a subclass', function () {
        final class TestTypeError extends CnpjValidatorTypeError
        {
        }

        it('is an instance of `TypeError`', function () {
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error)->toBeInstanceOf(TypeError::class);
        });

        it('is an instance of `CnpjValidatorTypeError`', function () {
            $error = new TestTypeError(123, 'number', 'string', 'some error');

            expect($error)->toBeInstanceOf(CnpjValidatorTypeError::class);
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

describe('CnpjValidatorInputTypeError', function () {
    describe('when instantiated', function () {
        it('is an instance of `TypeError`', function () {
            $error = new CnpjValidatorInputTypeError(123, 'string');

            expect($error)->toBeInstanceOf(TypeError::class);
        });

        it('is an instance of `CnpjValidatorTypeError`', function () {
            $error = new CnpjValidatorInputTypeError(123, 'string');

            expect($error)->toBeInstanceOf(CnpjValidatorTypeError::class);
        });

        it('sets the `actualInput` property', function () {
            $error = new CnpjValidatorInputTypeError(123, 'string');

            expect($error->actualInput)->toBe(123);
        });

        it('sets the `actualType` property', function () {
            $error = new CnpjValidatorInputTypeError(123, 'string');

            expect($error->actualType)->toBe('integer number');
        });

        it('sets the `expectedType` property', function () {
            $error = new CnpjValidatorInputTypeError(123, 'string or string[]');

            expect($error->expectedType)->toBe('string or string[]');
        });

        it('has the correct message', function () {
            $actualInput = 123;
            $actualType = 'integer number';
            $expectedType = 'string or string[]';
            $message = "CNPJ input must be of type {$expectedType}. Got {$actualType}.";

            $error = new CnpjValidatorInputTypeError($actualInput, $expectedType);

            expect($error->getMessage())->toBe($message);
        });

        it('has the correct name', function () {
            $error = new CnpjValidatorInputTypeError(123, 'string or string[]');

            expect($error->getName())->toBe('CnpjValidatorInputTypeError');
        });
    });
});

describe('CnpjValidatorOptionsTypeError', function () {
    describe('when instantiated', function () {
        it('is an instance of `TypeError`', function () {
            $error = new CnpjValidatorOptionsTypeError('hidden', 123, 'boolean');

            expect($error)->toBeInstanceOf(TypeError::class);
        });

        it('is an instance of `CnpjValidatorTypeError`', function () {
            $error = new CnpjValidatorOptionsTypeError('hidden', 123, 'boolean');

            expect($error)->toBeInstanceOf(CnpjValidatorTypeError::class);
        });

        it('sets the `optionName` property', function () {
            $error = new CnpjValidatorOptionsTypeError('caseSensitive', 123, 'boolean');

            expect($error->optionName)->toBe('caseSensitive');
        });

        it('sets the `actualInput` property', function () {
            $error = new CnpjValidatorOptionsTypeError('caseSensitive', 123, 'boolean');

            expect($error->actualInput)->toBe(123);
        });

        it('sets the `actualType` property', function () {
            $error = new CnpjValidatorOptionsTypeError('caseSensitive', 123, 'boolean');

            expect($error->actualType)->toBe('integer number');
        });

        it('sets the `expectedType` property', function () {
            $error = new CnpjValidatorOptionsTypeError('caseSensitive', 123, 'boolean');

            expect($error->expectedType)->toBe('boolean');
        });

        it('has the correct message', function () {
            $optionName = 'caseSensitive';
            $actualInput = 123;
            $actualInputType = 'integer number';
            $expectedType = 'boolean';
            $message = "CNPJ validator option \"{$optionName}\" must be of type {$expectedType}. Got {$actualInputType}.";

            $error = new CnpjValidatorOptionsTypeError($optionName, $actualInput, $expectedType);

            expect($error->getMessage())->toBe($message);
        });

        it('has the correct name', function () {
            $error = new CnpjValidatorOptionsTypeError('caseSensitive', 123, 'boolean');

            expect($error->getName())->toBe('CnpjValidatorOptionsTypeError');
        });
    });
});

describe('CnpjValidatorException', function () {
    describe('when instantiated through a subclass', function () {
        final class TestException extends CnpjValidatorException
        {
        }

        it('is an instance of `Exception`', function () {
            $exception = new TestException('some error');

            expect($exception)->toBeInstanceOf(Exception::class);
        });

        it('is an instance of `CnpjValidatorException`', function () {
            $exception = new TestException('some error');

            expect($exception)->toBeInstanceOf(CnpjValidatorException::class);
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

describe('CnpjValidatorOptionTypeInvalidException', function () {
    describe('when instantiated', function () {
        it('is an instance of `Exception`', function () {
            $exception = new CnpjValidatorOptionTypeInvalidException('test', ['foo', 'bar', 'baz']);

            expect($exception)->toBeInstanceOf(Exception::class);
        });

        it('is an instance of `CnpjValidatorException`', function () {
            $exception = new CnpjValidatorOptionTypeInvalidException('test', ['foo', 'bar', 'baz']);

            expect($exception)->toBeInstanceOf(CnpjValidatorException::class);
        });

        it('sets the `actualInput` property', function () {
            $exception = new CnpjValidatorOptionTypeInvalidException('test', ['foo', 'bar', 'baz']);

            expect($exception->actualInput)->toBe('test');
        });

        it('sets the `expectedValues` property', function () {
            $exception = new CnpjValidatorOptionTypeInvalidException('test', ['foo', 'bar', 'baz']);

            expect($exception->expectedValues)->toBe(['foo', 'bar', 'baz']);
        });

        it('has the correct message', function () {
            $actualInput = 'test';
            $expectedValues = ['foo', 'bar', 'baz'];
            $expectedValuesString = implode('", "', $expectedValues);
            $message = "CNPJ validator option \"type\" accepts only the following values: \"{$expectedValuesString}\". Got \"{$actualInput}\".";

            $exception = new CnpjValidatorOptionTypeInvalidException($actualInput, $expectedValues);

            expect($exception->getMessage())->toBe($message);
        });

        it('has the correct name', function () {
            $exception = new CnpjValidatorOptionTypeInvalidException('test', ['foo', 'bar', 'baz']);

            expect($exception->getName())->toBe('CnpjValidatorOptionTypeInvalidException');
        });
    });
});
