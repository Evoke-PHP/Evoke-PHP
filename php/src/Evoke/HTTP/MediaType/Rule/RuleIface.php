<?php
namespace Evoke\HTTP\MediaType\Rule;

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
	 * Get the output format for the media type.
	 *
	 * @param mixed[] The media type.
	 */
	public function getOutputFormat(Array $mediaType);
}
// EOF