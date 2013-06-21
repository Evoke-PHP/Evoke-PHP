<?php
/**
 * Message Tree View
 *
 * @package View\Message
 */
namespace Evoke\View\Message;

/**
 * Message Tree View
 *
 * Message Tree with a title and text at each node.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\Message
 */
class Tree implements TreeIface
{
	/**
	 * Children of the Message Tree node.
	 * @var mixed[]
	 */
	protected $children = array();
	
	/**
	 * The text for the message.
	 * @var string
	 */
	protected $text = NULL;

	/**
	 * The title of the message.
	 * @var string
	 */
	protected $title = NULL;

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Append a child message tree object to the tree node.
	 *
	 * @param TreeIface Message Tree to append.
	 */
	public function append(TreeIface $child)
	{
		$this->children[] = $child;
	}

	/**
	 * Build a tree node.
	 *
	 * @return Tree The new tree node.
	 */
	public function buildNode()
	{
		return new self;
	}		
	
	/**
	 * Return whether the node has been set.
	 *
	 * @return bool Whether the node has been set.
	 */
	public function exists()
	{
		return isset($this->title) || isset($this->text) ||
			!empty($this->children);
	}

	/**
	 * Get the children of the tree node.
	 *
	 * @return TreeIface[] The children of the tree node.
	 */
	public function getChildren()
	{
		return $this->children;
	}
	
	/**
	 * Get the text of the message node.
	 *
	 * @return string The text for the message node.
	 */
	public function getText()
	{
		return $this->text;
	}
	
	/**
	 * Get the title of the message node.
	 *
	 * @return string The title of the message node.
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * Whether the node has children.
	 *
	 * @return bool Whether the node has children.
	 */
	public function hasChildren()
	{
		return !empty($this->children);
	}

	/**
	 * Return whether the node is empty.
	 *
	 * @return bool Whether the node is empty.
	 */
	public function isEmpty()
	{
		return empty($this->title) && empty($this->text) &&
			empty($this->children);
	}
	
	/**
	 * Reset the node to the default empty state.
	 */
	public function reset()
	{
		$this->children = array();
		$this->text     = NULL;
		$this->title    = NULL;
	}
	
	/**
	 * Set the node to the passed in values.
	 *
	 * @param string      Title for the message.
	 * @param string      Text for the message.
	 * @param TreeIface[] Children of the node.
	 */
	public function set(/* String */ $title,
	                    /* String */ $text,
	                    Array        $children = array())
	{
		$this->children = $children;
		$this->text     = $text;
		$this->title    = $title;
	}
}
// EOF
