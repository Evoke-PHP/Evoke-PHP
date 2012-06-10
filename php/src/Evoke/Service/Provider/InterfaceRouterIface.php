<?php
namespace Evoke\Service\Provider;
/**
 * Interface Router Interface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
interface InterfaceRouterIface
{
	/**
	 * Add a rule to the router.
	 *
	 * @param Rule\RuleIface rule HTTP URI Rule object.
	 */
	public function addRule(Rule\RuleIface $rule);

	/**
	 * Route the Interface to a concrete class.
	 *
	 * @param string interfaceName The interface name to route.
	 * @return string|bool The classname (or false if no concrete class could be
	 *                     found).
	 */
	public function route($interfaceName);
}
// EOF