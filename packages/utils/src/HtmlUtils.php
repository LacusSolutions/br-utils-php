<?php

declare(strict_types=1);

namespace Lacus\Utils;

/**
 * Provides general-purpose HTML utility functions.
 *
 * This class offers static methods for a variety of HTML-related operations,
 * such as escaping content for safe output, and may be expanded with additional
 * helpers for everyday HTML handling or manipulation.
 */
final class HtmlUtils
{
    /**
     * Escapes HTML special characters for safe output and XSS mitigation (mirrors JS escapeHTML).
     *
     * Replaces &, <, >, ", ' with &amp;, &lt;, &gt;, &quot;, &#039;.
     *
     * @example
     *   HtmlUtils::escape('Tom & Jerry'); // 'Tom &amp; Jerry'
     *   HtmlUtils::escape('<script>alert("XSS")</script>');
     *   // '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;'
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars(
            $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8',
        );
    }

    /**
     * Decodes HTML entities (reverse of escape).
     *
     * Replaces &amp;, &lt;, &gt;, &quot;, &#039; (and common variants) with &, <, >, ", '.
     * Use only on content that was previously escaped or is otherwise trusted—decoded output
     * is not safe for direct insertion into HTML.
     *
     * @example
     *   HtmlUtils::unescape('Tom &amp; Jerry'); // 'Tom & Jerry'
     *   HtmlUtils::unescape('&lt;br&gt;');      // '<br>'
     */
    public static function unescape(string $value): string
    {
        return htmlspecialchars_decode(
            $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
        );
    }
}
