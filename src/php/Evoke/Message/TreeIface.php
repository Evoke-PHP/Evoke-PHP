<?php
/**
 * TreeIface
 *
 * @package Message
 */
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
	 * @param TreeIface MessageTree to append.
	 */
	public function append(TreeIface $child);

	/**
	 * Build a tree node.
	 *
	 * @return Tree The new tree node.
	 */
	public function buildNode();
	
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

	/**
	 * Whether the node has children.
	 *
	 * @return bool Whether the node has children.
	 */
	public function hasChildren();

	/**
	 * Return whether the node is empty.
	 *
	 * @return bool Whether the node is empty.
	 */
	public function isEmpty();
	/**
	 * Reset the node to the default empty state.
	 */
	public function reset();
	
	/**
	 * Set the node to the passed in values.
	 *
	 * @param string      Title for the message.
	 * @param string      Text for the message.
	 * @param TreeIface[] Children of the node.
	 */
	public function set(/* String */ $title,
	                    /* String */ $text,
	                    Array        $children = array());	
}
// EOF