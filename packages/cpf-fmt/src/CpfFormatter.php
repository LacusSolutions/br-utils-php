<?php

declare(strict_types=1);

namespace Lacus\CpfFmt;

use Closure;

class CpfFormatter
{
    private CpfFormatterOptions $options;

    public function __construct(
        ?bool $escape = null,
        ?bool $hidden = null,
        ?string $hiddenKey = null,
        ?int $hiddenStart = null,
        ?int $hiddenEnd = null,
        ?string $dotKey = null,
        ?string $dashKey = null,
        ?Closure $onFail = null,
    ) {
        $this->options = new CpfFormatterOptions(
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

    public function format(
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
        $actualOptions = $this->getOptions()->merge(
            $escape,
            $hidden,
            $hiddenKey,
            $hiddenStart,
            $hiddenEnd,
            $dotKey,
            $dashKey,
            $onFail,
        );

        $cpfNumbersString = preg_replace('/[^0-9]/', '', $cpfString) ?? '';
        $cpfNumbersArray = str_split($cpfNumbersString);

        if (count($cpfNumbersArray) !== CPF_LENGTH) {
            $error = new \Error('Parameter "' . $cpfString . '" does not contain ' . CPF_LENGTH . ' digits.');

            return $actualOptions->getOnFail()($cpfString, $error);
        }

        if ($actualOptions->isHidden()) {
            $hiddenStart = $actualOptions->getHiddenStart();
            $hiddenEnd = $actualOptions->getHiddenEnd();
            $hiddenKey = $actualOptions->getHiddenKey();

            for ($i = $hiddenStart; $i <= $hiddenEnd; $i++) {
                $cpfNumbersArray[$i] = $hiddenKey;
            }
        }

        $dashKey = $actualOptions->getDashKey();
        $dotKey = $actualOptions->getDotKey();

        array_splice($cpfNumbersArray, 9, 0, $dashKey);
        array_splice($cpfNumbersArray, 6, 0, $dotKey);
        array_splice($cpfNumbersArray, 3, 0, $dotKey);

        $prettyCpf = implode('', $cpfNumbersArray);

        if ($actualOptions->isEscaped()) {
            return htmlspecialchars($prettyCpf, ENT_QUOTES, 'UTF-8');
        }

        return $prettyCpf;
    }

    public function getOptions(): CpfFormatterOptions
    {
        return $this->options;
    }
}
