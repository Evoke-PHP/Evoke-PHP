<?php
namespace Evoke;

interface ViewIface
{
	/** Get the view (of the data) to be written.
	 *  @param params @array Parameters for retrieving the view.
	 */
	public function get(Array $params = array());
}
// EOF