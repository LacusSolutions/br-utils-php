<?php

declare(strict_types=1);

use Symfony\Component\Console\Helper\TreeHelper;
use Symfony\Component\Console\Helper\TreeNode;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Filesystem\Path;

require __DIR__ . '/helpers.php';

function print_deps_tree_usage(): void
{
    fwrite(STDERR, "Usage: php scripts/deps-tree.php [options] [<package>]\n");
    fwrite(STDERR, "\n");
    fwrite(STDERR, "Arguments:\n");
    fwrite(STDERR, "  <package>          Focus on one package (folder name or lacus/name)\n");
    fwrite(STDERR, "\n");
    fwrite(STDERR, "Options:\n");
    fwrite(STDERR, "  --dev              Include require-dev internal dependencies\n");
    fwrite(STDERR, "  -r, --reverse      Show packages that depend on the target package(s)\n");
    fwrite(STDERR, "  -h, --help         Show this help message\n");
}

/**
 * @param list<string> $arguments
 *
 * @return array{includeDev: bool, reverse: bool, package: ?string}
 */
function parse_deps_tree_arguments(array $arguments): array
{
    $includeDev = false;
    $reverse = false;
    $package = null;

    for ($index = 0, $count = count($arguments); $index < $count; ++$index) {
        $argument = $arguments[$index];

        if ($argument === '-h' || $argument === '--help') {
            print_deps_tree_usage();

            exit(0);
        }

        if ($argument === '--dev') {
            $includeDev = true;

            continue;
        }

        if ($argument === '-r' || $argument === '--reverse') {
            $reverse = true;

            continue;
        }

        if (str_starts_with($argument, '-')) {
            fwrite(STDERR, "Unknown option: {$argument}\n");
            print_deps_tree_usage();

            exit(1);
        }

        if ($package !== null) {
            fwrite(STDERR, "Unexpected argument: {$argument}\n");
            print_deps_tree_usage();

            exit(1);
        }

        $package = $argument;
    }

    return ['includeDev' => $includeDev, 'reverse' => $reverse, 'package' => $package];
}

/**
 * @return array{
 *     packages: array<string, array{name: string, require: array<string, string>, require-dev: array<string, string>}>,
 *     composerToFolder: array<string, string>
 * }
 */
function load_package_manifests(): array
{
    $packages = [];
    $composerToFolder = [];

    foreach (package_names() as $folder) {
        $manifestPath = Path::join(packages_directory(), $folder, 'composer.json');

        if (!is_file($manifestPath)) {
            fwrite(STDERR, "Missing composer.json for package: {$folder}\n");

            exit(1);
        }

        $decoded = json_decode((string) file_get_contents($manifestPath), true);

        if (!is_array($decoded) || !isset($decoded['name']) || !is_string($decoded['name'])) {
            fwrite(STDERR, "Invalid composer.json for package: {$folder}\n");

            exit(1);
        }

        $packages[$folder] = [
            'name' => $decoded['name'],
            'require' => normalize_composer_constraint_map($decoded['require'] ?? []),
            'require-dev' => normalize_composer_constraint_map($decoded['require-dev'] ?? []),
        ];
        $composerToFolder[$decoded['name']] = $folder;
    }

    return ['packages' => $packages, 'composerToFolder' => $composerToFolder];
}

/**
 * @return array<string, string>
 */
function normalize_composer_constraint_map(mixed $section): array
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
function normalize_package_filter(string $package, array $packages): string
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

    fwrite(STDERR, "Unknown package: {$package}\n");
    fwrite(STDERR, 'Available packages: ' . implode(', ', array_keys($packages)) . "\n");

    exit(1);
}

/**
 * @param array<string, array{name: string, require: array<string, string>, require-dev: array<string, string>}> $packages
 * @param array<string, string> $composerToFolder
 *
 * @return array<string, list<array{package: string, constraint: string, section: string}>>
 */
function build_internal_dependencies(
    array $packages,
    array $composerToFolder,
    bool $includeDev,
): array {
    $dependencies = [];

    foreach ($packages as $folder => $manifest) {
        $dependencies[$folder] = [];

        foreach (dependency_sections($manifest, $includeDev) as $section => $constraints) {
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
function dependency_sections(array $manifest, bool $includeDev): array
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
function build_reverse_dependencies(array $dependencies): array
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
function find_root_packages(array $dependencies): array
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

function format_dependency_label(
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
function build_dependency_subtree(
    string $folder,
    ?string $constraint,
    ?string $section,
    array $dependencies,
    bool $includeDev,
    array &$visited,
): TreeNode {
    $label = $constraint !== null
        ? format_dependency_label($folder, $constraint, $section ?? 'require', $includeDev)
        : $folder;

    if (isset($visited[$folder])) {
        return new TreeNode($label . ' (already shown above)');
    }

    $visited[$folder] = true;
    $children = [];

    foreach ($dependencies[$folder] ?? [] as $dependency) {
        $children[] = build_dependency_subtree(
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

function render_tree(TreeNode $node): void
{
    $output = new StreamOutput(STDOUT);
    TreeHelper::createTree($output, $node)->render();
}

function render_section_title(string $title): void
{
    fwrite(STDOUT, $title . "\n");
    fwrite(STDOUT, str_repeat('=', strlen($title)) . "\n\n");
}

/**
 * @param list<string> $roots
 * @param array<string, list<array{package: string, constraint: string, section: string}>> $dependencies
 */
function render_dependency_trees(
    string $title,
    array $roots,
    array $dependencies,
    bool $includeDev,
): void {
    render_section_title($title);

    foreach ($roots as $root) {
        $visited = [];
        render_tree(build_dependency_subtree($root, null, null, $dependencies, $includeDev, $visited));
        fwrite(STDOUT, "\n");
    }
}

/**
 * @param array<string, list<array{package: string, constraint: string, section: string}>> $reverse
 */
function render_reverse_dependencies(string $title, array $reverse, bool $includeDev): void
{
    render_section_title($title);

    foreach (array_keys($reverse) as $folder) {
        $dependents = $reverse[$folder];

        if ($dependents === []) {
            fwrite(STDOUT, "{$folder}\n");
            fwrite(STDOUT, "  (no internal dependents)\n\n");

            continue;
        }

        $node = new TreeNode($folder);

        foreach ($dependents as $dependent) {
            $node->addChild(format_dependency_label(
                $dependent['package'],
                $dependent['constraint'],
                $dependent['section'],
                $includeDev,
            ));
        }

        render_tree($node);
        fwrite(STDOUT, "\n");
    }
}

$options = parse_deps_tree_arguments(script_arguments());
$manifests = load_package_manifests();
$packages = $manifests['packages'];
$dependencies = build_internal_dependencies(
    $packages,
    $manifests['composerToFolder'],
    $options['includeDev'],
);
$reverse = build_reverse_dependencies($dependencies);

if ($options['package'] !== null) {
    $focusedPackage = normalize_package_filter($options['package'], $packages);

    if ($options['reverse']) {
        render_reverse_dependencies(
            "Packages depending on {$focusedPackage}",
            [$focusedPackage => $reverse[$focusedPackage]],
            $options['includeDev'],
        );
    } else {
        render_dependency_trees(
            "{$focusedPackage} dependencies",
            [$focusedPackage],
            $dependencies,
            $options['includeDev'],
        );
    }

    exit(0);
}

if ($options['reverse']) {
    render_reverse_dependencies('Packages depending on', $reverse, $options['includeDev']);
} else {
    render_dependency_trees(
        'Packages dependencies',
        find_root_packages($dependencies),
        $dependencies,
        $options['includeDev'],
    );
}
