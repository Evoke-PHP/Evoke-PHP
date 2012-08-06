<?php
namespace Evoke\Model\Mapper;

/**
 * MapperIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
interface MapperIface
{
	/**
	 * Fetch some data from the mapper (specified by params).
	 *
	 * @param mixed[] The conditions to match in the mapped data.
	 */
	public function fetch(Array $params = array());
}
// EOF