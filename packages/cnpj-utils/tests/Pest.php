<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Pest bootstrap
|--------------------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

$specsDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'specs' . DIRECTORY_SEPARATOR;

uses(TestCase::class)->in($specsDirectory);
