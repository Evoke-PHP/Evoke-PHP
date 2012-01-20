<?php
namespace Evoke\Element;
/** Element - Provide an element container with array access suitable for
 *  an XML Writing Resource that takes the form:
 *  \verbatim
    array(0 => Tag,
          1 => Attribs,
	  2 => Options);
    \endverbatim
 */
class Base implements \Evoke\Core\Iface\Element
{
   protected $el;
   protected $offsets;
   protected $setup;
   
   public function __construct(Array $setup=array())
   {
      $this->setup = array_merge(
	 array('Default_Attribs' => array(),
	       'Default_Options' => array('Children' => array(),
					  'Finish'   => true,
					  'Start'    => true,
					  'Text'     => NULL),
	       'Offsets'         => array('Attribs' => 1,
					  'Options' => 2,
					  'Tag'     => 0)),
	 $setup);

      $this->el = array();
      $this->offsets =& $this->setup['Offsets'];
   }

   /******************/
   /* Public Methods */
   /******************/

   /** Add a class to the element.
    *  @param c \string The class to be added to the element.
    */
   public function addClass($c)
   {
      if (isset($this->el[$this->offsets['Attribs']]['class']))
      {
	 $this->el[$this->offsets['Attribs']]['class'] = preg_replace(
	    '/(^' . $c . '\s?|\s?' . $c . '\s?|\s?' . $c . '$)/',
	    '',
	    $this->el[$this->offsets['Attribs']]['class']) . ' ' . $c;
      }
      else
      {
	 if (!isset($this->el[$this->offsets['Attribs']]))
	 {
	    $this->el[$this->offsets['Attribs']] = array();
	 }
	 
	 $this->el[$this->offsets['Attribs']]['class'] = $c;
      }
   }

   /** Add a class to the element.
    *  @param attrib \string The attribute to be appended to.
    *  @param value \string The value to be appended to the attribute.
    */
   public function appendAttrib($attrib, $value)
   {
      if (isset($this->el[$this->offsets['Attribs']][$attrib]))
      {
	 $this->el[$this->offsets['Attribs']][$attrib] .= $value;
      }
      else
      {
	 if (!isset($this->el[$this->offsets['Attribs']]))
	 {
	    $this->el[$this->offsets['Attribs']] = array();
	 }
	 
	 $this->el[$this->offsets['Attribs']][$attrib] = $value;
      }
   }

   /** Set the element from the array values passed in for the element.  The
    *  element should be passed in as an array with the numerical keys
    *  corresponding to the desired element data.  By default this is:
    *  \verbatim
       0 => Tag
       1 => Attribs
       2 => Options
       \endverbatim
    *
    *  Atrribs and Options data is merged with the Default values from the
    *  setup.
    *
    *  Any data that is not passed in is not set.
    *  Any data with a key outside of this range is ignored.
    *
    *  @param element \array The element data to set ourselves to.
    *  \return \array Return the data that has been set.
    */
   public function set(Array $element)
   {
      $this->el = array();
      
      if (isset($element[$this->offsets['Tag']]))
      {
	 $this->el[$this->offsets['Tag']] = $element[$this->offsets['Tag']];
      }
      
      if (isset($element[$this->offsets['Attribs']]))
      {
	 $this->el[$this->offsets['Attribs']] =
	    array_merge($this->setup['Default_Attribs'],
			$element[$this->offsets['Attribs']]);
      }

      if (isset($element[$this->offsets['Options']]))
      {
	 $this->el[$this->offsets['Options']] =
	    array_merge($this->setup['Default_Options'],
			$element[$this->offsets['Options']]);
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