<?php
/**
 * HTTP URI String Replace Rule
 *
 * @package Network\URI\Rule
 */
namespace Evoke\Network\URI\Rule;

/**
 * HTTP URI String Replace Rule
 *
 * A rule to change strings from the URI so that a controller can be formed.
 * No parameters are matched by this class.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Network\URI\Rule
 */
class StrReplace extends Rule
{
	/**
	 * The string to match on.
	 * @var string
	 */
	protected $match;

	/** 
	 * The string to use as a replacement.
	 * @var string
	 */
	protected $replacement;

	/**
	 * Construct the string replacements rule.
	 *
	 * @param string The string to match on.
	 * @param string The string to use as a replacement.
	 * @param bool   Whether the rule can definitely give the final route for
	 *               all URIs that it matches.
	 */
	public function __construct(/* String */ $match,
	                            /* String */ $replacement,
	                            /* Bool   */ $authoritative = false)
	{
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
	 * @return string The uri with the string replacements made.
	 */
	public function getController($uri)
	{
		return str_replace($this->match, $this->replacement, $uri);
	}
	
	/**
	 * Check the uri to see if it matches. Only 1 sub-rule needs to match.
	 *
	 * @param string The URI.
	 * @return bool Whether the uri is matched.
	 */
	public function isMatch($uri)
	{
		return strpos($uri, $this->match) !== false;
	}
}
// EOF