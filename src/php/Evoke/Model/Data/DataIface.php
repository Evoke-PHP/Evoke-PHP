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
	 * Get the current record as a simple array (without iterator or class
	 * properties).
	 *
	 * @return mixed[] The current record as a simple array.
	 */
	public function getRecord();

	/**
	 * Whether the data is empty or not.
	 *
	 * @return bool Whether the data is empty or not.
	 */
	public function isEmpty();
	
	/**
	 * Set the data that we are managing.
	 *
	 * @param mixed[] The data we want to manage.
	 */
	public function setData(Array $data);
}
// EOF
