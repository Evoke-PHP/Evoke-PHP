<?php
namespace Evoke\View;

use Evoke\Service\TranslatorIface;

abstract class View implements ViewIface
{
	/** @property $translator
	 *  @object Translator
	 */
	protected $translator;

	/** Construct the View.
	 *  @param Translator @object Translator.
	 */
	public function __construct(TranslatorIface $translator)
	{
		$this->translator = $translator;
	}
}
// EOF