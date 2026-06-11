<?php

declare(strict_types=1);

namespace Scripts;

require_once __DIR__ . '/helpers.php';

use Scripts\Commands\DepsCommand;
use Scripts\Commands\LintCheckCommand;
use Scripts\Commands\LintCiCommand;
use Scripts\Commands\LintCommand;
use Scripts\Commands\LintFormatCommand;
use Scripts\Commands\LintStagedCommand;
use Scripts\Commands\LintStagedTestCommand;
use Scripts\Commands\PhpStanStagedCommand;
use Scripts\Commands\ReleaseCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('br-utils-php');

        $this->addCommands([
            new DepsCommand(),
            new LintCommand(),
            new LintFormatCommand(),
            new LintCheckCommand(),
            new LintCiCommand(),
            new LintStagedCommand(),
            new LintStagedTestCommand(),
            new PhpStanStagedCommand(),
            new ReleaseCommand(),
        ]);
    }

    public function run(
        ?InputInterface $input = null,
        ?OutputInterface $output = null,
    ): int {
        $root = monorepo_root();

        chdir($root);

        return parent::run($input, $output);
    }
}
