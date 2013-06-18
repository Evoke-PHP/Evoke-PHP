<?php
/**
 * Page Interface
 *
 * @package Writer
 */
namespace Evoke\Writer;

/**
 * Page Interface
 *
 * The interface to an object that writes a page.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Writer
 */
interface PageIface extends WriterIface
{
	/**
	 * Write the end of a page.
	 */
	public function writeEnd();

	/**
	 * Write the start of a page.
	 */
	public function writeStart();
}
// EOF