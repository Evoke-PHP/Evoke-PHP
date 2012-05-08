<?php
namespace Evoke\Iface\Model;

interface Mapper
{
	/** Fetch some data from the mapper (specified by params).
	 *  @param params \array The conditions to match in the mapped data.
	 */
	public function fetch(Array $params);
	
	/// Fetch the data using the mapper.
	public function fetchAll();
}
// EOF