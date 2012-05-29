<?php
namespace Evoke\View\Message;

class Failures extends Tree
{ 
	/** Construct a Failures Message view.
	 *  @param translator  @object Translator.
	 *  @param messageTree @object MessageTree.
	 *  @param attribs     @array  Attribs.
	 */
	public function __construct(
		Iface\Translator  $translator,
		Iface\MessageTree $messageTree,
		Array             $attribs = array('class' => 'Message Failure'))
	{
		parent::__construct($translator, $messageTree, $attribs);
	}
}
// EOF