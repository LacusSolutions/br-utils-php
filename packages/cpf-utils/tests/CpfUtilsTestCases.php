<?php

declare(strict_types=1);

namespace Lacus\CpfUtils\Tests;

use Lacus\CpfFmt\Tests\CpfFormatterTestCases;
use Lacus\CpfGen\Tests\CpfGeneratorTestCases;
use Lacus\CpfVal\Tests\CpfValidatorTestCases;

trait CpfUtilsTestCases
{
    use CpfFormatterTestCases;
    use CpfGeneratorTestCases;
    use CpfValidatorTestCases;
}
