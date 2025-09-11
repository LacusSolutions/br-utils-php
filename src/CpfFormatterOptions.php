<?php

declare(strict_types=1);

namespace Lacus\CpfFmt;

const CPF_LENGTH = 11;

class CpfFormatterOptions
{
    private array $options;

    public function __construct(
        ?bool $escape = null,
        ?bool $hidden = null,
        ?string $hiddenKey = null,
        ?int $hiddenStart = null,
        ?int $hiddenEnd = null,
        ?string $dotKey = null,
        ?string $dashKey = null,
        ?callable $onFail = null,
    ) {
        $this->setEscape($escape ?? false);
        $this->setHide($hidden ?? false);
        $this->setHiddenKey($hiddenKey ?? '*');
        $this->setHiddenRange($hiddenStart ?? 3, $hiddenEnd ?? 10);
        $this->setDotKey($dotKey ?? '.');
        $this->setDashKey($dashKey ?? '-');
        $this->setOnFail($onFail ?? function (string $value): string {
            return $value;
        });
    }

    public function merge(
        ?bool $escape = null,
        ?bool $hidden = null,
        ?string $hiddenKey = null,
        ?int $hiddenStart = null,
        ?int $hiddenEnd = null,
        ?string $dotKey = null,
        ?string $dashKey = null,
        ?callable $onFail = null,
    ): self
    {
        return new self(
            $escape ?? $this->isEscaped(),
            $hidden ?? $this->isHidden(),
            $hiddenKey ?? $this->getHiddenKey(),
            $hiddenStart ?? $this->getHiddenStart(),
            $hiddenEnd ?? $this->getHiddenEnd(),
            $dotKey ?? $this->getDotKey(),
            $dashKey ?? $this->getDashKey(),
            $onFail ?? $this->getOnFail(),
        );
    }

    public function setEscape(bool $value): void
    {
        $this->options['escape'] = $value;
    }

    public function isEscaped(): bool
    {
        return $this->options['escape'];
    }

    public function setHide(bool $value): void
    {
        $this->options['hidden'] = $value;
    }

    public function isHidden(): bool
    {
        return $this->options['hidden'];
    }

    public function setHiddenKey(string $value): void
    {
        $this->options['hiddenKey'] = $value;
    }

    public function getHiddenKey(): string
    {
        return $this->options['hiddenKey'];
    }

    public function setHiddenRange(int $start, int $end): void
    {
        $min = 0;
        $max = CPF_LENGTH - 1;

        if (!is_int($start) || $start < $min || $start > $max) {
            throw new \TypeError(
                'Option "hiddenStart" must be an integer between '
                . $min . ' and '
                . $max . '.'
            );
        }

        if (!is_int($end) || $end < $min || $end > $max) {
            throw new \TypeError(
                'Option "hiddenRange.end" must be an integer between '
                . $min . ' and '
                . $max . '.'
            );
        }

        if ($start > $end) {
            $aux = $start;
            $start = $end;
            $end = $aux;
        }

        $this->options['hiddenStart'] = $start;
        $this->options['hiddenEnd'] = $end;
    }

    public function getHiddenStart(): int
    {
        return $this->options['hiddenStart'];
    }

    public function getHiddenEnd(): int
    {
        return $this->options['hiddenEnd'];
    }

    public function setDotKey(string $value): void
    {
        $this->options['dotKey'] = $value;
    }

    public function getDotKey(): string
    {
        return $this->options['dotKey'];
    }

    public function setDashKey(string $value): void
    {
        $this->options['dashKey'] = $value;
    }

    public function getDashKey(): string
    {
        return $this->options['dashKey'];
    }

    public function setOnFail(callable $callback): void
    {
        if (!is_callable($callback)) {
            throw new \TypeError(
                'The option "onFail" must be a callable function.'
            );
        }

        $this->options['onFail'] = $callback;
    }

    public function getOnFail(): callable
    {
        return $this->options['onFail'];
    }
}
