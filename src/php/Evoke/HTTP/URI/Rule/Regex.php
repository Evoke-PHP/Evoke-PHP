<?php
namespace Evoke\HTTP\URI\Rule;

use InvalidArgumentException;

/**
 * Regex
 *
 * A regex rule to map the uri classname and parameters.  There is a single
 * match for the URI, with all replacements being made from this match.  If
 * there are more complex requirements such as optional parameters then the
 * RegexTwoLevel rule should be used.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
 */
class Regex extends Rule
{
	/**
	 * Regex match for the URI.
	 * @var string
	 */
	protected $match;

	/**
	 * Parameter key and value replacement regex for the URI.
	 * @var Array[]
	 */
	protected $params;

	/**
	 * Regex replacement for the classname.
	 * @var string
	 */
	protected $replacement;

	/**
	 * Construct the SimpleReplace Rule.
	 *
	 * @param string  The Regex to match the URI with.
	 * @param string  The classname regex replacement string.
	 * @param Array[] Regexes replacements for the parameters.
	 * @param bool    Is this always the final route?
	 */
	public function __construct(/* String */ $match,
	                            /* String */ $replacement,
	                            Array        $params        = array(),
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
		
		foreach ($params as $index => $paramSpec)
		{
			// Set the keys to remove need for isset checks.
			$paramSpec += array('Key' => NULL, 'Value' => NULL);

			if (!is_string($paramSpec['Key']))
			{
				throw new InvalidArgumentException(
					__METHOD__ . ' param spec at index: ' . $index .
					' requires Key as string.');
			}

			if (!is_string($paramSpec['Value']))
			{
				throw new InvalidArgumentException(
					__METHOD__ . ' param spec at index: ' . $index .
					' requires Value as string.');
			}
		}
		
		parent::__construct($authoritative);

		$this->match       = $match;
		$this->params      = $params;
		$this->replacement = $replacement;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the classname.
	 *
	 * @param string The URI to get the classname from.
	 * @return string The uri with the match replaced.
	 */
	public function getClassname($uri)
	{
		return preg_replace($this->match, $this->replacement, $uri);
	}

	/**
	 * Get any parameters.
	 *
	 * @param string The URI to get the parameters from.
	 * @return mixed[] Parameters from the URI.
	 */
	public function getParams($uri)
	{
		$params = array();

		foreach ($this->params as $paramSpec)
		{
			$params[preg_replace($this->match, $paramSpec['Key'], $uri)] =
				preg_replace($this->match, $paramSpec['Value'], $uri);
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
		return (preg_match($this->match, $uri) > 0);
	}
}
// EOF