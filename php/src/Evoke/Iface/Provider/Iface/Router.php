<?php
namespace Evoke\Iface\Provider\Iface;

use Evoke\Iface;

interface Router
{
	/** Add a rule to the router.
	 *  @param rule @object HTTP URI Rule object.
	 */
	public function addRule(Iface\Provider\Iface\Rule $rule);

	/** Route the Interface to a concrete class.
	 *  @param interfaceName @string The interface name to route.
	 *  @return @mixed The classname (or false if no concrete class could be found).
	 */
	public function route($interfaceName);
}
// EOF