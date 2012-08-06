<?php
namespace Evoke\View\Message;

use Evoke\Message\TreeIface;

/**
 * Failures
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Failures extends Tree
{ 
	/**
	 * Construct a Failures Message view.
	 *
	 * @param Evoke\Message\TreeIface Failures message tree.
	 * @param array                   Attribs.
	 */
	public function __construct(
		TreeIface $messageTree,
		Array     $attribs = array('class' => 'Message Failure'))
	{
		parent::__construct($messageTree, $attribs);
	}
}
// EOF