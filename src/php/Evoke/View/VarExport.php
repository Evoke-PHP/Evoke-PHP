<?php
/**
 * Variable Export View
 *
 * @package View
 */
namespace Evoke\View;

use Evoke\View\Data;

/**
 * Variable Export View
 *
 * This view is useful for a quick view of data being passed to a view.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View
 */
class VarExport extends Data
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
	 * @param mixed[] The data to view via var_export!
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
	protected function setVar($var)
	{
		$this->var = $var;
	}
}
// EOF
