<?php
/**
 * Tree
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

/**
 * Tree
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2013 Paul Young
 * @license   MIT
 * @package   Model\Data
 */
class Tree implements TreeIface
{
	/**
	 * The children of the current node.
	 * @var Tree[]
	 */
	protected $children = array();

	/**
	 * The value of the current node.
	 * @var mixed
	 */
	protected $value;
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add a node to the tree.
	 *
	 * @param TreeIface The node to add as a child.
	 */
	public function add(TreeIface $node)
	{
		$this->children[] = $node;
	}

	/**
	 * Get the value of the node.
	 *
	 * @return mixed The value that the node has been set to.
	 */
	public function get()
	{
		return $this->value;
	}

	/**
	 * Get the children of the node.
	 *
	 * @return TreeIface[]
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * Return whether the node has any children.
	 *
	 * @return bool Whether the node has any children.
	 */
	public function hasChildren()
	{
		return !empty($this->children);
	}

	/**
	 * Set the value of the node.
	 *
	 * @param mixed Value for the node.
	 */
	public function set($value)
	{
		$this->value = $value;
	}
}
// EOF