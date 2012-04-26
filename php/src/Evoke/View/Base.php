<?php
namespace Evoke\View;

use Evoke\Iface\Core as ICore;

abstract class Base implements Evoke\Iface\View
{
	/** @property $writer
	 *  Writer \object
	 */
	protected $writer;

	/** Construct the View.
	 *  @param Writer \object The writer object.
	 */
	public function __construct(ICore\Writer $writer)
	{
		$this->writer = $writer;
	}
}
// EOF