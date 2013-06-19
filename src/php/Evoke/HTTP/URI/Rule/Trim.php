<?php
/**
 * HTTP URI Trim Rule
 *
 * @package HTTP\URI\Rule
 */
namespace Evoke\HTTP\URI\Rule;

use InvalidArgumentException;

/**
 * HTTP URI Trim Rule
 *
 * A rule to trim characters from the URI.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   HTTP\URI\Rule
 */
class Trim extends Rule
{
	/**
	 * Characters to trim from the the URI@
	 * @var string
	 */
	protected $characters;

	/**
	 * Construct the Trim Rule.
	 *
	 * @param string The characters to trim from the URI.
	 * @param bool   Whether the rule can definitely give the final route for
	 *               all URIs that it matches.
	 */
	public function __construct(/* String */ $characters,
	                            /* Bool   */ $authoritative = false)
	{
		if (!is_string($characters))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires characters as string');
		}
      
		parent::__construct($authoritative);

		$this->characters = $characters;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the controller.
	 *
	 * @param string The URI to get the controller from.
	 * @return string The uri trimmed appropriately.
	 */
	public function getController($uri)
	{
		return trim($uri, $this->characters);
	}

	/**
	 * Check the uri to see if it matches.
	 *
	 * @return bool Whether the uri is matched.
	 */
	public function isMatch($uri)
	{
		return (
			preg_match(
				'/^[' . preg_quote($this->characters, '/') . ']+/', $uri) ||
			preg_match(
				'/[' . preg_quote($this->characters, '/') . ']+$/', $uri));
	}	
}
// EOF