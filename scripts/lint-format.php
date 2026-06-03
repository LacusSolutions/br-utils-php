<?php

declare(strict_types=1);

require __DIR__ . '/helpers.php';

$arguments = array_slice($_SERVER['argv'], 1);
$splitArguments = split_script_arguments($arguments);
$lintPaths = resolve_lint_paths($splitArguments['paths']);
$packageGroups = group_lint_paths_by_package($lintPaths);

foreach ($packageGroups as $packageDirectory => $relativePaths) {
    $relativePaths = array_values(array_filter(
        $relativePaths,
        static fn (string $relativePath): bool => $relativePath !== '' && $relativePath !== '.',
    ));

    $fixerArguments = array_merge(
        [
            'fix',
            '--config=' . monorepo_config_path('.php-cs-fixer.config.php'),
        ],
        $splitArguments['options'],
        $relativePaths,
    );

    $exitCode = run_vendor_bin('php-cs-fixer', $fixerArguments, $packageDirectory);

    if ($exitCode !== 0) {
        exit($exitCode);
    }
}

exit(0);
