<?php
namespace Evoke\Core\Iface\HTTP\URI;

interface Router
{
	/** Add a rule to the router.
	 *  @param rule \object The rule object.
	 */
	public function addRule($rule);

	/// Reset the router rules.
	public function reset();

	/// Perform the routing based on the added rules.
	public function route();
}
// EOF