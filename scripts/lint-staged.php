<?php

/**
 * Script to run lint only on staged files (similar to JS lint-staged)
 *
 * This script:
 * 1. Gets the list of PHP files in the staging area
 * 2. Runs php-cs-fixer only on those files
 * 3. If there are fixes, adds the fixed files back to the staging area
 */

declare(strict_types=1);

class LintStaged
{
    private const PHP_EXTENSIONS = ['php'];
    private const GIT_DIFF_COMMAND = 'git diff --cached --name-only --diff-filter=ACM';
    private const PHP_CS_FIXER_COMMAND = 'vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php';
    private const PHPSTAN_COMMAND = 'vendor/bin/phpstan analyse --no-progress';
    private const GIT_ADD_COMMAND = 'git add';

    public function run(): int
    {
        echo "🔍 Checking staged files...\n";

        $stagedFiles = $this->getStagedFiles();

        if (empty($stagedFiles)) {
            echo "✅ No staged PHP files found.\n";

            return 0;
        }

        echo "📁 Staged files found: " . count($stagedFiles) . "\n";

        foreach ($stagedFiles as $file) {
            echo "  - $file\n";
        }

        $phpFiles = $this->filterPhpFiles($stagedFiles);

        if (empty($phpFiles)) {
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
            $this->addFilesToStaging($phpFiles);
            echo "✅ Fixed files added to the staging area.\n";
        } else {
            echo "✅ No fixes needed.\n";
        }

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

    private function runPhpCsFixer(array $files): bool
    {
        $hasChanges = false;

        foreach ($files as $file) {
            echo "  🔧 Processing: $file\n";

            // Run php-cs-fixer on the specific file
            $command = self::PHP_CS_FIXER_COMMAND . " $file";
            $output = [];
            $returnCode = 0;

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                echo "  ⚠️  Error processing $file: " . implode("\n", $output) . "\n";

                continue;
            }

            // Check whether the file has changes
            $gitStatusCommand = "git diff --name-only $file";
            $gitOutput = [];
            exec($gitStatusCommand, $gitOutput);

            if (!empty($gitOutput)) {
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
        $command = self::PHPSTAN_COMMAND . ' ' . implode(' ', $files);
        $output = [];
        $returnCode = 0;

        exec($command, $output, $returnCode);

        // Show PhpStan output only if there are problems
        if ($returnCode !== 0 && !empty($output)) {
            echo "  ⚠️  PhpStan found issues:\n";

            foreach ($output as $line) {
                echo "    $line\n";
            }
        }

        return $returnCode;
    }

    private function addFilesToStaging(array $files): void
    {
        foreach ($files as $file) {
            $command = self::GIT_ADD_COMMAND . " $file";
            $output = [];
            $returnCode = 0;

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                echo "  ⚠️  Error adding $file to the staging area: " . implode("\n", $output) . "\n";
            }
        }
    }
}

// Run the script
try {
    $lintStaged = new LintStaged();

    exit($lintStaged->run());
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";

    exit(1);
}
