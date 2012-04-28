<?php
namespace Evoke\Iface\Writer;

/// The interface to an object that writes a page (using a buffer).
interface Page extends \Evoke\Iface\Writer
{
	/// Write the end of a page.
	public function writeEnd();

	/// Write the start of a page.
	public function writeStart();
}
// EOF