<?php

declare(strict_types=1);

namespace Lacus\Utils;

/**
 * Character type for random sequence generation (mirrors JS SequenceType).
 *
 * - alphanumeric: digits and uppercase letters (0-9A-Z)
 * - numeric: digits only (0-9)
 * - alphabetic: uppercase letters only (A-Z)
 */
enum SequenceType: string
{
    case Alphabetic = 'alphabetic';
    case Alphanumeric = 'alphanumeric';
    case Numeric = 'numeric';
}
