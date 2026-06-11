<?php

declare(strict_types=1);

namespace Scripts\Commands;

use function git_lines;
use function monorepo_config_path;
use function monorepo_root;
use function package_directory_for;
use function path_relative_to;
use function run_git;
use function run_vendor_bin;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LintStagedCommand extends Command
{
    private const PHP_EXTENSIONS = ['php'];

    protected function configure(): void
    {
        $this
            ->setName('lint:staged')
            ->setDescription('Lint only git-staged PHP files (pre-commit hook)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            return $this->runLintStaged($output);
        } catch (RuntimeException $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');

            return Command::FAILURE;
        }
    }

    private function runLintStaged(OutputInterface $output): int
    {
        $output->writeln('Checking staged files...');

        $stagedFiles = git_lines(['diff', '--cached', '--name-only', '--diff-filter=ACM']);

        if ($stagedFiles === []) {
            $output->writeln('No staged PHP files found.');

            return Command::SUCCESS;
        }

        $output->writeln('Staged files found: ' . count($stagedFiles));

        foreach ($stagedFiles as $file) {
            $output->writeln("  - {$file}");
        }

        $phpFiles = $this->filterPhpFiles($stagedFiles);

        if ($phpFiles === []) {
            $output->writeln('No staged PHP files found.');

            return Command::SUCCESS;
        }

        $output->writeln('');
        $output->writeln('Running php-cs-fixer on staged files...');

        $hasChanges = $this->runPhpCsFixer($phpFiles, $output);

        if ($hasChanges === null) {
            return Command::FAILURE;
        }

        if ($hasChanges) {
            $output->writeln('Fixes applied. Adding fixed files to the staging area...');

            if (!$this->addFilesToStaging($phpFiles, $output)) {
                $output->writeln('<error>Failed to add fixed files to the staging area.</error>');

                return Command::FAILURE;
            }

            $output->writeln('Fixed files added to the staging area.');
        }

        if (!$hasChanges) {
            $output->writeln('No fixes needed.');
        }

        return $this->runPhpStanCheck($phpFiles, $output);
    }

    /**
     * @param list<string> $files
     */
    private function runPhpStanCheck(array $files, OutputInterface $output): int
    {
        $output->writeln('');
        $output->writeln('Running PhpStan on staged files...');

        $phpStanResult = $this->runPhpStan($files);

        if ($phpStanResult !== 0) {
            $output->writeln('<error>PhpStan found issues on staged files.</error>');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * @param list<string> $files
     *
     * @return list<string>
     */
    private function filterPhpFiles(array $files): array
    {
        return array_values(array_filter($files, function (string $file): bool {
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            return in_array($extension, self::PHP_EXTENSIONS, true);
        }));
    }

    /**
     * @param list<string> $files
     */
    private function runPhpCsFixer(array $files, OutputInterface $output): ?bool
    {
        $hasChanges = false;

        foreach ($files as $file) {
            $output->writeln("  Processing: {$file}");

            $packageDirectory = package_directory_for($file);

            if ($packageDirectory === null) {
                $output->writeln("  Skipping file outside the monorepo: {$file}");

                continue;
            }

            $relativePath = path_relative_to($file, $packageDirectory);
            $returnCode = run_vendor_bin('php-cs-fixer', [
                'fix',
                '--config=' . monorepo_config_path('.php-cs-fixer.config.php'),
                $relativePath,
            ], $packageDirectory);

            if ($returnCode !== 0) {
                $output->writeln("<error>Error processing {$file} (exit code {$returnCode}).</error>");

                return null;
            }

            if (git_lines(['diff', '--name-only', $file]) !== []) {
                $output->writeln("  Fixes applied to: {$file}");
                $hasChanges = true;

                continue;
            }

            $output->writeln("  No fixes needed for: {$file}");
        }

        return $hasChanges;
    }

    /**
     * @param list<string> $files
     */
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

    /**
     * @param list<string> $files
     */
    private function addFilesToStaging(array $files, OutputInterface $output): bool
    {
        $failed = false;

        foreach ($files as $file) {
            try {
                run_git(['add', $file]);
            } catch (RuntimeException $exception) {
                $output->writeln("  Error adding {$file} to the staging area: {$exception->getMessage()}");
                $failed = true;
            }
        }

        return !$failed;
    }
}
