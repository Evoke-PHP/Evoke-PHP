<?php
/**
 * HTTP URI Regex Rule
 *
 * @package HTTP\URI\Rule
 */
namespace Evoke\HTTP\URI\Rule;

use InvalidArgumentException;

/**
 * HTTP URI Regex Rule
 *
 * A regex rule to map the uri controller and parameters.  There is a single
 * match for the URI, with all replacements being made from this match.  If
 * there are more complex requirements such as optional parameters then the
 * RegexTwoLevel rule should be used.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   HTTP\URI\Rule
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
	 * Regex replacement for the controller.
	 * @var string
	 */
	protected $replacement;

	/**
	 * Construct the Regex Rule.
	 *
	 * @param string  The Regex to match the URI with.
	 * @param string  The controller regex replacement string.
	 * @param Array[] Regexes replacements for the parameters.
	 * @param bool    Is this always the final route?
	 */
	public function __construct(/* String */ $match,
	                            /* String */ $replacement,
	                            Array        $params        = array(),
	                            /* Bool   */ $authoritative = false)
	{
		parent::__construct($authoritative);

		foreach ($params as $index => $paramSpec)
		{
			// Set the keys to remove need for isset checks.
			$paramSpec += array('Key' => NULL, 'Value' => NULL);

			if (!isset($paramSpec['Key'], $paramSpec['Value']))
			{
				throw new InvalidArgumentException(
					'param spec needs Key and Value at index: ' . $index);
			}
		}

		$this->match       = $match;
		$this->params      = $params;
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
		$result = preg_match($this->match, $uri);
		
		return $result !== false && $result > 0;
	}
}
// EOF