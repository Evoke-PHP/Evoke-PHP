<?php
/**
 * Create Interface
 *
 * @package Model
 */
namespace Evoke\Model\Mapper;

/**
 * Create Interface
 *
 * Decouples the creation of data from the storage mechanism.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
interface CreateIface
{
	/**
	 * Create some data in the storage.
	 *
	 * @param mixed[] The data to create.
	 */
	public function create(Array $data = array());
}
// EOF