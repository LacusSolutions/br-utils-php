<?php

declare(strict_types=1);

namespace Scripts\Commands;

use function package_names;
use function packages_directory;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TreeHelper;
use Symfony\Component\Console\Helper\TreeNode;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Path;

final class DepsCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('deps')
            ->setDescription('Show the internal lacus/* dependency graph')
            ->addOption('dev', null, InputOption::VALUE_NONE, 'Include require-dev internal dependencies')
            ->addOption('reverse', 'r', InputOption::VALUE_NONE, 'Show packages that depend on the target package(s)')
            ->addArgument('package', InputArgument::OPTIONAL, 'Focus on one package (folder name or lacus/name)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $includeDev = (bool) $input->getOption('dev');
        $reverse = (bool) $input->getOption('reverse');
        $package = $input->getArgument('package');

        if ($package !== null && !is_string($package)) {
            return Command::FAILURE;
        }

        $manifests = $this->loadPackageManifests($output);
        $packages = $manifests['packages'];

        if ($packages === []) {
            return Command::FAILURE;
        }
        $dependencies = $this->buildInternalDependencies(
            $packages,
            $manifests['composerToFolder'],
            $includeDev,
        );
        $reverseDependencies = $this->buildReverseDependencies($dependencies);

        if ($package !== null) {
            $focusedPackage = $this->normalizePackageFilter($package, $packages, $output);

            if ($focusedPackage === null) {
                return Command::FAILURE;
            }

            if ($reverse) {
                $this->renderReverseDependencies(
                    $output,
                    "Packages depending on {$focusedPackage}",
                    [$focusedPackage => $reverseDependencies[$focusedPackage]],
                    $includeDev,
                );
            } else {
                $this->renderDependencyTrees(
                    $output,
                    "{$focusedPackage} dependencies",
                    [$focusedPackage],
                    $dependencies,
                    $includeDev,
                );
            }

            return Command::SUCCESS;
        }

        if ($reverse) {
            $this->renderReverseDependencies($output, 'Packages depending on', $reverseDependencies, $includeDev);
        } else {
            $roots = $this->findRootPackages($dependencies);

            if ($roots === []) {
                $roots = array_keys($dependencies);
                sort($roots);
            }

            $this->renderDependencyTrees(
                $output,
                'Packages dependencies',
                $roots,
                $dependencies,
                $includeDev,
            );
        }

        return Command::SUCCESS;
    }

    /**
     * @return array{
     *     packages: array<string, array{name: string, require: array<string, string>, require-dev: array<string, string>}>,
     *     composerToFolder: array<string, string>
     * }
     */
    private function loadPackageManifests(OutputInterface $output): array
    {
        $packages = [];
        $composerToFolder = [];

        foreach (package_names() as $folder) {
            $manifestPath = Path::join(packages_directory(), $folder, 'composer.json');

            if (!is_file($manifestPath)) {
                $output->writeln("<error>Missing composer.json for package: {$folder}</error>");

                return ['packages' => [], 'composerToFolder' => []];
            }

            $decoded = json_decode((string) file_get_contents($manifestPath), true);

            if (!is_array($decoded) || !isset($decoded['name']) || !is_string($decoded['name'])) {
                $output->writeln("<error>Invalid composer.json for package: {$folder}</error>");

                return ['packages' => [], 'composerToFolder' => []];
            }

            $packages[$folder] = [
                'name' => $decoded['name'],
                'require' => $this->normalizeComposerConstraintMap($decoded['require'] ?? []),
                'require-dev' => $this->normalizeComposerConstraintMap($decoded['require-dev'] ?? []),
            ];
            $composerToFolder[$decoded['name']] = $folder;
        }

        return ['packages' => $packages, 'composerToFolder' => $composerToFolder];
    }

    /**
     * @return array<string, string>
     */
    private function normalizeComposerConstraintMap(mixed $section): array
    {
        if (!is_array($section)) {
            return [];
        }

        $constraints = [];

        foreach ($section as $packageName => $constraint) {
            if (!is_string($packageName) || !is_string($constraint)) {
                continue;
            }

            $constraints[$packageName] = $constraint;
        }

        ksort($constraints);

        return $constraints;
    }

    /**
     * @param array<string, array{name: string, require: array<string, string>, require-dev: array<string, string>}> $packages
     */
    private function normalizePackageFilter(string $package, array $packages, OutputInterface $output): ?string
    {
        if (isset($packages[$package])) {
            return $package;
        }

        if (str_starts_with($package, 'lacus/')) {
            $folder = substr($package, strlen('lacus/'));

            if (isset($packages[$folder])) {
                return $folder;
            }
        }

        $output->writeln("<error>Unknown package: {$package}</error>");
        $output->writeln('Available packages: ' . implode(', ', array_keys($packages)));

        return null;
    }

    /**
     * @param array<string, array{name: string, require: array<string, string>, require-dev: array<string, string>}> $packages
     * @param array<string, string> $composerToFolder
     *
     * @return array<string, list<array{package: string, constraint: string, section: string}>>
     */
    private function buildInternalDependencies(
        array $packages,
        array $composerToFolder,
        bool $includeDev,
    ): array {
        $dependencies = [];

        foreach ($packages as $folder => $manifest) {
            $dependencies[$folder] = [];

            foreach ($this->dependencySections($manifest, $includeDev) as $section => $constraints) {
                foreach ($constraints as $composerName => $constraint) {
                    if (!isset($composerToFolder[$composerName])) {
                        continue;
                    }

                    $dependencies[$folder][] = [
                        'package' => $composerToFolder[$composerName],
                        'constraint' => $constraint,
                        'section' => $section,
                    ];
                }
            }

            usort(
                $dependencies[$folder],
                static fn (array $left, array $right): int => $left['package'] <=> $right['package'],
            );
        }

        return $dependencies;
    }

    /**
     * @param array{name: string, require: array<string, string>, require-dev: array<string, string>} $manifest
     *
     * @return array<string, array<string, string>>
     */
    private function dependencySections(array $manifest, bool $includeDev): array
    {
        $sections = ['require' => $manifest['require']];

        if ($includeDev) {
            $sections['require-dev'] = $manifest['require-dev'];
        }

        return $sections;
    }

    /**
     * @param array<string, list<array{package: string, constraint: string, section: string}>> $dependencies
     *
     * @return array<string, list<array{package: string, constraint: string, section: string}>>
     */
    private function buildReverseDependencies(array $dependencies): array
    {
        $reverse = [];

        foreach (array_keys($dependencies) as $folder) {
            $reverse[$folder] = [];
        }

        foreach ($dependencies as $dependent => $deps) {
            foreach ($deps as $dependency) {
                $reverse[$dependency['package']][] = [
                    'package' => $dependent,
                    'constraint' => $dependency['constraint'],
                    'section' => $dependency['section'],
                ];
            }
        }

        foreach ($reverse as $folder => $dependents) {
            usort(
                $dependents,
                static fn (array $left, array $right): int => $left['package'] <=> $right['package'],
            );
            $reverse[$folder] = $dependents;
        }

        return $reverse;
    }

    /**
     * @param array<string, list<array{package: string, constraint: string, section: string}>> $dependencies
     *
     * @return list<string>
     */
    private function findRootPackages(array $dependencies): array
    {
        $dependedUpon = [];

        foreach ($dependencies as $deps) {
            foreach ($deps as $dependency) {
                $dependedUpon[$dependency['package']] = true;
            }
        }

        $roots = [];

        foreach (array_keys($dependencies) as $folder) {
            if (!isset($dependedUpon[$folder])) {
                $roots[] = $folder;
            }
        }

        sort($roots);

        return $roots;
    }

    private function formatDependencyLabel(
        string $folder,
        string $constraint,
        string $section,
        bool $includeDev,
    ): string {
        $label = $folder . ' (' . $constraint . ')';

        if ($includeDev && $section === 'require-dev') {
            $label .= ' [dev]';
        }

        return $label;
    }

    /**
     * @param array<string, list<array{package: string, constraint: string, section: string}>> $dependencies
     * @param array<string, true> $visited
     */
    private function buildDependencySubtree(
        string $folder,
        ?string $constraint,
        ?string $section,
        array $dependencies,
        bool $includeDev,
        array &$visited,
    ): TreeNode {
        $label = $constraint !== null
            ? $this->formatDependencyLabel($folder, $constraint, $section ?? 'require', $includeDev)
            : $folder;

        if (isset($visited[$folder])) {
            return new TreeNode($label . ' (already shown above)');
        }

        $visited[$folder] = true;
        $children = [];

        foreach ($dependencies[$folder] ?? [] as $dependency) {
            $children[] = $this->buildDependencySubtree(
                $dependency['package'],
                $dependency['constraint'],
                $dependency['section'],
                $dependencies,
                $includeDev,
                $visited,
            );
        }

        unset($visited[$folder]);

        return new TreeNode($label, $children);
    }

    /**
     * @param list<string> $roots
     * @param array<string, list<array{package: string, constraint: string, section: string}>> $dependencies
     */
    private function renderDependencyTrees(
        OutputInterface $output,
        string $title,
        array $roots,
        array $dependencies,
        bool $includeDev,
    ): void {
        $this->renderSectionTitle($output, $title);

        foreach ($roots as $root) {
            $visited = [];
            TreeHelper::createTree(
                $output,
                $this->buildDependencySubtree($root, null, null, $dependencies, $includeDev, $visited),
            )->render();
            $output->writeln('');
        }
    }

    /**
     * @param array<string, list<array{package: string, constraint: string, section: string}>> $reverse
     */
    private function renderReverseDependencies(
        OutputInterface $output,
        string $title,
        array $reverse,
        bool $includeDev,
    ): void {
        $this->renderSectionTitle($output, $title);

        foreach (array_keys($reverse) as $folder) {
            $dependents = $reverse[$folder];

            if ($dependents === []) {
                $output->writeln($folder);
                $output->writeln('  (no internal dependents)');
                $output->writeln('');

                continue;
            }

            $node = new TreeNode($folder);

            foreach ($dependents as $dependent) {
                $node->addChild($this->formatDependencyLabel(
                    $dependent['package'],
                    $dependent['constraint'],
                    $dependent['section'],
                    $includeDev,
                ));
            }

            TreeHelper::createTree($output, $node)->render();
            $output->writeln('');
        }
    }

    private function renderSectionTitle(OutputInterface $output, string $title): void
    {
        $output->writeln($title);
        $output->writeln(str_repeat('=', strlen($title)));
        $output->writeln('');
    }
}
