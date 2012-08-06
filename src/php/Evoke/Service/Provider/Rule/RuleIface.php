<?php
namespace Evoke\Service\Provider\Rule;

/**
 * RuleIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
interface RuleIface
{
	/**
	 * Get the classname.
	 *
	 * @param string The interface name to convert to an instantiable class.
	 *
	 * @return string The conversion of the interface into a concrete class.
	 */
	public function getClassname($interfaceName);
	
	/**
	 * Check the interface name  to see whether the rule matches.
	 *
	 * @param string The interface name to check.
	 *
	 * @return bool Whether the interface name is matched by the rule.
	 */
	public function isMatch($interfaceName);
}
// EOF