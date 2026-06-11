<?php

declare(strict_types=1);

namespace Scripts\Commands;

use function git_lines;
use function monorepo_config_path;
use function monorepo_root;
use function run_vendor_bin;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PhpStanStagedCommand extends Command
{
    private const PHP_EXTENSIONS = ['php'];

    protected function configure(): void
    {
        $this
            ->setName('phpstan:staged')
            ->setDescription('Run PhpStan only on git-staged PHP files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            return $this->runPhpStanStaged($output);
        } catch (RuntimeException $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');

            return Command::FAILURE;
        }
    }

    private function runPhpStanStaged(OutputInterface $output): int
    {
        $output->writeln('Checking staged files with PhpStan...');

        $stagedFiles = git_lines(['diff', '--cached', '--name-only', '--diff-filter=ACM']);

        if ($stagedFiles === []) {
            $output->writeln('No staged PHP files found.');

            return Command::SUCCESS;
        }

        $phpFiles = $this->filterPhpFiles($stagedFiles);

        if ($phpFiles === []) {
            $output->writeln('No staged PHP files found.');

            return Command::SUCCESS;
        }

        $output->writeln('Staged PHP files found: ' . count($phpFiles));

        foreach ($phpFiles as $file) {
            $output->writeln("  - {$file}");
        }

        $output->writeln('');
        $output->writeln('Running PhpStan on staged files...');

        $returnCode = run_vendor_bin('phpstan', array_merge(
            [
                'analyse',
                '--no-progress',
                '--configuration=' . monorepo_config_path('.php-stan.config.neon'),
            ],
            $phpFiles,
        ), monorepo_root());

        if ($returnCode !== 0) {
            $output->writeln('<error>PhpStan found issues on staged files.</error>');

            return $returnCode;
        }

        $output->writeln('PhpStan passed with no issues on staged files.');

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
}
