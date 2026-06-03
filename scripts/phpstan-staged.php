<?php

/**
 * Script to run PhpStan only on staged files
 *
 * This script:
 * 1. Gets the list of PHP files in the staging area
 * 2. Runs PhpStan only on those files
 */

declare(strict_types=1);

require __DIR__ . '/helpers.php';

class PhpStanStaged
{
    private const PHP_EXTENSIONS = ['php'];

    public function run(): int
    {
        echo "🔍 Checking staged files with PhpStan...\n";

        $stagedFiles = git_lines(['diff', '--cached', '--name-only', '--diff-filter=ACM']);

        if ($stagedFiles === []) {
            echo "✅ No staged PHP files found.\n";

            return 0;
        }

        $phpFiles = $this->filterPhpFiles($stagedFiles);

        if ($phpFiles === []) {
            echo "✅ No staged PHP files found.\n";

            return 0;
        }

        echo "📁 Staged PHP files found: " . count($phpFiles) . "\n";

        foreach ($phpFiles as $file) {
            echo "  - $file\n";
        }

        echo "\n🔧 Running PhpStan on staged files...\n";

        $returnCode = run_vendor_bin('phpstan', array_merge(
            [
                'analyse',
                '--no-progress',
                '--configuration=' . monorepo_config_path('.php-stan.config.neon'),
            ],
            $phpFiles,
        ), monorepo_root());

        if ($returnCode !== 0) {
            echo "❌ PhpStan found issues on staged files.\n";

            return $returnCode;
        }

        echo "✅ PhpStan passed with no issues on staged files.\n";

        return 0;
    }

    private function filterPhpFiles(array $files): array
    {
        return array_values(array_filter($files, function (string $file): bool {
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            return in_array($extension, self::PHP_EXTENSIONS, true);
        }));
    }
}

try {
    $phpStanStaged = new PhpStanStaged();

    exit($phpStanStaged->run());
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";

    exit(1);
}
