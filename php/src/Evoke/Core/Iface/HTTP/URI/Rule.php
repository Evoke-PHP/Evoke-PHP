<?php
namespace Evoke\Core\Iface\HTTP\URI;

interface Rule
{
	/** Return the parameters for the URI.
	 *  @param uri \string The URI.
	 *  @return \array The parameters for the response.
	 */
	public function getParams($uri);

	/** Get the response.
	 *  @param uri \string The URI to get the response from.
	 *  \return \string The uri mapped towards the response with the rule.
	 */	
	public function getResponse($uri);

	/** Check whether the rule is authoritative.
	 *  @return \bool Whether the rule can definitely give the final route for
	 *  all URIs that it matches.
	 */
	public function isAuthoritative();
	
	/** Check the uri to see if it matches.
	 *  @return \bool Whether the uri is matched.
	 */
	public function isMatch($uri);
}
// EOF