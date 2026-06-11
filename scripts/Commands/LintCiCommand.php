<?php

declare(strict_types=1);

namespace Scripts\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LintCiCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('lint:ci')
            ->setDescription('Dry-run format check, then dry-run static analysis (CI equivalent)')
            ->addArgument('paths', InputArgument::IS_ARRAY, 'Package names or paths to lint');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();

        if ($application === null) {
            return Command::FAILURE;
        }

        /** @var list<string> $paths */
        $paths = $input->getArgument('paths');

        $formatCommand = $application->find('lint:format');
        $formatExitCode = $formatCommand->run(new ArrayInput([
            'command' => 'lint:format',
            'paths' => $paths,
            '--dry-run' => true,
        ]), $output);

        if ($formatExitCode !== Command::SUCCESS) {
            return $formatExitCode;
        }

        $checkCommand = $application->find('lint:check');

        return $checkCommand->run(new ArrayInput([
            'command' => 'lint:check',
            'paths' => $paths,
            '--dry-run' => true,
        ]), $output);
    }
}
