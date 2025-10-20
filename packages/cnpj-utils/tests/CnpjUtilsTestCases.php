<?php

declare(strict_types=1);

namespace Lacus\CnpjUtils\Tests;

use Lacus\CnpjFmt\Tests\CnpjFormatterTestCases;
use Lacus\CnpjGen\Tests\CnpjGeneratorTestCases;
use Lacus\CnpjVal\Tests\CnpjValidatorTestCases;

trait CnpjUtilsTestCases
{
    use CnpjFormatterTestCases;
    use CnpjGeneratorTestCases;
    use CnpjValidatorTestCases;
}
