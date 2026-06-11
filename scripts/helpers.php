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

/**
 * @param list<string> $command
 */
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

/**
 * @param list<string> $arguments
 */
function run_vendor_bin(string $name, array $arguments = [], ?string $workingDirectory = null): int
{
    $path = Path::join(monorepo_root(), 'vendor', 'bin', $name);

    if (!is_file($path)) {
        fwrite(STDERR, "Vendor binary not found: {$path}\n");
        fwrite(STDERR, "Run `composer install` from the monorepo root.\n");

        exit(1);
    }

    return run_process(
        array_merge([PHP_BINARY, $path], $arguments),
        $workingDirectory,
    );
}

/**
 * @param list<string> $arguments
 */
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

/**
 * @param list<string> $arguments
 *
 * @return list<string>
 */
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

function packages_directory(): string
{
    return Path::join(monorepo_root(), 'packages');
}

/**
 * @return list<string>
 */
function package_names(): array
{
    /** @var list<string>|null $names */
    static $names = null;

    if ($names !== null) {
        return $names;
    }

    $names = [];

    foreach (scandir(packages_directory()) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        if (is_dir(Path::join(packages_directory(), $entry))) {
            $names[] = $entry;
        }
    }

    sort($names);

    return $names;
}

function resolve_lint_path(string $argument): ?string
{
    if (!str_contains($argument, '/') && !Path::isAbsolute($argument)) {
        $packagePath = Path::join(packages_directory(), $argument);

        if (is_dir($packagePath)) {
            return Path::canonicalize($packagePath);
        }
    }

    if (!Path::isAbsolute($argument)) {
        foreach (array_unique([monorepo_root(), getcwd() ?: monorepo_root()]) as $basePath) {
            $candidate = Path::join($basePath, $argument);

            if (file_exists($candidate)) {
                return Path::canonicalize($candidate);
            }
        }
    }

    if (Path::isAbsolute($argument) && file_exists($argument)) {
        return Path::canonicalize($argument);
    }

    return null;
}

/**
 * @param list<string> $pathArguments
 *
 * @return list<string>
 */
function resolve_lint_paths(array $pathArguments): array
{
    if ($pathArguments === []) {
        return array_values(array_unique(array_merge(
            ['scripts'],
            array_map(
                static fn (string $name): string => Path::join('packages', $name),
                package_names(),
            ),
        )));
    }

    $resolvedPaths = [];

    foreach ($pathArguments as $argument) {
        $absolutePath = resolve_lint_path($argument);

        if ($absolutePath === null) {
            fwrite(STDERR, "Invalid lint path: {$argument}\n");
            fwrite(STDERR, 'Available packages: ' . implode(', ', package_names()) . "\n");

            exit(1);
        }

        $resolvedPaths[] = path_relative_to($absolutePath, monorepo_root());
    }

    return array_values(array_unique($resolvedPaths));
}

function package_directory_for(string $file): ?string
{
    return package_directory_for_absolute(Path::join(monorepo_root(), $file));
}

function package_directory_for_absolute(string $absolutePath): ?string
{
    $monorepoRoot = Path::canonicalize(monorepo_root());
    $packagesDir = Path::canonicalize(packages_directory());
    $absolutePath = Path::canonicalize($absolutePath);

    if (str_starts_with($absolutePath, $packagesDir . DIRECTORY_SEPARATOR)
        || $absolutePath === $packagesDir) {
        $relativePath = Path::makeRelative($absolutePath, $packagesDir);
        $packageName = explode('/', str_replace('\\', '/', $relativePath))[0] ?? '';

        if ($packageName === '' || $packageName === '.') {
            return null;
        }

        $packageDirectory = Path::join($packagesDir, $packageName);

        return is_dir($packageDirectory) ? $packageDirectory : null;
    }

    if (str_starts_with($absolutePath, $monorepoRoot . DIRECTORY_SEPARATOR)
        || $absolutePath === $monorepoRoot) {
        return $monorepoRoot;
    }

    return null;
}

/**
 * @param list<string> $monorepoRelativePaths
 *
 * @return array<string, list<string>>
 */
function group_lint_paths_by_package(array $monorepoRelativePaths): array
{
    $groups = [];

    foreach ($monorepoRelativePaths as $monorepoRelativePath) {
        $absolutePath = Path::join(monorepo_root(), $monorepoRelativePath);
        $packageDirectory = package_directory_for_absolute($absolutePath);

        if ($packageDirectory === null) {
            fwrite(STDERR, "Lint path is outside the monorepo: {$monorepoRelativePath}\n");

            exit(1);
        }

        $relativePath = path_relative_to($absolutePath, $packageDirectory);
        $groups[$packageDirectory][] = $relativePath;
    }

    foreach ($groups as $packageDirectory => $relativePaths) {
        $groups[$packageDirectory] = array_values(array_unique($relativePaths));
    }

    return $groups;
}

/**
 * @param list<string> $relativePaths
 *
 * @return list<string>
 */
function normalize_package_lint_paths(array $relativePaths, string $packageDirectory): array
{
    $relativePaths = array_values(array_filter(
        $relativePaths,
        static fn (string $relativePath): bool => $relativePath !== '' && $relativePath !== '.',
    ));

    if ($relativePaths !== []) {
        return $relativePaths;
    }

    $defaults = [];

    $defaultDirectories = Path::canonicalize($packageDirectory) === Path::canonicalize(monorepo_root())
        ? ['scripts']
        : ['src', 'tests'];

    foreach ($defaultDirectories as $directory) {
        if (is_dir(Path::join($packageDirectory, $directory))) {
            $defaults[] = $directory;
        }
    }

    return $defaults;
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
