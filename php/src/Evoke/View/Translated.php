<?php
namespace Evoke\View;

use \Evoke\Core\Iface;

abstract class Translated extends Base
{
	/** @property $translator
	 *  Translator \object
	 */
	protected $translator;

	/** Construct the View.
	 *  @param Writer     \object The Writer object.
	 *  @param Translator \object The Translator object.
	 */
	public function __construct(Iface\Writer $writer,
	                            Iface\Translator $translator)
	{
		parent::__construct($writer);
		
		$this->translator = $translator;
	}
}
// EOF