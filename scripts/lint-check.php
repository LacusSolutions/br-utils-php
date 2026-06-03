<?php

declare(strict_types=1);

require __DIR__ . '/helpers.php';

$arguments = array_slice($_SERVER['argv'], 1);
$splitArguments = split_script_arguments($arguments);
$lintPaths = resolve_lint_paths($splitArguments['paths']);
$packageGroups = group_lint_paths_by_package($lintPaths);

foreach ($packageGroups as $packageDirectory => $relativePaths) {
    $relativePaths = normalize_package_lint_paths($relativePaths, $packageDirectory);

    $exitCode = run_vendor_bin('phpstan', array_merge(
        [
            'analyse',
            '--configuration=' . monorepo_config_path('.php-stan.config.neon'),
        ],
        $splitArguments['options'],
        $relativePaths,
    ), $packageDirectory);

    if ($exitCode !== 0) {
        exit($exitCode);
    }
}

exit(0);
