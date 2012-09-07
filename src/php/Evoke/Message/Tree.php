<?php
/**
 * Message Tree
 *
 * @package Message
 */
namespace Evoke\Message;

use InvalidArgumentException;

/**
 * Message Tree
 *
 * Message Tree with a title and text at each node.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Message
 */
class Tree implements TreeIface
{
	/**
	 * Children of the Message Tree node.
	 * @var mixed[]
	 */
	protected $children;
	
	/**
	 * The text for the message.
	 * @var string
	 */
	protected $text;

	/**
	 * The title of the message.
	 * @var string
	 */
	protected $title;

	/**
	 * Construct a message tree node.
	 *
	 * @param null|string Title for the message.
	 * @param null|string Text for the message.
	 * @param array Children of the node.
	 */
	public function __construct(/* Mixed */ $title    = NULL,
	                            /* Mixed */ $text     = NULL,
	                            /* Array */ $children = array())
	{
		$this->reset($title, $text, $children);
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Append a child message tree object to the tree node.
	 *
	 * @param TreeIface MessageTree to append.
	 */
	public function append(TreeIface $child)
	{
		$this->children[] = $child;
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
	 * Reset the node to passed in values or default empty state.
	 *
	 * @param null|string Title for the message.
	 * @param null|string Text for the message.
	 * @param array Children of the node.
	 */
	public function reset(/* Mixed */ $title    = NULL,
	                      /* Mixed */ $text     = NULL,
	                      /* Array */ $children = array())
	{
		if (isset($title) && !is_string($title))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires title as string or NULL');
		}

		if (isset($text) && !is_string($text))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires text as string or NULL');
		}

		$this->children = $children;
		$this->text     = $text;
		$this->title    = $title;
	}
	
	/**
	 * Set the text of the node.
	 *
	 * @param string Text for the node.
	 */
	public function setText(/* String */ $text)
	{
		if (!is_string($text))
		{
			throw new InvalidArgumentException('Text must be a string.');
		}
		
		$this->text = $text;
	}

	/**
	 * Set the title of the node.
	 *
	 * @param string Title for the node.
	 */
	public function setTitle(/* String */ $title)
	{
		if (!is_string($title))
		{
			throw new InvalidArgumentException('Title must be a string.');
		}
		
		$this->title = $title;
	}	
}
// EOF
