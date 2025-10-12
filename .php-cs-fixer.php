<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/packages/**/src/',
        __DIR__ . '/packages/**/tests/',
    ])
    ->exclude([
        'vendor/',
        'tests/',
    ]);

return (new Config())
    ->setRules([
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
        'not_operator_with_successor_space' => false,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
        ],
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name' => true,
        'trailing_comma_in_multiline' => true,
    ])
    ->setFinder($finder);
