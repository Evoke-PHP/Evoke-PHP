<?php
namespace Evoke\Element;

/// The Element Translator class provides an element object with a Translator.
abstract class Translator extends \Evoke\ELement
{
	/** @property $translator
	 *  Translator \object
	 */
	protected $translator;

	/** Construct a translated element.
	 *  @param attribs @array Attributes for the element.
	 *  @param pos     @array Positional information for the element.
	 */
	public function __construct(\Evoke\Iface\Translator $translator,
	                            Array                   $attribs = array(),
	                            Array                   $pos     = array())
	{
		parent::__construct($attribs, $pos);
		
		$this->translator = $translator;
	}
}
// EOF