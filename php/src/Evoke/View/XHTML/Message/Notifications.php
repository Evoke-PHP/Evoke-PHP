<?php
namespace Evoke\View\XHTML\Message;

use Evoke\Message\TreeIface;

class Notifications extends Tree
{
	/** Construct a Notifications view.
	 *  @param messageTree @object Message Tree.
	 *  @param attribs     @array  Attribs.
	 */
	public function __construct(
		TreeIface $messageTree,
		Array     $attribs = array('class' => 'Message Notification'))
	{
		parent::__construct($messageTree, $attribs);
	}
}
// EOF