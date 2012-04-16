<?php
namespace Evoke\Core;
/// \todo Create a recursive iterator for using the MessageTree.

/// A MessageTree with title and text at each node.
class MessageTree implements Iface\MessageTree
{
	/** @property $children
	 *  \array Children of the Message Tree node.
	 */
	protected $children;
	
	/** @property $text
	 *  \string The text for the message.
	 */
	protected $text;

	/** @property $title
	 *  \string The title of the message.
	 */
	protected $title;

	/** Construct a message tree node.
	 *  @param title \string Title for the message.
	 *  @param text  \string Text for the message.
	 */
	public function __construct($title, $text)
	{
		if (!is_string($title))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires title as string');
		}

		if (!is_string($text))
		{
			throw new \InvalidArgumentException(
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
	 *  @param Child \object MessageTree to append.
	 */
	public function append(Iface\MessageTree $Child)
	{
		$this->children[] = $Child;
	}

	public function buildNode($text, $title)
	{
		return new MessageTree($text, $title);
	}
	
	public function getChildren()
	{
		return $this->children;
	}
	
	/** Get the text of the message node.
	 *  @return \string The text for the message node.
	 */
	public function getText()
	{
		return $this->text;
	}
	
	/** Get the title of the message node.
	 *  @return \string The title of the message node.
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
