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

uses()->group('isolated-process-tests')->in("{$specsDirectory}*.isolated.spec.php");

uses()->afterEach(fn () => Mockery::close());
