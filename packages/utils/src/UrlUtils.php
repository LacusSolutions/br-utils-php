<?php

declare(strict_types=1);

namespace Lacus\Utils;

/**
 * Utilities for URI element encoding and decoding (percent-encoding, RFC 3986).
 *
 * Use for path segments, query names/values, and fragment components so that
 * reserved and special characters are safely encoded in URIs.
 */
final class UrlUtils
{
    /**
     * Encodes a string for use as a URI component (path segment, query name/value, or fragment).
     *
     * @example
     *   UrlUtils::encodeUriComponent('a/b');   // 'a%2Fb'
     *   UrlUtils::encodeUriComponent('a b');   // 'a%20b'
     *   UrlUtils::encodeUriComponent('a=b');   // 'a%3Db'
     *   UrlUtils::encodeUriComponent('a#b');   // 'a%23b'
     */
    public static function encodeUriComponent(string $value): string
    {
        return rawurlencode($value);
    }

    /**
     * Decodes a percent-encoded URI component.
     *
     * @example
     *   UrlUtils::decodeUriComponent('a%20b');   // 'a b'
     *   UrlUtils::decodeUriComponent('a%2Fb'); // 'a/b'
     */
    public static function decodeUriComponent(string $value): string
    {
        return rawurldecode($value);
    }
}
