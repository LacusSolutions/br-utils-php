<?php

/**
 * Test script for lint-staged
 *
 * This script simulates lint-staged behavior without committing
 */

declare(strict_types=1);

echo "🧪 Testing lint-staged...\n\n";

// Check if we are in a git repository
$gitCheck = [];
exec('git rev-parse --git-dir 2>/dev/null', $gitCheck, $returnCode);

if ($returnCode !== 0) {
    echo "❌ Error: Not in a Git repository.\n";

    exit(1);
}

// Check if there are staged files
$stagedFiles = [];
exec('git diff --cached --name-only --diff-filter=ACM', $stagedFiles, $returnCode);

if (empty($stagedFiles)) {
    echo "⚠️  No staged files found.\n";
    echo "💡 To test, add some files with: git add <file>\n";

    exit(0);
}

echo "📁 Staged files found:\n";

foreach ($stagedFiles as $file) {
    echo "  - $file\n";
}

// Filter only PHP files
$phpFiles = array_filter($stagedFiles, function ($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'php';
});

if (empty($phpFiles)) {
    echo "✅ No staged PHP files found.\n";

    exit(0);
}

echo "\n🔧 Running lint-staged on staged PHP files...\n";

// Run the lint-staged script
$command = 'php scripts/lint-staged.php';
$output = [];
$returnCode = 0;

exec($command, $output, $returnCode);

// Show the output
foreach ($output as $line) {
    echo $line . "\n";
}

if ($returnCode === 0) {
    echo "\n✅ Test completed successfully!\n";
} else {
    echo "\n❌ Test failed with exit code: $returnCode\n";
}

exit($returnCode);
