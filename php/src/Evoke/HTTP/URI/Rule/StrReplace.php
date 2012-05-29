<?php
namespace Evoke\HTTP\URI\Rule;

use InvalidArgumentException;

/** A rule to change strings from a request so that a classname can be formed.
 *  No parameters are matched by this class.
 */
class StrReplace extends Rule
{
	/** @property $match
	 *  @string The string to match on.
	 */
	protected $match;

	/** @property $replacement
	 *  @string The string to use as a replacement.
	 */
	protected $replacement;

	/** Construct the string replacements rule.
	 *  @param match         @string The string to match on.
	 *  @param replacment    @string The string to use as a replacement.
	 *  @param authoritative @bool   Whether the rule can definitely give the
	 *                               final route for all URIs that it matches.
	 */
	public function __construct(/* String */ $match,
	                            /* String */ $replacement,
	                            /* Bool   */ $authoritative = false)
	{
		if (!is_string($match))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires match as string');
		}

		if (!is_string($replacement))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires replacement as string');
		}
      
		parent::__construct($authoritative);

		$this->match       = $match;
		$this->replacement = $replacement;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/** Get the classname.
	 *  @param uri @string The URI to get the classname from.
	 *  @return @string The uri with the string replacements made.
	 */
	public function getClassname($uri)
	{
		return str_replace($this->match, $this->replacement, $uri);
	}
	
	/** Check the uri to see if it matches. Only 1 sub-rule needs to match.
	 *  @return @bool Whether the uri is matched.
	 */
	public function isMatch($uri)
	{
		return strpos($uri, $this->match) !== false;
	}
}
// EOF