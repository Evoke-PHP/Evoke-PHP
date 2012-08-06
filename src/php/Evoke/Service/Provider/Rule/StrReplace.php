<?php
namespace Evoke\Service\Provider\Rule;

/** 
 */
/**
 * StrReplace Rule
 *
 * A rule to replace a string to convert to a concrete class.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
class StrReplace implements RuleIface
{
	/**
	 * Construct the string replacements rule.
	 *
	 * @param string The string to match.
	 * @param string The string to replace the match with.
	 */
	public function __construct(/* String */ $match,
	                            /* String */ $replacement)
	{
		$this->match       = $match;
		$this->replacement = $replacement;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the classname.
	 *
	 * @param string The interface name to convert to an instantiable class.
	 *
	 * @return string The conversion of the interface into a concrete class.
	 */
	public function getClassname($interfaceName)
	{
		return str_replace($this->match, $this->replacement, $interfaceName);
	}
	
	/**
	 * Check the interface name  to see whether the rule matches.
	 *
	 * @param string The interface name to check.
	 *
	 * @return bool Whether the interface name is matched by the rule.
	 */
	public function isMatch($interfaceName)
	{
		return strpos($interfaceName, $this->match) !== false;
	}
}
// EOF