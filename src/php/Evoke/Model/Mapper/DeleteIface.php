<?php
/**
 * Delete Interface
 *
 * @package Model
 */
namespace Evoke\Model\Mapper;

/**
 * Delete Interface
 *
 * Decouples the deletion of data from the storage mechanism.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
interface DeleteIface
{
	/**
	 * Delete some data from storage.
	 *
	 * @param mixed[] The conditions to match in the mapped data.
	 */
	public function delete(Array $params = array());
}
// EOF