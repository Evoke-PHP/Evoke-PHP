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
   
	public function __construct(Array $attribs=array(),
	                            Array $pos=array())
	{
		$this->attribs = $attribs;
		$this->el      = array();
		$this->pos     = array_merge($pos,
		                             array('Attribs'  => 1,
		                                   'Children' => 2,
		                                   'Tag'      => 0));
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

	/** Set the element from the array values passed in for the element and
	 *  return the representation in a plain array.  A value must be returned so
	 *  that the Element object can be re-used to build more Elements.
	 *
	 *  This allows it to be used in a template like this:
	 *  \code
	 *  array('div',
	 *        array('class' => 'example'),
	 *        array($element->set($val1),
	 *              $element->set($val2)));
	 *  \endcode
	 *
	 *  The Element object was set twice, if it was set before the code val2
	 *  would appear twice, because the object would have been modified.
	 *
	 *  @param element \array The element that we are setting ourselves to.
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
	 *  Children are optional.  A single text element child can be passed as a
	 *  string.  All other children must be passed using an array.  Within the
	 *  array Strings are used for text elements.  All other items in the array
	 *  must be array accessible (such as an Element object or plain array).
	 *
	 *  Any data with a key outside of this range is ignored.
	 *
	 *  @param element \mixed The element data to set ourselves to must be array
	 *  accessible.
	 *  \return \array Return the data that has been set.
	 */
	public function set(Array $element)
	{
		// Remove the need for isset calls and default the attribs and children
		// to empty arrays.
		$element += array($this->pos['Tag']      => NULL,
		                  $this->pos['Attribs']  => array(),
		                  $this->pos['Children'] => array());
		
		if (!is_string($element[$this->pos['Tag']]))
		{
			throw new \DomainException(__METHOD__ . ' Tag must be a string.');
		}
		
		if (!is_array($element[$this->pos['Attribs']]))
		{
			throw new \DomainException(
				__METHOD__ . ' if attribs are supplied they must be an array');
		}

		if (is_string($element[$this->pos['Children']]))
		{
			$element[$this->pos['Children']] =
				array($element[$this->pos['Children']]);
		}
		elseif (!is_array($element[$this->pos['Children']]))
		{
			throw new \DomainException(
				__METHOD__ . ' children must be supplied as an array or ' .
				'string (for a single child)');
		}

		$this->el = array(
			$this->pos['Tag']      => $element[$this->pos['Tag']],
			$this->pos['Attribs']  => array_merge(
				$this->attribs, $element[$this->pos['Attribs']]),
			$this->pos['Children'] => $element[$this->pos['Children']]);

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