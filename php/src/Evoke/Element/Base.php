<?php
namespace Evoke\Element;
/** Element - Provide an element container with array access suitable for
 *  an XML Writing Resource that takes the form:
 *  \verbatim
 *  array(0 => Tag,
 *        1 => Attribs,
 *        2 => Options);
 *  \endverbatim
 */
class Base implements \Evoke\Core\Iface\Element
{
	/** @property $attribs
	 *  \array The default attributes for the element.
	 */
	protected $attribs;

	/** @property $attribsPos
	 *  \int The position of the attributes in the XML arrays being written.
	 */
	protected $attribsPos;	

	/** @property $el
	 *  The element data.
	 */
	protected $el;
	
	/** @property $options
	 *  \array The default options for the element.
	 */
	protected $options;

	/** @property $optionsPos
	 *  \int The position of the options in the XML arrays being written.
	 */
	protected $optionsPos;
	
	/** @property $tagsPos
	 *  \int The position of the tags in the XML arrays being written.
	 */
	protected $tagsPos;
   
	public function __construct(Array $setup=array())
	{
		$setup += array('Attribs'     => array(),
		                'Attribs_Pos' => 1,
		                'Options'     => array('Children' => array(),
		                                       'Finish'   => true,
		                                       'Start'    => true,
		                                       'Text'     => NULL),
		                'Options_Pos' => 2,
		                'Tag_Pos'     => 0);

		$this->el = array();

		$this->attribs    = $setup['Attribs'];
		$this->attribsPos = $setup['Attribs_Pos'];
		$this->options    = $setup['Options'];
		$this->optionsPos = $setup['Options_Pos'];
		$this->tagPos     = $setup['Tag_Pos'];
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Add a class to the element.
	 *  @param c \string The class to be added to the element.
	 */
	public function addClass($c)
	{
		if (isset($this->el[$this->attribsPos]['class']))
		{
			$this->el[$this->attribsPos]['class'] = preg_replace(
				'/(^' . $c . '\s?|\s?' . $c . '\s?|\s?' . $c . '$)/',
				'',
				$this->el[$this->attribsPos]['class']) . ' ' . $c;
		}
		else
		{
			if (!isset($this->el[$this->attribsPos]))
			{
				$this->el[$this->attribsPos] = array();
			}
	 
			$this->el[$this->attribsPos]['class'] = $c;
		}
	}

	/** Add a class to the element.
	 *  @param attrib \string The attribute to be appended to.
	 *  @param value \string The value to be appended to the attribute.
	 */
	public function appendAttrib($attrib, $value)
	{
		if (isset($this->el[$this->attribsPos][$attrib]))
		{
			$this->el[$this->attribsPos][$attrib] .= $value;
		}
		else
		{
			if (!isset($this->el[$this->attribsPos]))
			{
				$this->el[$this->attribsPos] = array();
			}
	 
			$this->el[$this->attribsPos][$attrib] = $value;
		}
	}

	/** Set the element from the array values passed in for the element.  The
	 *  element should be passed in as an array with the numerical keys
	 *  corresponding to the desired element data.  By default this is:
	 *  \verbatim
	 *  0 => Tag
	 *  1 => Attribs
	 *  2 => Options
	 *  \endverbatim
	 *
	 *  Atrribs and Options data is merged with the Default values from the
	 *  setup.
	 *
	 *  Any data that is not passed in is not set.
	 *  Any data with a key outside of this range is ignored.
	 *
	 *  @param element \mixed The element data to set ourselves to must be array
	 *  accessible.
	 *  \return \array Return the data that has been set.
	 */
	public function set(Array $element)
	{
		$this->el = array();
      
		if (isset($element[$this->tagPos]))
		{
			$this->el[$this->tagPos] = $element[$this->tagPos];
		}

		if (isset($element[$this->attribsPos]))
		{
			$this->el[$this->attribsPos] =
				array_merge($this->attribs, $element[$this->attribsPos]);
		}

		if (isset($element[$this->optionsPos]))
		{
			$this->el[$this->optionsPos] =
				array_merge($this->options, $element[$this->optionsPos]);
		}

		return $this->el;
	}
   
	/******************************************/
	/* Public Methods - ArrayAccess Interface */
	/******************************************/

	/// Provide the array isset operator.
	public function offsetExists($offset)
	{
		return isset($this->el[$offset]);
	}

	/// Provide the array access operator.
	public function offsetGet($offset)
	{
		return $this->el[$offset];
	}

	/** We are required to make these available to complete the interface,
	 *  but we don't want the element to change.
	 */
	public function offsetSet($offset, $value)
	{
		throw new \RuntimeException(
			__METHOD__ . ' should never be called - use the set method.');
	}

	/** We are required to make these available to complete the interface,
	 *  but we don't want the element to change.
	 */
	public function offsetUnset($offset)
	{
		throw new \RuntimeException(
			__METHOD__ . ' should never be called - use the set method.');
	}
}
// EOF