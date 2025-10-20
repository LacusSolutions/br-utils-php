<?php

/**
 * Script to run PhpStan only on staged files
 *
 * This script:
 * 1. Gets the list of PHP files in the staging area
 * 2. Runs PhpStan only on those files
 */

declare(strict_types=1);

class PhpStanStaged
{
    private const PHP_EXTENSIONS = ['php'];
    private const GIT_DIFF_COMMAND = 'git diff --cached --name-only --diff-filter=ACM';
    private const PHPSTAN_COMMAND = 'vendor/bin/phpstan analyse --no-progress';

    public function run(): int
    {
        echo "🔍 Checking staged files with PhpStan...\n";

        $stagedFiles = $this->getStagedFiles();

        if (empty($stagedFiles)) {
            echo "✅ No staged PHP files found.\n";

            return 0;
        }

        $phpFiles = $this->filterPhpFiles($stagedFiles);

        if (empty($phpFiles)) {
            echo "✅ No staged PHP files found.\n";

            return 0;
        }

        echo "📁 Staged PHP files found: " . count($phpFiles) . "\n";

        foreach ($phpFiles as $file) {
            echo "  - $file\n";
        }

        echo "\n🔧 Running PhpStan on staged files...\n";

        $command = self::PHPSTAN_COMMAND . ' ' . implode(' ', $phpFiles);
        $output = [];
        $returnCode = 0;

        exec($command, $output, $returnCode);

        // Show PhpStan output
        if (!empty($output)) {
            echo implode("\n", $output) . "\n";
        }

        if ($returnCode !== 0) {
            echo "❌ PhpStan found issues on staged files.\n";

            return $returnCode;
        }

        echo "✅ PhpStan passed with no issues on staged files.\n";

        return 0;
    }

    private function getStagedFiles(): array
    {
        $output = [];
        $returnCode = 0;

        exec(self::GIT_DIFF_COMMAND, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new RuntimeException('Error running git diff: ' . implode("\n", $output));
        }

        return array_filter($output, fn ($file) => !empty(trim($file)));
    }

    private function filterPhpFiles(array $files): array
    {
        return array_filter($files, function ($file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            return in_array($extension, self::PHP_EXTENSIONS, true);
        });
    }
}

// Run the script
try {
    $phpStanStaged = new PhpStanStaged();

    exit($phpStanStaged->run());
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";

    exit(1);
}
