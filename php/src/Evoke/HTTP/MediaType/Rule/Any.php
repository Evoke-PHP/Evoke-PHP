<?php
namespace Evoke\HTTP\MediaType\Rule;

/**
 * Any
 *
 * A Media Type rule that matches any media type from the accept header.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
 */
class Any extends Rule
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * This rule matches anything!
	 *
	 * @return bool True.
	 */
	public function isMatch($mediaType)
	{
		return true;
	}
}
// EOF