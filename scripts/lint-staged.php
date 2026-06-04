<?php

/**
 * Script to run lint only on staged files.
 *
 * This script:
 * 1. Gets the list of PHP files in the staging area;
 * 2. Runs `php-cs-fixer` only on those files;
 * 3. If there are fixes, adds the fixed files back to the staging area;
 */

declare(strict_types=1);

require __DIR__ . '/helpers.php';

class LintStaged
{
    private const PHP_EXTENSIONS = ['php'];

    public function run(): int
    {
        echo "🔍 Checking staged files...\n";

        $stagedFiles = git_lines(['diff', '--cached', '--name-only', '--diff-filter=ACM']);

        if ($stagedFiles === []) {
            echo "✅ No staged PHP files found.\n";

            return 0;
        }

        echo "📁 Staged files found: " . count($stagedFiles) . "\n";

        foreach ($stagedFiles as $file) {
            echo "  - $file\n";
        }

        $phpFiles = $this->filterPhpFiles($stagedFiles);

        if ($phpFiles === []) {
            echo "✅ No staged PHP files found.\n";

            return 0;
        }

        echo "\n🔧 Running PhpStan on staged files...\n";

        $phpStanResult = $this->runPhpStan($phpFiles);

        if ($phpStanResult !== 0) {
            echo "❌ PhpStan found issues. Fixing with php-cs-fixer...\n";
        }

        echo "\n🔧 Running php-cs-fixer on staged files...\n";

        $hasChanges = $this->runPhpCsFixer($phpFiles);

        if ($hasChanges) {
            echo "✨ Fixes applied! Adding fixed files to the staging area...\n";

            if (!$this->addFilesToStaging($phpFiles)) {
                echo "❌ Failed to add fixed files to the staging area.\n";

                return 1;
            }

            echo "✅ Fixed files added to the staging area.\n";
        } else {
            echo "✅ No fixes needed.\n";
        }

        return 0;
    }

    private function filterPhpFiles(array $files): array
    {
        return array_values(array_filter($files, function (string $file): bool {
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            return in_array($extension, self::PHP_EXTENSIONS, true);
        }));
    }

    private function runPhpCsFixer(array $files): bool
    {
        $hasChanges = false;

        foreach ($files as $file) {
            echo "  🔧 Processing: $file\n";

            $packageDirectory = package_directory_for($file);

            if ($packageDirectory === null) {
                echo "  ⚠️  Skipping file outside packages/: $file\n";

                continue;
            }

            $relativePath = path_relative_to($file, $packageDirectory);
            $returnCode = run_vendor_bin('php-cs-fixer', [
                'fix',
                '--config=' . monorepo_config_path('.php-cs-fixer.config.php'),
                $relativePath,
            ], $packageDirectory);

            if ($returnCode !== 0) {
                echo "  ⚠️  Error processing $file (exit code {$returnCode}).\n";

                continue;
            }

            if (git_lines(['diff', '--name-only', $file]) !== []) {
                echo "  ✨ Fixes applied to: $file\n";
                $hasChanges = true;
            } else {
                echo "  ✅ No fixes needed for: $file\n";
            }
        }

        return $hasChanges;
    }

    private function runPhpStan(array $files): int
    {
        return run_vendor_bin('phpstan', array_merge(
            [
                'analyse',
                '--no-progress',
                '--configuration=' . monorepo_config_path('.php-stan.config.neon'),
            ],
            $files,
        ), monorepo_root());
    }

    private function addFilesToStaging(array $files): bool
    {
        $failed = false;

        foreach ($files as $file) {
            try {
                run_git(['add', $file]);
            } catch (RuntimeException $exception) {
                echo "  ⚠️  Error adding $file to the staging area: {$exception->getMessage()}\n";
                $failed = true;
            }
        }

        return !$failed;
    }
}

try {
    $lintStaged = new LintStaged();

    exit($lintStaged->run());
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";

    exit(1);
}
