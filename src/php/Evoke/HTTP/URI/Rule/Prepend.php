<?php
/**
 * HTTP URI Prepend Rule
 *
 * @pakcage HTTP\URI\Rule
 */
namespace Evoke\HTTP\URI\Rule;

/**
 * HTTP URI Prepend Rule
 *
 * A rule to prepend a string to the controller.
 * No parameters are matched by this class.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   HTTP\URI\Rule
 */
class Prepend extends Rule
{
	/**
	 * String to prepend to the controller.
	 * @var string
	 */
	protected $str;

	/**
	 * Construct the prepend rule.
	 *
	 * @param string The string to prepend.
	 * @param bool   Whether the rule can definitely give the final route for
	 *               all URIs that it matches.
	 */
	public function __construct(/* String */ $str,
	                            /* Bool   */ $authoritative = false)
	{
		parent::__construct($authoritative);

		$this->str = $str;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the controller.
	 *
	 * @param string The URI to get the controller from.
	 * @return string The uri with the string prepended.
	 */
	public function getController($uri)
	{
		return $this->str . $uri;
	}
	
	/**
	 * The prepend rule always matches.
	 *
	 * @param string The URI to determine the match from.
	 * @return bool TRUE.
	 */
	public function isMatch($uri)
	{
		return true;
	}
}
// EOF