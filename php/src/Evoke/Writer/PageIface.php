<?php
namespace Evoke\Writer;

/// The interface to an object that writes a page (using a buffer).
interface PageIface extends WriterIface
{
	/// Write the end of a page.
	public function writeEnd();

	/// Write the start of a page.
	public function writeStart();
}
// EOF