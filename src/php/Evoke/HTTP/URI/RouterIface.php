<?php
/**
 * HTTP URI Router Interface
 *
 * @package HTTP\URI
 */
namespace Evoke\HTTP\URI;
/**
 * HTTP URI Router Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   HTTP\URI
 */
interface RouterIface
{
	/**
	 * Add a rule to the router.
	 *
	 * @param Rule\RuleIface The rule.
	 */
	public function addRule(Rule\RuleIface $rule);

	/**
	 * Perform the routing based on the added rules.
	 */
	public function route();
}
// EOF