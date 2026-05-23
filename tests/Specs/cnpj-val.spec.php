<?php

declare(strict_types=1);

use function Lacus\BrUtils\Cnpj\cnpj_val;

use Lacus\BrUtils\Cnpj\CnpjValidator;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;

describe('cnpj_val', function () {
    describe('when called', function () {
        it('matches `CnpjValidator::isValid` behavior', function () {
            $input = '91415732000793';
            $validator = new CnpjValidator();

            $result = cnpj_val($input);

            expect($result)->toBe($validator->isValid($input));
        });

        it('accepts options and forwards validation behavior', function () {
            $input = '01ABC234000X56';
            $options = ['type' => CnpjValidationType::Numeric];

            $result = cnpj_val($input, ...$options);

            expect($result)->toBeFalse();
        });
    });
});
