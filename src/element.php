<?php
/** Element - Provide an element container with array access suitable for
 *  an XML Writing Resource that takes the form:
 *  \verbatim
    array(0 => Tag,
          1 => Attribs,
	  2 => Options);
    \endverbatim
 */
class Element implements ArrayAccess
{
   protected $el;
   protected $offsets;
   
   public function __construct(Array $element)
   {
      $this->offsets = array('Tag'     => 0,
			     'Attribs' => 1,
			     'Options' => 2);
      
       if (!isset($element[$this->offsets['Tag']]))
      {
	 throw new InvalidArgumentException(__METHOD__ . ' needs tag');
      }

      if (!isset($element[$this->offsets['Attribs']]))
      {
	 $element[$this->offsets['Attribs']] = array();
      }

      $defaultOptions = array('Children' => array(),
			      'Finish'   => true,  
			      'Start'    => true,
			      'Text'     => NULL);
      
      if (isset($element[$this->offsets['Options']]) &&
	  is_array($element[$this->offsets['Options']]))
      {
	 $element[$this->offsets['Options']] = array_merge(
	    $defaultOptions, $element[$this->offsets['Options']]);
      }
      else
      {
	 $element[$this->offsets['Options']] = $defaultOptions;
      }

      $this->el = $element;
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
      throw new RuntimeException(
	 __METHOD__ . ' should never be called - our elements are private.');
   }

   /** We are required to make these available to complete the interface,
    *  but we don't want the element to change.
    */
   public function offsetUnset($offset)
   {
      throw new RuntimeException(
	 __METHOD__ . ' should never be called - our elements are private.');
   }
}
// EOF