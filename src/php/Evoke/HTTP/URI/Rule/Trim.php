<?php
namespace Evoke\HTTP\URI\Rule;

use InvalidArgumentException;

/**
 * Trim
 *
 * A rule to trim characters from the URI.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
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
	 * Get the classname.
	 *
	 * @param string The URI to get the classname from.
	 * @return string The uri trimmed appropriately.
	 */
	public function getClassname($uri)
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
			preg_match('/^[' . preg_quote($this->characters, '/') . ']+/', $uri) ||
			preg_match('/[' . preg_quote($this->characters, '/') . ']+$/', $uri));
	}	
}
// EOF