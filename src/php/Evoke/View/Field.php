<?php
/**
 * Field View.
 *
 * @package View
 */
namespace Evoke\View;

/**
 * Field View.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Field implements ViewIface
{
	/******************/
	/* Public Methods */
	/******************/

	public function get(Array $params = array())
	{
		$params += array('Field' => 'UNKNOWN',
		                 'Value' => '');
		
		return array('div',
		             array('class' => 'Field ' . $params['Field']),
		             $params['Value']);
	}
}
// EOF