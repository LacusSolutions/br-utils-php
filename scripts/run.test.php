<?php

declare(strict_types=1);

/**
 * Smoke tests for the monorepo run CLI.
 */

function run_test_run_path(): string
{
    return dirname(__DIR__) . '/run';
}

/**
 * @param list<string> $arguments
 *
 * @return array{exitCode: int, stdout: string, stderr: string}
 */
function invoke_run_executable(array $arguments): array
{
    $command = escapeshellarg(run_test_run_path());

    foreach ($arguments as $argument) {
        $command .= ' ' . escapeshellarg($argument);
    }

    $descriptorSpec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($command, $descriptorSpec, $pipes, dirname(__DIR__));

    if (!is_resource($process)) {
        fwrite(STDERR, "Failed to start run executable.\n");

        exit(1);
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]) ?: '';
    $stderr = stream_get_contents($pipes[2]) ?: '';
    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    return ['exitCode' => $exitCode, 'stdout' => $stdout, 'stderr' => $stderr];
}

/**
 * @param array{exitCode: int, stdout: string, stderr: string} $result
 */
function assert_exit_code(array $result, int $expected, string $label): void
{
    if ($result['exitCode'] !== $expected) {
        fwrite(STDERR, "FAIL: {$label} (expected exit {$expected}, got {$result['exitCode']})\n");
        fwrite(STDERR, $result['stderr'] . $result['stdout']);

        exit(1);
    }

    echo "PASS: {$label}\n";
}

/**
 * @param array{exitCode: int, stdout: string, stderr: string} $result
 */
function assert_contains(array $result, string $needle, string $label): void
{
    $haystack = $result['stderr'] . $result['stdout'];

    if (!str_contains($haystack, $needle)) {
        fwrite(STDERR, "FAIL: {$label} (output missing \"{$needle}\")\n{$haystack}\n");

        exit(1);
    }

    echo "PASS: {$label}\n";
}

echo "Testing run CLI...\n\n";

assert_exit_code(invoke_run_executable([]), 0, 'missing command shows application help');
assert_contains(invoke_run_executable([]), 'lint:ci', 'missing command lists lint:ci');

assert_exit_code(invoke_run_executable(['--help']), 0, '--help exits successfully');
assert_contains(invoke_run_executable(['--help']), 'List commands', '--help shows list command help');

assert_exit_code(invoke_run_executable(['list']), 0, 'list exits successfully');
assert_contains(invoke_run_executable(['list']), 'lint:ci', 'list shows lint:ci');

assert_exit_code(invoke_run_executable(['unknown-command']), 1, 'unknown command exits with error');
assert_contains(invoke_run_executable(['unknown-command']), 'is not defined', 'unknown command shows error');

$depsHelp = invoke_run_executable(['deps', '--help']);
assert_exit_code($depsHelp, 0, 'deps --help exits successfully');
assert_contains($depsHelp, '--reverse', 'deps --help shows --reverse option');
assert_contains($depsHelp, '--dev', 'deps --help shows --dev option');

echo "\nAll run CLI tests passed.\n";

exit(0);
