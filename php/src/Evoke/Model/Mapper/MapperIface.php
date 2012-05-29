<?php
namespace Evoke\Model\Mapper;

interface MapperIface
{
	/** Fetch some data from the mapper (specified by params).
	 *  @param params \array The conditions to match in the mapped data.
	 */
	public function fetch(Array $params = array());
}
// EOF