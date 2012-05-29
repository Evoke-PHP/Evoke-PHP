<?php
namespace Evoke\HTTP;

interface ResponseIface
{
	/** Redirect to a different page.
	 *  @param location \string Where to redirect to.
	 */
	public function redirect($location);
	
	/** Set the headers to show that the document should be cached. This must
	 *  come before any output is set in the document (otherwise the headers will
	 *  have already been sent).
	 *
	 *  @param days    \int The number of days to cache the document for.
	 *  @param hours   \int The number of hours to cache the document for.
	 *  @param minutes \int The number of minutes to cache the document for.
	 *  @param seconds \int The number of seconds to cache the document for.
	 */
	public function setCache($days=0, $hours=0, $minutes=0, $seconds=0);

	/** Set the content type header for the response.
	 *  @param contentType \string The content type.
	 */
	public function setContentType($contentType);
	
	/** Set the reponse code (200 OK, 404 Not Found, etc.)
	 *  @param code \int The HTTP status code.
	 */
	public function setResponseCode($code);
}
// EOF