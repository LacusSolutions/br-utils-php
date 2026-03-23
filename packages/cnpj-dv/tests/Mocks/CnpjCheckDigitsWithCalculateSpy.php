<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Tests\Mocks;

use Lacus\BrUtils\Cnpj\CnpjCheckDigits;

final class CnpjCheckDigitsWithCalculateSpy extends CnpjCheckDigits
{
    public int $calculateCallCount = 0;

    protected function calculate(array $cnpjSequence): int
    {
        $this->calculateCallCount++;

        return parent::calculate($cnpjSequence);
    }
}
