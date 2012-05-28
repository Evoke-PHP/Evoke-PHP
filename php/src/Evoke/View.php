<?php
namespace Evoke;

use Evoke\Iface;

abstract class View implements Iface\View
{
	/** @property $translator
	 *  @object Translator
	 */
	protected $translator;

	/** Construct the View.
	 *  @param Translator @object Translator.
	 */
	public function __construct(Iface\Translator $translator)
	{
		$this->translator = $translator;
	}
}
// EOF