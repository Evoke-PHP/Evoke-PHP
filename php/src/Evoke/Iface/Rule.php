<?php
namespace Evoke\Iface;

interface Rule
{
	/** Check to see if the rule matches.
	 *  @return \bool Whether the rule matches.
	 */
	public function isMatch($input);
}
// EOF