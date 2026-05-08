<?php

declare(strict_types=1);

use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\CnpjGeneratorOptions;
use Lacus\BrUtils\Cnpj\Enums\CnpjType;
use Lacus\Utils\SequenceGenerator;
use Mockery\CompositeExpectation;

/*
|--------------------------------------------------------------------------
| Process-isolation specs
|--------------------------------------------------------------------------
|
| These tests use Mockery `alias:` on `SequenceGenerator`, which replaces the
| class for the entire PHP process; `composer test` runs this file in a
| second Pest invocation (group `isolated-process-tests`).
|
| The alias only works before `SequenceGenerator` is autoloaded, so this file
| is kept apart from other `CnpjGenerator` specs and must load first within
| its worker/process.
|
| Mocking `CnpjCheckDigits` with `overload:` is intentionally avoided: the
| generated overload class does not expose magic methods such as `__get`,
| so reading `->cnpj` on the mock would return `null` regardless of the
| expectations attached to it. Forcing the real `CnpjCheckDigits` to throw
| on a controlled, invalid sequence keeps the assertion meaningful while
| sidestepping that limitation.
*/

describe('CnpjGenerator - process isolation', function () {
    class CnpjGeneratorCallsSpy extends CnpjGenerator
    {
        public int $callsCount = 0;

        /**
         * @var list<
         *     array{
         *         0: ?CnpjGeneratorOptions,
         *         1: mixed,
         *         2: mixed,
         *         3: mixed
         *     }
         * >
         */
        public array $callsArguments = [];

        public function generate(
            ?CnpjGeneratorOptions $options = null,
            $format = null,
            $prefix = null,
            $type = null,
        ): string {
            $this->callsCount++;
            $this->callsArguments[] = func_get_args();

            return parent::generate($options, $format, $prefix, $type);
        }
    }

    function mockSequenceGenerator(string ...$returns): void
    {
        $mockingException = Mockery::mock('alias:' . SequenceGenerator::class)
            ->shouldReceive('generate');

        if (!$mockingException instanceof CompositeExpectation) {
            throw new RuntimeException('Unexpected Mockery expectation type for SequenceGenerator::generate.');
        }

        $mockingException->andReturn(...$returns);
    }

    describe('when `CnpjCheckDigits` throws an exception', function () {
        it('retries generation and returns a valid CNPJ', function () {
            mockSequenceGenerator('111111111111', '123456780001');

            $generator = new CnpjGeneratorCallsSpy();
            $result = $generator->generate();

            expect($result)->toHaveLength(14);
            expect($result)->toStartWith('123456780001');
            expect($generator->callsCount)->toBe(2);
        });

        it('uses the same options on retry', function () {
            mockSequenceGenerator('0000000000', 'ABC1230001');

            $generator = new CnpjGeneratorCallsSpy();
            $result = $generator->generate(
                format: false,
                prefix: '00',
                type: CnpjType::Alphanumeric,
            );

            expect($result)->toHaveLength(14);
            expect($result)->toStartWith('00ABC1230001');
            expect($generator->callsArguments)->toBe([
                [null, false, '00', CnpjType::Alphanumeric],
                [null, false, '00', CnpjType::Alphanumeric],
            ]);
        });
    });
});
