<?php
namespace Evoke\Iface;

interface View
{
	/** Write the view.
	 *  @param params @array Parameters for the writing of the view.
	 */
	public function write(Array $params = array());
}
// EOF