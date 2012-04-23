<?php
namespace Evoke\Element;

/// The Element Translator class provides an element object with a Translator.
abstract class Translator extends Base
{
	/** @property $translator
	 *  Translator \object
	 */
	protected $translator;

	public function __construct(\Evoke\Iface\Translator $translator,
	                            Array $attribs=array(),
	                            Array $pos=array())
	{
		parent::__construct($attribs, $pos);
		
		$this->translator = $translator;
	}
}
// EOF