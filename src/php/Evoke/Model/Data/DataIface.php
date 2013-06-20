<?php
/**
 * Data Interface
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

/**
 * Data Interface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model\Data
 */
interface DataIface extends \ArrayAccess, \Iterator
{
	/**
	 * Set the data that we are managing.
	 *
	 * @param mixed[] The data we want to manage.
	 */
	public function setData(Array $data);
}
// EOF
