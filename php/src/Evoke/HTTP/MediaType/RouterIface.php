<?php
namespace Evoke\HTTP\MediaType;

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
	 * @param Evoke\HTTP\MediaType\Rule\RuleIface The rule to add to the router.
	 */
	public function addRule(Rule\RuleIface $rule);

	/**
	 * Perform the routing based on the rules.
	 */
	public function route();
}
// EOF