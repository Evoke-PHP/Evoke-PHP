<?php
namespace Evoke\View\Message;

class Notifications extends Array
{
	/** Construct a Notifications view.
	 *  @param translator  @object Translator.
	 *  @param messageTree @object MessageTree.
	 *  @param attribs     @array  Attribs.
	 */
	public function __construct(
		Iface\Translator  $translator,
		Iface\MessageTree $messageTree,
		Array             $attribs = array('class' => 'Message Notification'))
	{
		parent::__construct($translator, $messageTree, $attribs);
	}
}
// EOF