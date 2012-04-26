<?php
namespace Evoke\View;

use Evoke\Iface\Core as ICore;

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
	public function __construct(ICore\Writer     $writer,
	                            ICore\Translator $translator)
	{
		parent::__construct($writer);
		
		$this->translator = $translator;
	}
}
// EOF