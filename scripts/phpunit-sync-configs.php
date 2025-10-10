<?php

/**
 * Script to generate PHPUnit XML configurations for all packages
 */

$filename = __DIR__ . '/phpunit.common.xml';
$template = file_get_contents($filename);

$packages = [
    'cnpj-fmt' => 'CNPJ Formatter',
    'cnpj-gen' => 'CNPJ Generator',
    'cnpj-val' => 'CNPJ Validator',
    'cpf-fmt' => 'CPF Formatter',
    'cpf-gen' => 'CPF Generator',
    'cpf-val' => 'CPF Validator',
    'cnpj-utils' => 'CNPJ Utils',
    'cpf-utils' => 'CPF Utils',
    'br-utils' => 'BR Utils',
];

foreach ($packages as $packageDir => $packageName) {
    $config = str_replace('{{PACKAGE_NAME}}', $packageName, $template);
    $filePath = __DIR__ . "/../packages/{$packageDir}/phpunit.xml";

    file_put_contents($filePath, $config);
    echo "✅ Generated: {$filePath}\n";
}

echo "\n📝 All PHPUnit XML configurations generated successfully!\n";
