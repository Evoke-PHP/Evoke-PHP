<?php
namespace Evoke\View\Message;

use Evoke\Message\TreeIface,
	Evoke\Service\TranslatorIface;

class Notifications extends Tree
{
	/** Construct a Notifications view.
	 *  @param translator  @object Translator.
	 *  @param messageTree @object Message Tree.
	 *  @param attribs     @array  Attribs.
	 */
	public function __construct(
		TranslatorIface $translator,
		TreeIface       $messageTree,
		Array           $attribs = array('class' => 'Message Notification'))
	{
		parent::__construct($translator, $messageTree, $attribs);
	}
}
// EOF