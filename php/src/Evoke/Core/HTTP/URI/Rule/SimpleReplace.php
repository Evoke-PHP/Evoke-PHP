<?php
namespace Evoke\Core\HTTP\URI\Rule;

/** A rule to map a simple request to a response.
 *  This is for a request that does not have any parameters and only needs to
 *  be mapped to an appropriate Response class.
 */
class SimpleReplace extends Base
{
	/** @property $match
	 *  Regex match \string
	 */
	protected $match;

	/** @property $replacement
	 *  Regex replacement \string
	 */
	protected $replacement;

	/** Construct the SimpleReplace Rule.
	 *  @param match \string The Regex to match the URI with.
	 *  @param replacement \string The Regex replacement string.
	 *  @param authoritative \bool Whether the rule can definitely give the
	 *  final route for all URIs that it matches.
	 */
	public function __construct($match, $replacement, $authoritative=false)
	{
		if (!is_string($match))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires match as string');
		}

		if (!is_string($replacement))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires replacement as string');
		}
      
		parent::__construct($authoritative);

		$this->match       = $match;
		$this->replacement = $replacement;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/** Get the response.
	 *  @param uri \string The URI to get the response from.
	 *  \return \string The uri with the match replaced.
	 */
	public function getResponse($uri)
	{
		return preg_replace($this->match, $this->replacement, $uri);
	}

	/** Check the uri to see if it matches.
	 *  \return \bool Whether the uri is matched.
	 */
	public function isMatch($uri)
	{
		return (preg_match($this->match, $uri) > 0);
	}
}
// EOF