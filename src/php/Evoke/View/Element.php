<?php
namespace Evoke\View;

use InvalidArgumentException;

/**
 * An XHTML element view.
 */
class Element extends View
{
	protected
		/** A map of attributes that should be retrieved from the get
		 *  parameters.
		 *  @var string[]
		 */
		$attribMap,
		
		/** Attributes for the element.
		 * @var string[]
		 */
		$attribs,

		/** Children
		 * @var mixed
		 */
		$children,

		/** Tag
		 * @var string
		 */
		$tag,

		/** Map of the position of the text items that are to be obtained from
		 *  the get parameters to the parameter that should be used.
		 *  @var string[]
		 */
		$textMap;

	/**
	 * Construct a simple XHTML element view.
	 *
	 * @param string   Tag.
	 * @param string[] Attribs.
	 * @param mixed    Children.
	 */
	public function __construct(/* String */ $tag,
	                            Array        $attribs  = array(),
	                            /* Mixed  */ $children = NULL)
	{
		if (!is_string($tag))
		{
			throw new InvalidArgumentException('needs tag as string');
		}

		$this->attribMap = array();
		$this->attribs   = $attribs;

		if (!isset($children))
		{
			$this->children = array();
		}
		elseif (is_string($children))
		{
			$this->children = array($children);
		}
		else
		{
			$this->children = $children;
		}
		
		$this->tag       = $tag;
		$this->textMap   = array();
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add an attribute to be used from the get parameters.
	 *
	 * @param string The attribute to add.
	 * @param string The parameter to use for the value of the attribute.
	 */
	public function addAttrib(/* String */ $attrib,
	                          /* String */ $value,
	                          /* String */ $separator = ' ')
	{
		
		$this->attribMap[$attrib] = $param;
	}

	/**
	 * Add an attribute to be used from the get parameters.
	 *
	 * @param string The attribute to add.
	 * @param string The parameter to use for the value of the attribute.
	 */
	public function addAttribParam(/* String */ $attrib, /* String */ $param)
	{
		$this->attribMap[$attrib] = $param;
	}
	
	/**
	 * Add a child element (text or element array).
	 *
	 * @param mixed The child to add.
	 */
	public function addChild(/* Mixed */ $child)
	{
		$this->children[] = $child;
	}

	/**
	 * Add some text to be used from the get parameters.
	 *
	 * @param string The parameter to be used for the text.
	 */
	public function addTextParam(/* String */ $param)
	{
		// No need to -1 as we add the placeholder afterwards.
		$this->textMap[count($this->children)] = $param;
		$this->children[] = '';
	}
	
	/**
	 * Get the view (of the data) to be written.
	 *
	 * @return mixed[] The view data.
	 */
	public function get()
	{
		$attribs = $this->attribs;
		
		foreach ($this->attribMap as $attrib => $param)
		{
			$attribs[$attrib] .= ' ' . $params[$param];
		}

		$children = $this->children;
		
		foreach ($this->textMap as $index => $param)
		{
			$children[$index] = $params[$param];
		}
		
		return array($this->tag, $attribs, $children);
	}
}
// EOF
