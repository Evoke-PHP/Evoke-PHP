<?php
namespace Evoke\HTTP\URI\Rule;

use InvalidArgumentException;

/**
 * LeftTrim
 *
 * A rule to trim characters from the left side of the URI.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
 */
class LeftTrim extends Rule
{
	/**
	 * Characters to trim from the left side of the URI.
	 * @var string
	 */
	protected $characters;

	/**
	 * Construct the LeftTrim URI Rule.
	 *
	 * @param string The characters to left trim from the URI.
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
		return ltrim($uri, $this->characters);
	}
	
	/**
	 * Check the uri to see if it matches.
	 *
	 * @return bool Whether the uri is matched.
	 */
	public function isMatch($uri)
	{
		return isset($uri[0]) && (strpos($this->characters, $uri[0]) !== false);
	}   
}
// EOF