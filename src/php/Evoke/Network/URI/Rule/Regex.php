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
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   HTTP\URI\Rule
 */
class Regex extends Rule
{
	/**
	 * Controller regex match and replace.
	 * @var string[]
	 */
	protected $controller;

	/**
	 * Regex to determine whether this rule matches.
	 * @var string
	 */
	protected $match;

	/**
	 * Parameters each with a key and value regex for match and replace.
	 * Example:
	 * <pre><code>
	 * [
	 *     ['Key'   => ['Match' => 'regex', 'Replace' => 'replacement'],
	 *      'Value' => ['Match' => 'regex', 'Replace' => 'replacement']] 
	 * ]
	 * </code></pre>
	 */
	protected $params;

	/**
	 * Construct the Regex Rule.
	 *
	 * @param string[] Controller regex match and replace.
	 * @param string   Regex to determine whether the rule matches.
	 * @param Array[]  Parameters each with a key and value regex for match and
	 *                 replacement.
	 * @param bool     Whether the rule is authoritative.
	 */
	public function __construct(Array $controller,
	                            /* string */ $match,
	                            Array        $params,
	                            /* bool   */ $authoritative)
	{
		parent::__construct($authoritative);
		$invalidArgs = false;

		foreach ($params as $param)
		{
			if (!isset($param['Key']['Match'],
			           $param['Key']['Replace'],
			           $param['Value']['Match'],
			           $param['Value']['Replace']))
			{
				$invalidArgs = true;
				break;
			}			                         
		}
		
		if ($invalidArgs ||
		    !isset($controller['Match'], $controller['Replace']))
		{
			throw new InvalidArgumentException('Bad Arguments');
		}
		
		$this->controller = $controller;
		$this->match      = $match;
		$this->params     = $params;
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
		return preg_replace($this->controller['Match'],
		                    $this->controller['Replace'],
		                    $uri);
	}

	/**
	 * Get any parameters.
	 *
	 * @param string The URI to get the parameters from.
	 * @return mixed[] Parameters from the URI.
	 */
	public function getParams($uri)
	{
		$paramsFound = array();
		
		foreach ($this->params as $param)
		{
			if (preg_match($param['Key']['Match'], $uri) &&
			    preg_match($param['Value']['Match'], $uri))
			{
				$paramsFound[preg_replace($param['Key']['Match'],
				                          $param['Key']['Replace'],
				                          $uri)]
					= preg_replace($param['Value']['Match'],
					               $param['Value']['Replace'],
					               $uri);
			}
		}

		return $paramsFound;
	}

	/**
	 * Check the uri to see if it matches.
	 *
	 * @param string The URI.
	 * @return bool Whether the uri is matched.
	 */
	public function isMatch($uri)
	{
		return preg_match($this->controller['Match'], $uri) > 0;
	}	
}
// EOF