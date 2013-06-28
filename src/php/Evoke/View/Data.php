<?php
/**
 * Data based view.
 *
 * @package View
 */
namespace Evoke\View;

use Evoke\Model\Data\DataIface;

/**
 * Data based view.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
abstract class Data implements ViewIface
{
	/**
	 * Data for the view.
	 * @var DataIface
	 */
	protected $data;
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Set the data for the view.
	 *
	 * @param DataIface Data for the view.
	 */
	public function setData(DataIface $data)
	{
		$this->data = $data;
	}
}
// EOF