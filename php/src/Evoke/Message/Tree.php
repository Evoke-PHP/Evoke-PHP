<?php
namespace Evoke\Message;
/// @todo Create a recursive iterator for using the Tree?

use InvalidArgumentException;

/// Tree with title and text at each node.
class Tree implements TreeIface
{
	/** @property $children
	 *  @array Children of the Message Tree node.
	 */
	protected $children;
	
	/** @property $text
	 *  @string The text for the message.
	 */
	protected $text;

	/** @property $title
	 *  @string The title of the message.
	 */
	protected $title;

	/** Construct a message tree node.
	 *  @param title @string Title for the message.
	 *  @param text  @string Text for the message.
	 */
	public function __construct(/* String */ $title,
	                            /* String */ $text)
	{
		if (!is_string($title))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires title as string');
		}

		if (!is_string($text))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires text as string');
		}

		$this->children = array();
		$this->text     = $text;
		$this->title    = $title;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Append a child message tree object to the tree node.
	 *  @param Child @object MessageTree to append.
	 */
	public function append(TreeIface $child)
	{
		$this->children[] = $child;
	}

	public function buildNode($text, $title)
	{
		return new Tree($text, $title);
	}
	
	public function getChildren()
	{
		return $this->children;
	}
	
	/** Get the text of the message node.
	 *  @return @string The text for the message node.
	 */
	public function getText()
	{
		return $this->text;
	}
	
	/** Get the title of the message node.
	 *  @return @string The title of the message node.
	 */
	public function getTitle()
	{
		return $this->title;
	}

	public function hasChildren()
	{
		return !empty($this->children);
	}
}
// EOF