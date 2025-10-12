<?php

declare(strict_types=1);

namespace Lacus\CpfGen\Tests\Utils;

trait EnvironmentVariables
{
    private static bool $envLoaded = false;

    public static function setUpBeforeClass(): void
    {
        if (! self::$envLoaded) {
            self::loadTestEnv();
            self::$envLoaded = true;
        }
    }

    private static function loadTestEnv(): void
    {
        $envFile = getcwd() . DIRECTORY_SEPARATOR . '.env.test';
        $envExampleFile = getcwd() . DIRECTORY_SEPARATOR . '.env.example';

        if (! file_exists($envFile)) {
            copy($envExampleFile, $envFile);

            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            putenv("{$name}={$value}");
        }
    }
}
