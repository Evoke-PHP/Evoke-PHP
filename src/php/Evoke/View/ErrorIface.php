<?php
/**
 * Error Interface
 *
 * @package View
 */
namespace Evoke\View;

/**
 * Error Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2013 Paul Young
 * @license   MIT
 * @package   View
 */
interface ErrorIface extends ViewIface
{
	/**
	 * Set the error for the view.
	 *
	 * @param mixed[] Error.
	 */
	public function setError(Array $error);	
}
// EOF