<?php
namespace Evoke\Iface\Core\Provider\Iface;

use Evoke\Iface\Core as ICore;

interface Router
{
	/** Add a rule to the router.
	 *  @param rule @object HTTP URI Rule object.
	 */
	public function addRule(ICore\Provider\Iface\Rule $rule);

	/** Route the Interface to a concrete class.
	 *  @param interfaceName @string The interface name to route.
	 *  @return @mixed The classname (or false if no concrete class could be found).
	 */
	public function route($interfaceName);
}
// EOF