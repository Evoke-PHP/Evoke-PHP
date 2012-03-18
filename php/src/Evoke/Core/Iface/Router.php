<?php
namespace Evoke\Core\Iface;

interface Router
{
	/** Add a rule to the router.
	 *  @param rule \mixed The rule to add to the router.
	 */
	public function addRule($rule);

	/// Reset the router rules.
	public function reset();

	/// Perform the routing based on the rules.
	public function route();
}
// EOF