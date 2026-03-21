<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf\Tests\Mocks;

use Lacus\BrUtils\Cpf\CpfCheckDigits;

final class CpfCheckDigitsWithCalculateSpy extends CpfCheckDigits
{
    public int $calculateCallCount = 0;

    protected function calculate(array $cpfSequence): int
    {
        $this->calculateCallCount++;

        return parent::calculate($cpfSequence);
    }
}
