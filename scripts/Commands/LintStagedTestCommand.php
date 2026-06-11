<?php

declare(strict_types=1);

namespace Scripts\Commands;

use function git_lines;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LintStagedTestCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('lint:staged:test')
            ->setDescription('Exercise lint-staged against the current git index');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Testing lint-staged...');
        $output->writeln('');

        $gitDirectory = [];
        $returnCode = 0;
        exec('git rev-parse --git-dir 2>/dev/null', $gitDirectory, $returnCode);

        if ($returnCode !== 0) {
            $output->writeln('<error>Not in a Git repository.</error>');

            return Command::FAILURE;
        }

        $stagedFiles = git_lines(['diff', '--cached', '--name-only', '--diff-filter=ACM']);

        if ($stagedFiles === []) {
            $output->writeln('No staged files found.');
            $output->writeln('To test, add some files with: git add <file>');

            return Command::SUCCESS;
        }

        $output->writeln('Staged files found:');

        foreach ($stagedFiles as $file) {
            $output->writeln("  - {$file}");
        }

        $phpFiles = array_values(array_filter(
            $stagedFiles,
            static fn (string $file): bool => pathinfo($file, PATHINFO_EXTENSION) === 'php',
        ));

        if ($phpFiles === []) {
            $output->writeln('No staged PHP files found.');

            return Command::SUCCESS;
        }

        $output->writeln('');
        $output->writeln('Running lint-staged on staged PHP files...');

        $application = $this->getApplication();

        if ($application === null) {
            return Command::FAILURE;
        }

        $lintStagedCommand = $application->find('lint:staged');
        $lintStagedExitCode = $lintStagedCommand->run(
            new ArrayInput(['command' => 'lint:staged']),
            $output,
        );

        if ($lintStagedExitCode === Command::SUCCESS) {
            $output->writeln('');
            $output->writeln('Test completed successfully.');
        } else {
            $output->writeln('');
            $output->writeln("<error>Test failed with exit code: {$lintStagedExitCode}</error>");
        }

        return $lintStagedExitCode;
    }
}
