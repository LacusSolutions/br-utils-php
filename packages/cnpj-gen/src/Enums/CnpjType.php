<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Enums;

use Lacus\Utils\SequenceType;

enum CnpjType: string
{
    case Alphanumeric = SequenceType::Alphanumeric->value;
    case Alphabetic = SequenceType::Alphabetic->value;
    case Numeric = SequenceType::Numeric->value;

    /**
     * Get the values of the CNPJ type.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        $cases = self::cases();
        $values = array_map(fn (CnpjType $case) => $case->value, $cases);

        return $values;
    }

    /**
     * Convert the CNPJ type to a SequenceType.
     *
     * @return SequenceType
     */
    public function toSequenceType(): SequenceType
    {
        return match ($this) {
            self::Alphanumeric => SequenceType::Alphanumeric,
            self::Alphabetic   => SequenceType::Alphabetic,
            self::Numeric      => SequenceType::Numeric,
        };
    }
}
