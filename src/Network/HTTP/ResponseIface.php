<?php
declare(strict_types = 1);
/**
 * Response Interface
 *
 * @package Network\HTTP
 */
namespace Evoke\Network\HTTP;

/**
 * Response Interface
 *
 * The HTTP Response interface designed to meet RFC2616-sec6 and to a lesser extent RFC1945-sec6.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Network\HTTP
 */
interface ResponseIface
{
    /**
     * Send the Response as per RFC2616-sec6, (send the headers and body).
     */
    public function send();

    /**
     * Set the body of the response.
     *
     * @param string $text The text to set the response body to.
     */
    public function setBody(string $text);

    /**
     * Set the headers to show that the document should be cached. This must be called before any output is sent
     * (otherwise the headers will have already been sent).
     *
     * @param int $days    The number of days to cache the document for.
     * @param int $hours   The number of hours to cache the document for.
     * @param int $minutes The number of minutes to cache the document for.
     * @param int $seconds The number of seconds to cache the document for.
     */
    public function setCache(int $days = 0, int $hours = 0, int $minutes = 0, int $seconds = 0);

    /**
     * Set the header field with the given value.
     *
     * @param string $field The header field to set.
     * @param string $value The value to set the header field to.
     */
    public function setHeader(string $field, string $value);

    /**
     * Set the HTTP status code and reason (200 OK, 404 Not Found, etc.)
     *
     * @param int         $code   The HTTP status code.
     * @param null|string $reason The HTTP status reason.
     */
    public function setStatus(int $code, $reason = null);
}
// EOF
