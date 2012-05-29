<?php
namespace Evoke\HTTP\MediaType;

interface RouterIface
{
	/** Add a rule to the router.
	 *  @param rule \mixed The rule to add to the router.
	 */
	public function addRule(Rule $rule);

	/// Perform the routing based on the rules.
	public function route();
}
// EOF