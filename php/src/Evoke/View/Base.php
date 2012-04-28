<?php
namespace Evoke\View;

use Evoke\Iface;

abstract class Base implements Iface\View
{
	/** @property $writer
	 *  Writer \object
	 */
	protected $writer;

	/** Construct the View.
	 *  @param Writer \object The writer object.
	 */
	public function __construct(Iface\Writer $writer)
	{
		$this->writer = $writer;
	}
}
// EOF