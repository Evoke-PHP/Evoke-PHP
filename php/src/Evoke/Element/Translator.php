<?php
namespace Evoke\Element;

/// The Element Translator class provides an element object with a Translator.
abstract class Translator extends Base
{
	/** @property $Translator
	 *  Translator \object
	 */
	protected $Translator;

	public function __construct(Array $setup)
	{
		$setup += array('Translator');

		if (!$setup['Translator'] instanceof \Evoke\Core\Iface\Translator)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Translator');
		}

		parent::__construct($setup);
		
		$this->Translator = $setup['Translator'];
	}
}
// EOF