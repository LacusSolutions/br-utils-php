<?php

declare(strict_types=1);

namespace Scripts\Commands;

use function monorepo_root;
use function package_names;
use function packages_directory;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Path;

final class ReleaseCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('release')
            ->setDescription('Prepare release notes from a package CHANGELOG (developer/CI only)')
            ->addArgument('package', InputArgument::REQUIRED, 'Package folder name')
            ->addOption('version', 'v', InputOption::VALUE_REQUIRED, 'Release version (X.Y.Z)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $package = $input->getArgument('package');

        if (!is_string($package)) {
            return Command::FAILURE;
        }

        $requestedVersion = $input->getOption('version');

        if ($requestedVersion !== null && !is_string($requestedVersion)) {
            return Command::FAILURE;
        }

        if (!in_array($package, package_names(), true)) {
            $output->writeln("<error>Invalid package: {$package}</error>");
            $output->writeln('Available packages: ' . implode(', ', package_names()));

            return Command::FAILURE;
        }

        if ($requestedVersion !== null && !preg_match('/^\d+\.\d+\.\d+$/', $requestedVersion)) {
            $output->writeln("<error>Invalid version format: {$requestedVersion}</error>");
            $output->writeln('Expected format: X.Y.Z (e.g. 1.2.3)');

            return Command::FAILURE;
        }

        $changelogPath = Path::join(packages_directory(), $package, 'CHANGELOG.md');

        if (!is_readable($changelogPath)) {
            $output->writeln("<error>Changelog not found: {$changelogPath}</error>");

            return Command::FAILURE;
        }

        $changelogContents = file_get_contents($changelogPath);

        if ($changelogContents === false) {
            $output->writeln("<error>Failed to read changelog: {$changelogPath}</error>");

            return Command::FAILURE;
        }

        $versionBodies = $this->extractChangelogBodies($changelogContents);

        if ($versionBodies === []) {
            $output->writeln("<error>No version sections found in changelog: {$changelogPath}</error>");

            return Command::FAILURE;
        }

        if ($requestedVersion !== null) {
            if (!array_key_exists($requestedVersion, $versionBodies)) {
                $output->writeln("<error>Version not found in changelog: {$requestedVersion}</error>");
                $output->writeln('Available versions: ' . implode(', ', array_keys($versionBodies)));

                return Command::FAILURE;
            }

            $version = $requestedVersion;
        } else {
            $version = array_key_first($versionBodies);
        }

        if ($version === null) {
            return Command::FAILURE;
        }

        $releaseDirectory = Path::join(monorepo_root(), '.release');

        if (!is_dir($releaseDirectory) && !mkdir($releaseDirectory, 0777, true) && !is_dir($releaseDirectory)) {
            $output->writeln("<error>Failed to create release directory: {$releaseDirectory}</error>");

            return Command::FAILURE;
        }

        $outputPath = Path::join($releaseDirectory, "{$package}@{$version}.md");
        $outputContents = $versionBodies[$version] . "\n";

        if (file_put_contents($outputPath, $outputContents) === false) {
            $output->writeln("<error>Failed to write release notes: {$outputPath}</error>");

            return Command::FAILURE;
        }

        $output->writeln($outputPath);

        return Command::SUCCESS;
    }

    /**
     * @return array<string, string>
     */
    private function extractChangelogBodies(string $markdown): array
    {
        $pattern = '/^## (\d+\.\d+\.\d+)\s*\r?\n(.*?)(?=^## \d+\.\d+\.\d+\s*\r?\n|\z)/ms';

        if (!preg_match_all($pattern, $markdown, $matches, PREG_SET_ORDER)) {
            return [];
        }

        $bodies = [];

        foreach ($matches as $match) {
            $bodies[$match[1]] = rtrim($match[2]);
        }

        return $bodies;
    }
}
