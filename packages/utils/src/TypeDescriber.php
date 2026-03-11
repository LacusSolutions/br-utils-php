<?php

declare(strict_types=1);

namespace Lacus\Utils;

/**
 * Describes the type of a value for error messages (mirrors JS describeType).
 *
 * @example
 *   TypeDescriber::describe(null);       // 'null'
 *   TypeDescriber::describe('hello');    // 'string'
 *   TypeDescriber::describe(true);       // 'boolean'
 *   TypeDescriber::describe(42);         // 'integer number'
 *   TypeDescriber::describe(3.14);       // 'float number'
 *   TypeDescriber::describe(NAN);        // 'NaN'
 *   TypeDescriber::describe(INF);        // 'Infinity'
 *   TypeDescriber::describe([]);         // 'Array (empty)'
 *   TypeDescriber::describe([1, 2, 3]);  // 'number[]'
 *   TypeDescriber::describe([1, 'a', 2]); // '(number | string)[]'
 *   TypeDescriber::describe((object)[]); // 'object'
 */
final class TypeDescriber
{
    /**
     * Describes the type of a value for error messages.
     *
     * @param mixed $value    Any value
     * @param bool  $inArray  When true, int/float are described as "number" to match JS typeof
     */
    public static function describe(mixed $value, bool $inArray = false): string
    {
        if (is_array($value)) {
            if ($value === []) {
                return 'Array (empty)';
            }

            $types = array_unique(array_map(
                static fn (mixed $item): string => self::describe($item, true),
                $value
            ));
            $types = array_values($types);
            sort($types);

            if (count($types) === 1) {
                return $types[0] . '[]';
            }

            return '(' . implode(' | ', $types) . ')[]';
        }

        if ($value === null) {
            return $inArray ? 'object' : 'null';
        }

        if (is_int($value)) {
            return $inArray ? 'number' : 'integer number';
        }

        if (is_float($value)) {
            if (is_nan($value)) {
                return 'NaN';
            }

            if (!is_finite($value)) {
                return 'Infinity';
            }

            return $inArray ? 'number' : 'float number';
        }

        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_string($value)) {
            return 'string';
        }

        if (is_object($value)) {
            return 'object';
        }

        if (is_resource($value)) {
            return 'resource';
        }

        return gettype($value);
    }
}
