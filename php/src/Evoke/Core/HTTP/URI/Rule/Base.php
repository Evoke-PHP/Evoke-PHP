<?php
namespace Evoke\Core\HTTP\URI\Rule;

/** HTTP URI Rule class for mapping the URI to a response.
 */
abstract class Base implements \Evoke\Core\Iface\HTTP\URI\Rule
{
	/** @property $authoritative
	 *  \bool Whether the rule can definitely give the final route for all URIs
	 *  that it matches.
	 */
	protected $authoritative;

	/** Construct the URI Rule.
	 *  @param authoritative \bool Whether the rule can definitely give the
	 *  final route for all URIs that it matches.
	 */
	public function __construct($authoritative)
	{
		if (!is_bool($authoritative))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires authoritative as a bool');
		}

		$this->authoritative = $authoritative;
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Return the parameters for the URI.
	 *  @param uri \string The URI.
	 *  @return \array Empty Array. (By default no parameters are captured)
	 */	
	public function getParams($uri)
	{
		return array();
	}

	/** Check whether the rule is authoritative.
	 *  @return \bool Whether the rule can definitely give the final route for
	 *  all URIs that it matches.
	 */
	public function isAuthoritative()
	{
		return $this->authoritative;
	}
}
// EOF