<?php
/**
 * TreeIface
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

/**
 * TreeIface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2013 Paul Young
 * @license   MIT
 * @package   Model\Data
 */
interface TreeIface
{
	/**
	 * Add a node to the tree.
	 *
	 * @param TreeIface The node to add as a child.
	 */
	public function add(TreeIface $node);

	/**
	 * Get the value of the node.
	 *
	 * @return mixed The value that the node has been set to.
	 */
	public function get();

	/**
	 * Get the children of the node.
	 *
	 * @return TreeIface[]
	 */
	public function getChildren();
	
	/**
	 * Return whether the node has any children.
	 *
	 * @return bool Whether the node has any children.
	 */
	public function hasChildren();

	/**
	 * Set the value of the node.
	 *
	 * @param mixed Value for the node.
	 */
	public function set($value);
}
// EOF