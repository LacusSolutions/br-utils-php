<?php

declare(strict_types=1);

use Symfony\Component\Filesystem\Path;

require __DIR__ . '/helpers.php';

function print_release_usage(): void
{
    fwrite(STDERR, "Usage: php scripts/release.php <package> [-v|--version X.Y.Z]\n");
}

/**
 * @param list<string> $arguments
 *
 * @return array{package: ?string, version: ?string}
 */
function parse_release_arguments(array $arguments): array
{
    $package = null;
    $version = null;

    for ($index = 0, $count = count($arguments); $index < $count; ++$index) {
        $argument = $arguments[$index];

        if ($argument === '-v' || $argument === '--version') {
            if (!isset($arguments[$index + 1])) {
                fwrite(STDERR, "Missing value for {$argument}.\n");
                print_release_usage();

                exit(1);
            }

            $version = $arguments[++$index];

            continue;
        }

        if (str_starts_with($argument, '--version=')) {
            $version = substr($argument, strlen('--version='));

            if ($version === '') {
                fwrite(STDERR, "Missing value for --version.\n");
                print_release_usage();

                exit(1);
            }

            continue;
        }

        if (str_starts_with($argument, '-')) {
            fwrite(STDERR, "Unknown option: {$argument}\n");
            print_release_usage();

            exit(1);
        }

        if ($package !== null) {
            fwrite(STDERR, "Unexpected argument: {$argument}\n");
            print_release_usage();

            exit(1);
        }

        $package = $argument;
    }

    return ['package' => $package, 'version' => $version];
}

/**
 * @return array<string, string>
 */
function extract_changelog_bodies(string $markdown): array
{
    $pattern = '/^## (\d+\.\d+\.\d+)\s*\r?\n(.*?)(?=^## \d+\.\d+\.\d+\s*\r?\n|\z)/ms';

    if (!preg_match_all($pattern, $markdown, $matches, PREG_SET_ORDER)) {
        return [];
    }

    $bodies = [];

    foreach ($matches as $match) {
        $bodies[$match[1]] = rtrim($match[2]);
    }

    return $bodies;
}

$parsedArguments = parse_release_arguments(script_arguments());
$package = $parsedArguments['package'];
$requestedVersion = $parsedArguments['version'];

if ($package === null) {
    fwrite(STDERR, "Missing required package argument.\n");
    print_release_usage();

    exit(1);
}

if (!in_array($package, package_names(), true)) {
    fwrite(STDERR, "Invalid package: {$package}\n");
    fwrite(STDERR, 'Available packages: ' . implode(', ', package_names()) . "\n");

    exit(1);
}

if ($requestedVersion !== null && !preg_match('/^\d+\.\d+\.\d+$/', $requestedVersion)) {
    fwrite(STDERR, "Invalid version format: {$requestedVersion}\n");
    fwrite(STDERR, "Expected format: X.Y.Z (e.g. 1.2.3)\n");

    exit(1);
}

$changelogPath = Path::join(packages_directory(), $package, 'CHANGELOG.md');

if (!is_readable($changelogPath)) {
    fwrite(STDERR, "Changelog not found: {$changelogPath}\n");

    exit(1);
}

$changelogContents = file_get_contents($changelogPath);

if ($changelogContents === false) {
    fwrite(STDERR, "Failed to read changelog: {$changelogPath}\n");

    exit(1);
}

$versionBodies = extract_changelog_bodies($changelogContents);

if ($versionBodies === []) {
    fwrite(STDERR, "No version sections found in changelog: {$changelogPath}\n");

    exit(1);
}

if ($requestedVersion !== null) {
    if (!array_key_exists($requestedVersion, $versionBodies)) {
        fwrite(STDERR, "Version not found in changelog: {$requestedVersion}\n");
        fwrite(STDERR, 'Available versions: ' . implode(', ', array_keys($versionBodies)) . "\n");

        exit(1);
    }

    $version = $requestedVersion;
} else {
    $version = array_key_first($versionBodies);
}

$releaseDirectory = Path::join(monorepo_root(), '.release');

if (!is_dir($releaseDirectory) && !mkdir($releaseDirectory, 0777, true) && !is_dir($releaseDirectory)) {
    fwrite(STDERR, "Failed to create release directory: {$releaseDirectory}\n");

    exit(1);
}

$outputPath = Path::join($releaseDirectory, "{$package}@{$version}.md");
$outputContents = $versionBodies[$version] . "\n";

if (file_put_contents($outputPath, $outputContents) === false) {
    fwrite(STDERR, "Failed to write release notes: {$outputPath}\n");

    exit(1);
}

echo $outputPath . "\n";

exit(0);
