<?php
/**
 * Read Interface
 *
 * @package Model
 */
namespace Evoke\Model\Mapper;

/**
 * Read Interface
 *
 * Decouples the reading of data from the storage mechanism.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
interface ReadIface
{
	/**
	 * Read some data from the storage mechanism.
	 *
	 * @param mixed[] The conditions to match in the mapped data.
	 */
	public function read(Array $params = array());
}
// EOF