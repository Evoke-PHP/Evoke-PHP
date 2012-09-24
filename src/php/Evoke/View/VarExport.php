<?php
/**
 * VarExport View
 *
 * @package View
 */
namespace Evoke\View;

/**
 * VarExport View
 *
 * This view is useful for a quick view of data being passed to a view.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class VarExport implements ViewIface
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the parameters.
	 *
	 * @param mixed[] The data to view via var_export!
	 */
	public function get(Array $params = array())
	{
		return array('div',
		             array('class' => 'Var_Export'),
		             var_export($params, true));
	}
}
// EOF
