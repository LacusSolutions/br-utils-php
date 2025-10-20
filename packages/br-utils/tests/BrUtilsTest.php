<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests;

use Closure;
use Error;
use Lacus\BrUtils\BrUtils;
use Lacus\CnpjUtils\Tests\CnpjUtilsTestCases;
use Lacus\CpfUtils\Tests\CpfUtilsTestCases;
use PHPUnit\Framework\TestCase;

const CNPJ_FILE_REGEX = "#[\\/]vendor[\\/]lacus[\\/]cnpj\-[a-z]+[\\/]#";
const CPF_FILE_REGEX = "#[\\/]vendor[\\/]lacus[\\/]cpf\-[a-z]+[\\/]#";

class BrUtilsTest extends TestCase
{
    use CnpjUtilsTestCases, CpfUtilsTestCases {
        CnpjUtilsTestCases::format as formatCnpj;
        CnpjUtilsTestCases::generate as generateCnpj;
        CnpjUtilsTestCases::isValid as isValidCnpj;
        CnpjUtilsTestCases::setUpBeforeClass insteadof CpfUtilsTestCases;
        CnpjUtilsTestCases::loadTestEnv insteadof CpfUtilsTestCases;
        CnpjUtilsTestCases::testInvalidInputFallsBackToOnFailCallback insteadof CpfUtilsTestCases;
        CnpjUtilsTestCases::testOptionWithRangeStartMinusOneThrowsException insteadof CpfUtilsTestCases;
        CnpjUtilsTestCases::testOptionWithRangeEndMinusOneThrowsException insteadof CpfUtilsTestCases;
        CnpjUtilsTestCases::testOptionWithOnFailAsNotFunctionThrowsException insteadof CpfUtilsTestCases;
        CnpjUtilsTestCases::testValue123IsNotValid insteadof CpfUtilsTestCases;
        CnpjUtilsTestCases::testValueAbcIsNotValid insteadof CpfUtilsTestCases;
        CnpjUtilsTestCases::testValueAbc123IsNotValid insteadof CpfUtilsTestCases;
        CnpjUtilsTestCases::testValueTrueIsNotValid insteadof CpfUtilsTestCases;
        CnpjUtilsTestCases::testValueFalseIsNotValid insteadof CpfUtilsTestCases;
        CnpjUtilsTestCases::testValueNullIsNotValid insteadof CpfUtilsTestCases;
        CnpjUtilsTestCases::testValueInfinityIsNotValid insteadof CpfUtilsTestCases;
        CpfUtilsTestCases::format as formatCpf;
        CpfUtilsTestCases::generate as generateCpf;
        CpfUtilsTestCases::isValid as isValidCpf;
        CpfUtilsTestCases::testInvalidInputFallsBackToOnFailCallback as testInvalidInputFallsBackToOnFailCallbackCpf;
        CpfUtilsTestCases::testOptionWithRangeStartMinusOneThrowsException as testOptionWithRangeStartMinusOneThrowsExceptionCpf;
        CpfUtilsTestCases::testOptionWithRangeEndMinusOneThrowsException as testOptionWithRangeEndMinusOneThrowsExceptionCpf;
        CpfUtilsTestCases::testOptionWithOnFailAsNotFunctionThrowsException as testOptionWithOnFailAsNotFunctionThrowsExceptionCpf;
        CpfUtilsTestCases::testValue123IsNotValid as testValue123IsNotValidCpf;
        CpfUtilsTestCases::testValueAbcIsNotValid as testValueAbcIsNotValidCpf;
        CpfUtilsTestCases::testValueAbc123IsNotValid as testValueAbc123IsNotValidCpf;
        CpfUtilsTestCases::testValueTrueIsNotValid as testValueTrueIsNotValidCpf;
        CpfUtilsTestCases::testValueFalseIsNotValid as testValueFalseIsNotValidCpf;
        CpfUtilsTestCases::testValueNullIsNotValid as testValueNullIsNotValidCpf;
        CpfUtilsTestCases::testValueInfinityIsNotValid as testValueInfinityIsNotValidCpf;
    }

    private BrUtils $utils;

    protected function setUp(): void
    {
        $this->utils = new BrUtils();
    }

    protected function format(...$args): string
    {
        $stackTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $callerFile = $stackTrace[0]['file'];

        if (preg_match(CNPJ_FILE_REGEX, $callerFile)) {
            return $this->formatCnpj(...$args);
        }

        if (preg_match(CPF_FILE_REGEX, $callerFile)) {
            return $this->formatCpf(...$args);
        }

        throw new Error("Caller not found.");
    }

    protected function generate(...$args): string
    {
        $stackTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $callerFile = $stackTrace[0]['file'];

        if (preg_match(CNPJ_FILE_REGEX, $callerFile)) {
            return $this->generateCnpj(...$args);
        }

        if (preg_match(CPF_FILE_REGEX, $callerFile)) {
            return $this->generateCpf(...$args);
        }

        throw new Error("Caller not found.");
    }

    protected function isValid(...$args): bool
    {
        $stackTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $callerFile = $stackTrace[0]['file'];

        if (preg_match(CNPJ_FILE_REGEX, $callerFile)) {
            return $this->isValidCnpj(...$args);
        }

        if (preg_match(CPF_FILE_REGEX, $callerFile)) {
            return $this->isValidCpf(...$args);
        }

        throw new Error("Caller not found.");
    }

    protected function formatCnpj(
        string $cnpjString,
        ?bool $escape = null,
        ?bool $hidden = null,
        ?string $hiddenKey = null,
        ?int $hiddenStart = null,
        ?int $hiddenEnd = null,
        ?string $dotKey = null,
        ?string $slashKey = null,
        ?string $dashKey = null,
        ?Closure $onFail = null,
    ): string {
        return $this->utils->cnpj->format(
            $cnpjString,
            $escape,
            $hidden,
            $hiddenKey,
            $hiddenStart,
            $hiddenEnd,
            $dotKey,
            $slashKey,
            $dashKey,
            $onFail,
        );
    }

    protected function generateCnpj(
        ?bool $format = null,
        ?string $prefix = null,
    ): string {
        return $this->utils->cnpj->generate(
            $format,
            $prefix,
        );
    }

    protected function isValidCnpj(string $cnpjString): bool
    {
        return $this->utils->cnpj->isValid($cnpjString);
    }

    protected function formatCpf(
        string $cpfString,
        ?bool $escape = null,
        ?bool $hidden = null,
        ?string $hiddenKey = null,
        ?int $hiddenStart = null,
        ?int $hiddenEnd = null,
        ?string $dotKey = null,
        ?string $dashKey = null,
        ?Closure $onFail = null,
    ): string {
        return $this->utils->cpf->format(
            $cpfString,
            $escape,
            $hidden,
            $hiddenKey,
            $hiddenStart,
            $hiddenEnd,
            $dotKey,
            $dashKey,
            $onFail,
        );
    }

    protected function generateCpf(
        ?bool $format = null,
        ?string $prefix = null,
    ): string {
        return $this->utils->cpf->generate(
            $format,
            $prefix,
        );
    }

    protected function isValidCpf(string $cpfString): bool
    {
        return $this->utils->cpf->isValid($cpfString);
    }
}
