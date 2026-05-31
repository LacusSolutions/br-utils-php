<?php

declare(strict_types=1);

use Symfony\Component\Filesystem\Path;
use Symfony\Component\Process\Process;

require_once dirname(__DIR__) . '/vendor/autoload.php';

function monorepo_root(): string
{
    return dirname(__DIR__);
}

function monorepo_config_path(string $filename): string
{
    return Path::join(monorepo_root(), $filename);
}

function find_vendor_bin(string $name): string
{
    $path = Path::join(monorepo_root(), 'vendor', 'bin', $name);

    if (!is_file($path)) {
        fwrite(STDERR, "Vendor binary not found: {$path}\n");
        fwrite(STDERR, "Run `composer install` from the monorepo root (`php/`).\n");

        exit(1);
    }

    return $path;
}

function run_process(array $command, ?string $workingDirectory = null): int
{
    $process = new Process(
        $command,
        $workingDirectory ?? (getcwd() ?: null),
    );

    $process->setTimeout(null);
    $process->run(function (string $type, string $buffer): void {
        fwrite(Process::ERR === $type ? STDERR : STDOUT, $buffer);
    });

    return $process->getExitCode() ?? 1;
}

function run_vendor_bin(string $name, array $arguments = [], ?string $workingDirectory = null): int
{
    return run_process(
        array_merge([PHP_BINARY, find_vendor_bin($name)], $arguments),
        $workingDirectory,
    );
}

function run_git(array $arguments, ?string $workingDirectory = null): string
{
    $process = new Process(
        array_merge(['git'], $arguments),
        $workingDirectory ?? monorepo_root(),
    );

    $process->run();

    if (!$process->isSuccessful()) {
        throw new RuntimeException(
            trim($process->getErrorOutput() ?: $process->getOutput()) ?: 'Git command failed.',
        );
    }

    return $process->getOutput();
}

function git_lines(array $arguments, ?string $workingDirectory = null): array
{
    $output = trim(run_git($arguments, $workingDirectory));

    if ($output === '') {
        return [];
    }

    return array_values(array_filter(
        explode("\n", $output),
        static fn (string $line): bool => trim($line) !== '',
    ));
}

function package_directory_for(string $file): ?string
{
    if (!preg_match('#^packages/([^/]+)/#', $file, $matches)) {
        return null;
    }

    return Path::join(monorepo_root(), 'packages', $matches[1]);
}

function path_relative_to(string $path, string $basePath): string
{
    $absolutePath = Path::isAbsolute($path)
        ? $path
        : Path::join(monorepo_root(), $path);

    return Path::makeRelative(
        Path::canonicalize($absolutePath),
        Path::canonicalize($basePath),
    );
}
