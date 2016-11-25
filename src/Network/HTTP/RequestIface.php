<?php
declare(strict_types = 1);
/**
 * HTTP Request Interface
 *
 * @package Network\HTTP
 */
namespace Evoke\Network\HTTP;

/**
 * HTTP Request Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Network\HTTP
 */
interface RequestIface extends \Evoke\Network\RequestIface
{
    /**
     * Get the method.  (One of the HTTP verbs HEAD, GET, OPTIONS, TRACE, POST, PUT or DELETE).
     */
    public function getMethod();

    /**
     * Parse the Accept header field from the request according to RFC-2616.
     *
     * This field specifies the preferred media types for responses.
     *
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
     * @return array[]
     * Accepted media types with their quality factor, ordered by preference according to compareAccept. Each element of
     * the array has keys defining the Params, Q_Factor, Subtype and Type.
     */
    public function parseAccept();

    /**
     * Parse the Accept-Language header from the request according to RFC-2616.
     *
     * This header field specifies the preferred languages for responses.
     *
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.10
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
     * @return array[] The accepted languages from the request in order of
     *                 quality from highest to lowest.  Each element of the
     *                 array has keys defining the Language and Q_Factor.
     */
    public function parseAcceptLanguage();
}
// EOF
