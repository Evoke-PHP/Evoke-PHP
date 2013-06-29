<?php
/**
 * URI Rule Interface
 *
 * @package Network\URI\Rule
 */
namespace Evoke\Network\URI\Rule;

/**
 * URI Rule Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Network\URI\Rule
 */
interface RuleIface
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

	/**
	 * Check to see if the rule matches.
	 *
	 * @param string The URI to check for a match.
	 * @return bool Whether the rule matches.
	 */
	public function isMatch($uri);
	
}
// EOF