<?php
/**
 * View Interface
 *
 * @package View
 */
namespace Evoke\View;

/**
 * ViewIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
interface ViewIface
{
	/**
	 * Get the view (of the data) to be written.
	 *
	 * @param mixed[] Parameters for retrieving the view.
	 *
	 * @return mixed[] The view data.
	 */
	public function get(Array $params = array());
}
// EOF