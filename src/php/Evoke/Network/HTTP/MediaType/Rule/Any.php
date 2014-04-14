<?php
/**
 * HTTP Media Type Any Rule
 *
 * @package Network\HTTP\MediaType\Rule
 */
namespace Evoke\Network\HTTP\MediaType\Rule;

/**
 * HTTP Media Type Any Rule
 *
 * A Media Type rule that matches any media type from the accept header.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Network\HTTP\MediaType\Rule
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
	public function isMatch()
	{
		return true;
	}
}
// EOF