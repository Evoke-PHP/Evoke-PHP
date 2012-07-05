<?php
namespace Evoke\View\XHTML\Message;

use Evoke\Message\TreeIface;

/**
 * Notifications
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Notifications extends Tree
{
	/**
	 * Construct a Notifications view.
	 *
	 * @param Evoke\Message\TreeIface Message Tree of the notifications.
	 * @param array                   Attributes.
	 */
	public function __construct(
		TreeIface $messageTree,
		Array     $attribs = array('class' => 'Message Notification'))
	{
		parent::__construct($messageTree, $attribs);
	}
}
// EOF