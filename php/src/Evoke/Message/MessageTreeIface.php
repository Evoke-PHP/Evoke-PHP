<?php
namespace Evoke;

interface MessageTreeIface
{
	/** Append a child message tree object to the tree node.
	 *  @param Child \object MessageTree to append.
	 */
	public function append(MessageTree $child);
	
	/** Get the text of the message node.
	 *  @return \string The text for the message node.
	 */
	public function getText();

	/** Get the title of the message node.
	 *  @return \string The title of the message node.
	 */
	public function getTitle();
}
// EOF