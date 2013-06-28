<?php
/**
 * HTTP Media Type Rule Interface
 *
 * @package Network\HTTP\MediaType\Rule
 */
namespace Evoke\Network\HTTP\MediaType\Rule;

/**
 * HTTP Media Type Rule Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Network\HTTP\MediaType\Rule
 */
interface RuleIface
{
	/**
	 * Get the output format for the media type.
	 *
	 * @param mixed[] The media type.
	 */
	public function getOutputFormat(Array $mediaType);

	/**
	 * Check to see if the rule matches.
	 *
	 * @param mixed[] The input to check for a match.
	 * @return bool Whether the rule matches.
	 */
	public function isMatch(Array $mediaType);
	
}
// EOF