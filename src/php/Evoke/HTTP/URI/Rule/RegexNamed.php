<?php
/**
 * Regex Named
 *
 * @package HTTP
 */
namespace Evoke\HTTP\URI\Rule;

use InvalidArgumentException;

/**
 * Regex Named
 *
 * A regex rule to map the uri controller and parameters.  There is a single
 * match for the URI, with all replacements being made from this match.  If
 * there are more complex requirements such as optional parameters then the
 * RegexTwoLevel rule should be used.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
 */
class RegexNamed extends Rule
{
	/**
	 * Regex match for the URI.  The named subpatterns are used for the
	 * parameters.
	 * @var string
	 */
	protected $match;

	/**
	 * Regex replacement for the controller.  Any named subpatterns must be
	 * referred to by number in the replacement.
	 * @var string
	 */
	protected $replacement;

	/**
	 * Construct the Regex Named rule.
	 *
	 * @param string  The Regex to match the URI with named subpatterns.
	 * @param string  The controller regex replacement string.
	 * @param bool    Is this always the final route?
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

	/**
	 * Get the controller.
	 *
	 * @param string The URI to get the controller from.
	 * @return string The uri with the match replaced.
	 */
	public function getController($uri)
	{
		return preg_replace($this->match, $this->replacement, $uri);
	}

	/**
	 * Get any parameters.
	 *
	 * @param string The URI to get the parameters from.
	 *
	 * @return mixed[] Named parameters from the URI subpattern matches.
	 */
	public function getParams($uri)
	{
		preg_match($this->match, $uri, $params);

		// Return only the named parameters rather than the numbered ones. 
		foreach (array_keys($params) as $key)
		{
			if (!is_string($key))
			{
				unset($params[$key]);
			}
		}

		return $params;
	}
	
	/**
	 * Check the uri to see if it matches.
	 *
	 * @param string The URI.
	 * @return bool Whether the uri is matched.
	 */
	public function isMatch($uri)
	{
		$result = preg_match($this->match, $uri);

		return $result !== false && $result > 0;
	}
}
// EOF