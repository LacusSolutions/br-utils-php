<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$dir = getcwd() ?: __DIR__;
$dir .= DIRECTORY_SEPARATOR;
$vendorDir = $dir . 'vendor' . DIRECTORY_SEPARATOR;
$searchDirs = [];

foreach (['src', 'tests', 'scripts'] as $directory) {
    $path = $dir . $directory . DIRECTORY_SEPARATOR;

    if (is_dir($path)) {
        $searchDirs[] = $path;
    }
}

if ($searchDirs === []) {
    $searchDirs[] = $dir;
}

$cacheFile = "{$vendorDir}.php-cs-fixer.cache";
$finder = Finder::create()
    ->in($searchDirs)
    ->exclude([$vendorDir]);
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

$config = new Config();

$config->setCacheFile($cacheFile);
$config->setFinder($finder);
$config->setRules($rules);

return $config;
