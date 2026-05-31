<?php

declare(strict_types=1);

require __DIR__ . '/helpers.php';

$extraArguments = array_slice($_SERVER['argv'], 1);

exit(run_vendor_bin('php-cs-fixer', array_merge(
    [
        'fix',
        '--config=' . monorepo_config_path('.php-cs-fixer.config.php'),
    ],
    $extraArguments,
)));
