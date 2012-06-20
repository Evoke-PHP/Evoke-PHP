<?php
namespace Evoke\HTTP;

use LogicException;

/**
 * Response
 *
 * The HTTP Response.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
 */
class Response implements ResponseIface
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Redirect to a different page.
	 *
	 * @param string Where to redirect to.
	 */
	public function redirect($location)
	{
		if (headers_sent())
		{
			throw new LogicException(
				__METHOD__ . ' headers have already been sent.');
		}

		header('Location: ' . $location);
	}
	
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
	public function setCache($days=0, $hours=0, $minutes=0, $seconds=0)
	{
		if (headers_sent())
		{
			throw new LogicException(
				__METHOD__ . ' headers have already been sent.');
		}
      
		// Calculate the offset in seconds.
		$offset = ((((($days * 24) + $hours) * 60) + $minutes) * 60) + $seconds;

		header('Pragma: public');
		header('Cache-Control: must-revalidate maxage=' . $offset);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT');
	}

	/**
	 * Set the content type header for the response.
	 *
	 * @param string The content type.
	 */
	public function setContentType($contentType)
	{
		if (headers_sent())
		{
			throw new LogicException(
				__METHOD__ . ' headers have already been sent.');
		}

		header('Content-Type: ' . $contentType);
	}
	
	/**
	 * Set the reponse code (200 OK, 404 Not Found, etc.)
	 *
	 * @param int The HTTP status code.
	 */
	public function setResponseCode($code)
	{
		if (headers_sent())
		{
			throw new LogicException(
				__METHOD__ . ' headers have already been sent.');
		}

		/** http_response_code should appear in PHP 5.4, but to keep Evoke
		 *  compatible with PHP 5.3 we only use it if it is callable.
		 */
		if (is_callable('http_response_code'))
		{
			http_response_code($code);
		}
		else
		{
			/// @todo Make a switch for the status text.
			header('HTTP/1.0 ' . $code . ' todo status text');	
		}
	}
}
// EOF
