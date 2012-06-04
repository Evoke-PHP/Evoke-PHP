<?php
namespace Evoke\View\XHTML\Message;

use Message\TreeIface;

class Failures extends Tree
{ 
	/** Construct a Failures Message view.
	 *  @param messageTree @object MessageTree.
	 *  @param attribs     @array  Attribs.
	 */
	public function __construct(
		TreeIface $messageTree,
		Array     $attribs = array('class' => 'Message Failure'))
	{
		parent::__construct($messageTree, $attribs);
	}
}
// EOF