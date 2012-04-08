<?php
namespace Evoke\Element;
/** An XML element.Provide an element container with array access suitable for
 *  a writer:
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

	/** @property $el
	 *  The element data.
	 */
	protected $el;
	
	/** @property $pos
	 *  \array The position of the tag, attribs and children in the element.
	 */
	protected $pos;
   
	public function __construct(Array $setup=array())
	{
		$setup += array('Attribs' => array(),
		                'Pos'     => array('Attribs'  => 1,
		                                   'Children' => 2,
		                                   'Tag'      => 0));

		$this->el = array();

		$this->attribs = $setup['Attribs'];
		$this->pos     = $setup['Pos'];
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Add a class to the element.
	 *  @param c \string The class to be added to the element.
	 */
	public function addClass($c)
	{
		$attribsPos = $this->pos['Attribs'];
		
		if (isset($this->el[$attribsPos]['class']))
		{
			$this->el[$attribsPos]['class'] = preg_replace(
				'/(^' . $c . '\s?|\s?' . $c . '\s?|\s?' . $c . '$)/',
				'',
				$this->el[$attribsPos]['class']) . ' ' . $c;
		}
		else
		{
			if (!isset($this->el[$attribsPos]))
			{
				$this->el[$attribsPos] = array();
			}
	 
			$this->el[$attribsPos]['class'] = $c;
		}
	}

	/** Add a class to the element.
	 *  @param attrib \string The attribute to be appended to.
	 *  @param value \string The value to be appended to the attribute.
	 */
	public function appendAttrib($attrib, $value)
	{
		$attribsPos = $this->pos['Attribs'];
		
		if (isset($this->el[$attribsPos][$attrib]))
		{
			$this->el[$attribsPos][$attrib] .= $value;
		}
		else
		{
			if (!isset($this->el[$attribsPos]))
			{
				$this->el[$attribsPos] = array();
			}
	 
			$this->el[$attribsPos][$attrib] = $value;
		}
	}

	/** Set the element from the array values passed in for the element.  The
	 *  element should be passed in as an array with the numerical keys
	 *  corresponding to the desired element data.  By default this is:
	 *  \verbatim
	 *  0 => Tag
	 *  1 => Attribs
	 *  2 => Children
	 *  \endverbatim
	 *
	 *  A Tag is required to be passed in as a string.
	 *
	 *  Atrribs passed in are merged with the default attributes from the
	 *  construction.
	 *
	 *  Children are optional and must be passed in as an array.  Strings should
	 *  be used for text.  Array elements can provide nested Elements.
	 *
	 *  Any data with a key outside of this range is ignored.
	 *
	 *  @param element \mixed The element data to set ourselves to must be array
	 *  accessible.
	 *  \return \array Return the data that has been set.
	 */
	public function set(Array $element)
	{
		$tagPos      = $this->pos['Tag'];
		$attribsPos  = $this->pos['Attribs'];
		$childrenPos = $this->pos['Children'];
		
		// Remove the need for isset calls and default the attribs and children
		// to empty arrays.
		$element += array($tagPos      => NULL,
		                  $attribsPos  => array(),
		                  $childrenPos => array());
		
		if (!is_string($element[$tagPos]))
		{
			throw new \DomainException(__METHOD__ . ' Tag must be a string.');
		}
		
		if (!is_array($element[$attribsPos]))
		{
			throw new \DomainException(
				__METHOD__ . ' if attribs are supplied they must be an array');
		}

		if (!is_array($element[$childrenPos]))
		{
			throw new \DomainException(
				__METHOD__ . ' if children are supplied they must be an array');
		}

		$this->el = array(
			$tagPos      => $element[$tagPos],
			$attribsPos  => array_merge($this->attribs, $element[$attribsPos]),
			$childrenPos => $element[$childrenPos]);

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