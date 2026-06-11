<?php

declare(strict_types=1);

namespace Scripts\Commands;

use function group_lint_paths_by_package;
use function monorepo_config_path;
use function normalize_package_lint_paths;
use function resolve_lint_paths;
use function run_vendor_bin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class LintFormatCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('lint:format')
            ->setDescription('Apply php-cs-fixer to monorepo paths')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Run without applying fixes')
            ->addArgument('paths', InputArgument::IS_ARRAY, 'Package names or paths to lint');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var list<string> $paths */
        $paths = $input->getArgument('paths');
        $lintPaths = resolve_lint_paths($paths);
        $packageGroups = group_lint_paths_by_package($lintPaths);
        $toolOptions = $input->getOption('dry-run') ? ['--dry-run', '--diff'] : [];

        foreach ($packageGroups as $packageDirectory => $relativePaths) {
            $relativePaths = normalize_package_lint_paths($relativePaths, $packageDirectory);

            $exitCode = run_vendor_bin('php-cs-fixer', array_merge(
                [
                    'fix',
                    '--config=' . monorepo_config_path('.php-cs-fixer.config.php'),
                ],
                $toolOptions,
                $relativePaths,
            ), $packageDirectory);

            if ($exitCode !== 0) {
                return $exitCode;
            }
        }

        return Command::SUCCESS;
    }
}
