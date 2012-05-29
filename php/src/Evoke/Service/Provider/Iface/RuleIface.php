<?php
namespace Evoke\Service\Provider\Iface;

interface RuleIface
{
	/** Get the classname.
	 *  @param  interfaceName @string The interface name to convert to an
	 *                                instantiable class.
	 *  @return @string The conversion of the interface into a concrete class.
	 */
	public function getClassname($interfaceName);
	
	/** Check the interface name  to see whether the rule matches.
	 *  @param interfaceName @string The interface name to check.
	 *  @return @bool Whether the interface name is matched by the rule.
	 */
	public function isMatch($interfaceName);
}
// EOF