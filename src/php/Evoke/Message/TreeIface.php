<?php
namespace Evoke\Message;

/**
 * TreeIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Message
 */
interface TreeIface
{
	/**
	 * Append a child message tree object to the tree node.
	 *
	 * @param Evoke\Message\TreeIface MessageTree to append.
	 */
	public function append(TreeIface $child);
	
	/**
	 * Get the text of the message node.
	 *
	 * @return string The text for the message node.
	 */
	public function getText();

	/**
	 * Get the title of the message node.
	 *
	 * @return string The title of the message node.
	 */
	public function getTitle();
}
// EOF