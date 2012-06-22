<?php
namespace Evoke\Persistance;

/**
 * SessionIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistance
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