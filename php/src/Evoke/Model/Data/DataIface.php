<?php
namespace Evoke\Model\Data;

interface DataIface extends \ArrayAccess, \Iterator
{
	/** Set the data that we are managing.
	 *
	 *  @param data @array The data we want to manage.
	 */
	public function setData(Array $data);
}
// EOF
