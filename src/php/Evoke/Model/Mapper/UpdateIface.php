<?php
/**
 * Update Interface
 *
 * @package Model
 */
namespace Evoke\Model\Mapper;

/**
 * Update Interface
 *
 * Decouples the updating of data from the storage mechanism.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
interface UpdateIface
{
	/**
	 * Update some data from the storage mechanism.
	 *
	 * @param mixed[] The old data from storage.
	 * @param mixed[] The new data to set it to.
	 */
	public function update(Array $old = array(),
	                       Array $new = array());
}
// EOF