<?php
namespace Evoke\HTTP\URI\Rule;

/**
 * RuleIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
 */
interface RuleIface extends \Evoke\HTTP\RuleIface
{
	/**
	 * Get the controller.
	 *
	 * @param string The URI to get the controller from.
	 * @return string The uri mapped towards the controller with the rule.
	 */	
	public function getController($uri);

	/**
	 * Return the parameters for the URI.
	 *
	 * @param string The URI.
	 * @return mixed[] The parameters for the class.
	 */
	public function getParams($uri);
	
	/**
	 * Check whether the rule is authoritative.
	 *
	 * @return bool Whether the rule can definitely give the final route when it
	 *              matches the input.
	 */
	public function isAuthoritative();
}
// EOF