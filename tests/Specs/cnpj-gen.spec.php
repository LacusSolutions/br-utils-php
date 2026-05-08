<?php

declare(strict_types=1);

use function Lacus\BrUtils\Cnpj\cnpj_gen;

use Lacus\BrUtils\Cnpj\Enums\CnpjType;

describe('cnpj_gen', function () {
    describe('when called', function () {
        it('matches CnpjGenerator->generate behavior', function () {
            $result = cnpj_gen();

            expect($result)->toMatch('/^[0-9A-Z]{14}$/');
        });

        it('accepts options and forwards generating behavior', function () {
            $options = [
                'format' => true,
                'prefix' => '12345',
                'type' => CnpjType::Numeric,
            ];

            expect(cnpj_gen(...$options))->toMatch('/^12\.345\.\d{3}\/\d{4}-\d{2}$/');
        });
    });
});
