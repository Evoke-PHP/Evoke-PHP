<?php
/**
 * HTTP Media Type Rule Interface
 *
 * @package HTTP\MediaType\Rule
 */
namespace Evoke\HTTP\MediaType\Rule;

/**
 * HTTP Media Type Rule Interface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP\MediaType\Rule
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