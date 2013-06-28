<?php
/**
 * Response Interface
 *
 * @package Network\HTTP
 */
namespace Evoke\Network\HTTP;

/**
 * Response Interface
 *
 * The HTTP Response interface designed to meet RFC2616-sec6 and to a lesser
 * extent RFC1945-sec6.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
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
	 * @param string The text to set the response body to.
	 */
	public function setBody($text);		
	
	/**
	 * Set the headers to show that the document should be cached.

	 * This must be called before any output is sent (otherwise the headers will
	 * have already been sent).
	 *
	 * @param int The number of days to cache the document for.
	 * @param int The number of hours to cache the document for.
	 * @param int The number of minutes to cache the document for.
	 * @param int The number of seconds to cache the document for.
	 */
	public function setCache($days=0, $hours=0, $minutes=0, $seconds=0);
	
	/**
	 * Set the header field with the given value.
	 *
	 * @param string The header field to set.
	 * @param string The value to set the header field to.
	 */
	public function setHeader($field, $value);
	
	/**
	 * Set the HTTP status code and reason (200 OK, 404 Not Found, etc.)
	 *
	 * @param int The HTTP status code.
	 * @param null | string The HTTP status reason.
	 */
	public function setStatus($code, $reason = NULL);
}
// EOF