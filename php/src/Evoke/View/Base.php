<?php
namespace Evoke\View;

use \Evoke\Core\Iface;

abstract class Base implements Iface\View
{
	/** @property $Writer
	 *  Writer \object
	 */
	protected $Writer;

	/** Construct the View.
	 *  @param Writer \object The writer object.
	 */
	public function __construct(Iface\Writer $Writer)
	{
		$this->Writer = $Writer;
	}
}
// EOF