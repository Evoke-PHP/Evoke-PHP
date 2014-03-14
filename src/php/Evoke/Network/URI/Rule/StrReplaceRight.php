<?php
/**
 * StrReplaceRight
 *
 * @package Network\URI\Rule
 */
namespace Evoke\Network\URI\Rule;

/**
 * StrReplaceRight
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2013 Paul Young
 * @license   MIT
 * @package   Network\URI\Rule
 */
class StrReplaceRight extends Rule
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
	 * Construct the right string replacement rule.
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
	 * @return string The uri with the match at the right replaced.
	 */
	public function getController()
	{
		return substr($this->uri, 0, -strlen($this->match)) .
			$this->replacement;
	}
	
	/**
	 * Check the uri to see if it matches.
	 *
	 * @return bool Whether the uri is matched.
	 */
	public function isMatch()
	{
		return substr($this->uri, -strlen($this->match)) === $this->match;
	}
}
// EOF