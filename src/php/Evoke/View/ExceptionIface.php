<?php
/**
 * Exception Interface
 *
 * @package View
 */
namespace Evoke\View;

/**
 * Exception Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View
 */
interface ExceptionIface extends ViewIface
{
	/**
	 * Set the exception for the view.
	 *
	 * @param \Exception The exception for the view.	 
	 */
	public function setException(\Exception $exception);
}
// EOF