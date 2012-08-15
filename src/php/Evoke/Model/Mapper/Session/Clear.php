<?php
namespace Evoke\Model\Mapper\Session;

/**
 * @todo Make this a composite model, it is no longer a mapper.
 */

/**
 * Clear
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Clear extends Session
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Clear the session of its values.
	 */
	public function remove()
	{
		$this->sessionManager->remove();
	}
}
// EOF