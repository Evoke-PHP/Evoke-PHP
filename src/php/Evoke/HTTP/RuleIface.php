<?php
namespace Evoke\HTTP;

/**
 * RuleIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
 */
interface RuleIface
{
	/**
	 * Check to see if the rule matches.
	 *
	 * @param mixed The input to check for a match.
	 *
	 * @return bool Whether the rule matches.
	 */
	public function isMatch($input);
}
// EOF