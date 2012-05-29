<?php
namespace Evoke\Service\Provider\Iface;

interface RouterIface
{
	/** Add a rule to the router.
	 *  @param rule @object HTTP URI Rule object.
	 */
	public function addRule(RuleIface $rule);

	/** Route the Interface to a concrete class.
	 *  @param interfaceName @string The interface name to route.
	 *  @return @mixed The classname (or false if no concrete class could be found).
	 */
	public function route($interfaceName);
}
// EOF