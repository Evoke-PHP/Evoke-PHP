<?php
/**
 * MessageBox Interface
 *
 * @package View
 */
namespace Evoke\View;

/**
 * MessageBox Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2013 Paul Young
 * @license   MIT
 * @package   View
 */
interface MessageBoxIface extends ViewIface
{
	/**
	 * Add a content element to the message box.
	 *
	 * @param mixed Message box element.
	 */
	public function addContent($element);

	/**
	 * Set the title for the message box.
	 *
	 * @param string Title of the message box.
	 */
	public function setTitle(/* String */ $title);
}
// EOF