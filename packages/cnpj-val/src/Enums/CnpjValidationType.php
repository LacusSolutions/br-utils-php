<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Enums;

use Lacus\Utils\SequenceType;

/**
 * Character type for CNPJ validation.
 *
 * - `Alphanumeric` (`"alphanumeric"`): alphanumeric CNPJ format ([A-Z0-9]).
 * - `Numeric` (`"numeric"`): numeric-only (legacy) CNPJ format ([0-9]).
 */
enum CnpjValidationType: string
{
    case Alphanumeric = SequenceType::Alphanumeric->value;
    case Numeric = SequenceType::Numeric->value;

    /**
     * Get the values of the CNPJ validation type.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        $cases = self::cases();
        $values = array_map(fn (CnpjValidationType $case) => $case->value, $cases);

        return $values;
    }

    /**
     * Convert the CNPJ validation type to a SequenceType.
     *
     * @return SequenceType
     */
    public function toSequenceType(): SequenceType
    {
        return match ($this) {
            self::Alphanumeric => SequenceType::Alphanumeric,
            self::Numeric      => SequenceType::Numeric,
        };
    }
}
