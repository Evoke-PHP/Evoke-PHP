<?php
/**
 * Parameter based view.
 *
 * @package View
 */
namespace Evoke\View;

/**
 * A view based on the parameters.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View
 */
class Param extends View
{
	/**
	 * Construct a Param object.
	 *
	 * @param string Tag.
	 */
	public function __construct(/* string */ $tag = 'div')
	{
		$this->params['Tag'] = $tag;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add a fixed attribute to the element.
	 *
	 * @param string Attribute
	 * @param mixed  Value.
	 */
	public function addAttrib($attrib, $value)
	{
		$this->attribList[$attrib][] = array('By_Param' => false,
		                                     'Value'    => $value);
	}

	/**
	 * Add an attribute to the element that will be supplied by the view
	 * parameters.
	 *
	 * @param string Attribute
	 * @param string Parameter to be used as the value of the attribute.
	 */
	public function addAttribParam($attrib, $param)
	{
		$this->attribList[$attrib][] = array('By_Param' => true,
		                                     'Value'    => $param);
	}

	/**
	 * Add fixed text to an element.
	 *
	 * @param string Text to add to the element.
	 */
	public function addText($text)
	{
		$this->childList[] = array('By_Param' => false,
		                           'Value'    => $text);
	}

	/**
	 * Add text to an element which will be supplied by the view parameters.
	 *
	 * @param string Parameter to be used as the value of the text.
	 */
	public function addTextParam($param)
	{
		$this->childList[] = array('By_Param' => true,
		                           'Value'    => $param);
	}

	/**
	 * Get the output of the view.
	 */
	public function get()
	{
		$attribs = array();

		foreach ($this->attribList as $attrib => $entries)
		{
			if (!isset($attribs[$attrib]))
			{
				$attribs[$attrib] = '';
			}
			
			foreach ($entries as $entry)
			{
				$attribs[$attrib] .= $entry['By_Param'] ?
					$this->params[$entry['Value']] :
					$entry['Value'];
			}
		}
		
		$children = array();

		foreach ($this->childList as $entry)
		{
			$children[] = $entry['By_Param'] ?
				$this->params[$entry['Value']] :
				$entry['Value'];
		}
		
		return array($this->params['Tag'], $attribs, $children);
	}
}
// EOF
