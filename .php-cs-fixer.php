<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return function (string $dir): Config {
    $config = new Config();
    $cacheFile = $dir . '/vendor/.php-cs-fixer.cache';
    $finder = Finder::create()
        ->in([$dir . '/src/', $dir . '/tests/'])
        ->exclude(['vendor/']);
    $rules = [
        '@PSR12' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'blank_line_before_statement' => [
            'statements' => [
                'if',
                'switch',
                'case',
                'default',
                'for',
                'foreach',
                'do',
                'while',
                'continue',
                'phpdoc',
                'return',
                'try',
                'yield',
                'yield_from',
                'declare',
                'exit',
                'goto',
            ],
        ],
        'no_unused_imports' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
        ],
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name' => true,
        'trailing_comma_in_multiline' => true,
    ];

    $config->setCacheFile($cacheFile);
    $config->setFinder($finder);
    $config->setRules($rules);

    return $config;
};
