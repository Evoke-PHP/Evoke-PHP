<?php
/**
 * View Interface
 *
 * @package View
 */
namespace Evoke\View;

use Evoke\Model\Data\DataIface;

/**
 * View Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View
 */
interface ViewIface
{
	/**
	 * Get the view of the data ready for writing.
	 *
	 * @return mixed[] The view of the data according to the view parameters.
	 */
	public function get();

	/**
	 * Set the data for the view.
	 *
	 * @param DataIface The data to set for the view.
	 */
	public function setData(DataIface $data);

	/**
	 * Set a paramter for the view.
	 *
	 * @param string The parameter to set for the view.
	 * @param mixed  The value to set the parameter to.
	 */
	public function setParam(/* String */ $param, /* Mixed */ $value);
}
// EOF