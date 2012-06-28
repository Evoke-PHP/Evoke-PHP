<?php
namespace Evoke\Persistence;

/**
 * SessionIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistence
 */
interface SessionIface
{
	/**
	 * Ensure the session is started.
	 */
	public function ensure();

	/**
	 * Get the id for the session.
	 */
	public function getID();  
}
// EOF