<?php
/**
 * Abstract View
 *
 * @package View
 */
namespace Evoke\View;

use Evoke\Model\Data\DataIface;

/**
 * Abstract View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View
 */
abstract class View implements ViewIface
{
	protected
		/**
		 * Data
		 * @var DataIface
		 */
		$data,
		
		/**
		 * Parameters
		 * @var mixed[]
		 */
		$params;

	/******************/
	/* Public Methods */
	/******************/

	abstract public function get();
	
	/**
	 * Set the data for the view.
	 *
	 * @param DataIface The data to set for the view.
	 */
	public function setData(DataIface $data)
	{
		$this->data = $data;
	}

	/**
	 * Set a paramter for the view.
	 *
	 * @param string The parameter to set for the view.
	 * @param mixed  The value to set the parameter to.
	 */
	public function setParam(/* String */ $param, /* Mixed */ $value)
	{
		$this->params[$param] = $value;
	}
}
// EOF