<?php
/**
 * Variable Export View
 *
 * @package View\HTML5
 */
namespace Evoke\View\HTML5;

use Evoke\View\ViewIface;

/**
 * Variable Export View
 *
 * This view is useful for a quick view of data being passed to a view.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View\HTML5
 */
class VarExport implements ViewIface
{
	/**
	 * Variable to export.
	 * @var mixed
	 */
	protected $var;
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the parameters.
	 *
	 * @return mixed[] The view of the data.
	 */
	public function get()
	{
		return array('div',
		             array('class' => 'Var_Export'),
		             var_export($this->var, true));
	}

	/**
	 * Set the var to export.
	 *
	 * @param mixed Var to export.
	 */
	public function set($var)
	{
		$this->var = $var;
	}
}
// EOF
