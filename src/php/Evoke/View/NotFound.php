<?php
/**
 * Not Found View
 *
 * @package View
 */
namespace Evoke\View;

/**
 * Not Found View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View
 */
class NotFound extends MessageBox
{
	/**
	 * Construct the Not Found message box.
	 *
	 * @param mixed[] Attributes for the message box.
	 */
	public function __construct(
		Array $attribs = array('class' => 'Not_Found Message_Box System'))
	{
		parent::__construct($attribs);
	}
}
// EOF
