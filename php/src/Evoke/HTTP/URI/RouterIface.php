<?php
namespace Evoke\HTTP\URI;

interface RouterIface
{
	/** Add a rule to the router.
	 *  @param rule \object The rule object.
	 */
	public function addRule(Rule\RuleIface $rule);

	/// Perform the routing based on the added rules.
	public function route();
}
// EOF