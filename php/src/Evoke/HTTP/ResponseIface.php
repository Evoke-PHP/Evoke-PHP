<?php
namespace Evoke\HTTP;

/**
 * ResponseIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
 */
interface ResponseIface
{
	/**
	 * Redirect to a different page.
	 *
	 * @param string Where to redirect to.
	 */
	public function redirect($location);
	
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
	 * Set the content type header for the response.
	 *
	 * @param string The content type.
	 */
	public function setContentType($contentType);
	
	/**
	 * Set the reponse code (200 OK, 404 Not Found, etc.)
	 *
	 * @param int The HTTP status code.
	 */
	public function setResponseCode($code);
}
// EOF