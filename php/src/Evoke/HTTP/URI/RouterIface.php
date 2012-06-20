<?php
namespace Evoke\HTTP\URI;
/**
 * RouterIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
 */
interface RouterIface
{
	/**
	 * Add a rule to the router.
	 *
	 * @param Evoke\HTTP\URI\Rule\RuleIface The rule.
	 */
	public function addRule(Rule\RuleIface $rule);

	/**
	 * Perform the routing based on the added rules.
	 */
	public function route();
}
// EOF