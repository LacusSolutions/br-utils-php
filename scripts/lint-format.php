<?php

declare(strict_types=1);

require __DIR__ . '/helpers.php';

$arguments = script_arguments();
$splitArguments = split_script_arguments($arguments);
$lintPaths = resolve_lint_paths($splitArguments['paths']);
$packageGroups = group_lint_paths_by_package($lintPaths);
$toolOptions = lint_tool_options($splitArguments, ['--dry-run', '--diff']);

foreach ($packageGroups as $packageDirectory => $relativePaths) {
    $relativePaths = normalize_package_lint_paths($relativePaths, $packageDirectory);

    $fixerArguments = array_merge(
        [
            'fix',
            '--config=' . monorepo_config_path('.php-cs-fixer.config.php'),
        ],
        $toolOptions,
        $relativePaths,
    );

    $exitCode = run_vendor_bin('php-cs-fixer', $fixerArguments, $packageDirectory);

    if ($exitCode !== 0) {
        exit($exitCode);
    }
}

exit(0);
