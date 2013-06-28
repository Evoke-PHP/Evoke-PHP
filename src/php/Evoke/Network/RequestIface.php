<?php
/**
 * Request Interface
 *
 * @package Network
 */
namespace Evoke\Network;

/**
 * Request Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Network
 */
interface RequestIface
{
	/**
	 * Get the URI of the request (without the query string).
	 *
	 * @return string The URI of the request.
	 */
	public function getURI();
}
// EOF