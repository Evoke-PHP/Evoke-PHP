<?php
/**
 * URI Regex Rule with one match used for all calculations.
 *
 * @package Network\URI\Rule
 */
namespace Evoke\Network\URI\Rule;

use InvalidArgumentException;

/**
 * URI Regex Rule with one match used for all calculations.
 *
 * A regex rule to map the uri controller and parameters.  There is a single
 * match for the URI, with all replacements being made from this match.  If
 * there are more complex requirements such as optional parameters then the
 * RegexTwoLevel rule should be used.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Network\URI\Rule
 */
class RegexSharedMatch extends Rule
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
	 * @return string The uri with the match replaced.
	 */
	public function getController()
	{
		return preg_replace($this->match, $this->replacement, $this->uri);
	}

	/**
	 * Get any parameters.
	 *
	 * @return mixed[] Parameters from the URI.
	 */
	public function getParams()
	{
		$params = array();

		foreach ($this->params as $paramSpec)
		{
			$params[preg_replace($this->match, $paramSpec['Key'], $this->uri)] =
				preg_replace($this->match, $paramSpec['Value'], $this->uri);
		}

		return $params;		
	}
	
	/**
	 * Check the uri to see if it matches.
	 *
	 * @return bool Whether the uri is matched.
	 */
	public function isMatch()
	{
		$result = preg_match($this->match, $this->uri);
		
		return $result !== false && $result > 0;
	}
}
// EOF