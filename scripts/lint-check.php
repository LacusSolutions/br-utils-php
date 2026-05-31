<?php

declare(strict_types=1);

require __DIR__ . '/helpers.php';

$extraArguments = array_slice($_SERVER['argv'], 1);

exit(run_vendor_bin('phpstan', array_merge(
    [
        'analyse',
        '--configuration=' . monorepo_config_path('.php-stan.config.neon'),
        'src',
        'tests',
    ],
    $extraArguments,
)));
