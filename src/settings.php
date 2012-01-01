<?php
class Settings implements ArrayAccess
{
   protected $frozen;
   protected $separator;
   protected $variable;
   
   public function __construct(Array $setup)
   {
      $setup += array('Frozen'    => array(),
		      'Separator' => NULL,
		      'Variable'  => array());

      if (!is_string($setup['Separator']))
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' requires Separator as a string');
      }
      
      $this->frozen = $setup['Frozen'];
      $this->separator = $setup['Separator'];
      $this->variable = $setup['Variable'];
   }

   /******************/
   /* Public Methods */
   /******************/

   public function freeze($offset)
   {
      $variableReference = $this->getVariableReference($offset);
      $frozenReference = $this->getFrozenReference($offset);
      
      if ($variableReference !== NULL)
      {
	 $frozenReference = $variableReference;
	 $variableReference = NULL;
      }
      elseif (!isset($this->frozenReference))
      {
	 throw new OutOfRangeException(
	    __METHOD__ . ' offset: ' . var_export($offset, true) .
	    ' does not exist to be frozen.');
      }
   }
   
   public function freezeAll()
   {
      $this->frozen = array_merge_recursive($this->frozen, $this->variable);
      $this->variable = array();
   }

   public function get($offset)
   {
      return $this->offsetGet($offset);
   }

   public function isFrozen($offset)
   {
      $frozenValue = $this->getFrozenReference($offset);
      return isset($frozenValue);
   }
   
   public function set($offset, $value)
   {
      $this->offsetSet($offset, $value);
   }

   public function unfreeze($offset)
   {
      if (!$this->offsetSet($offset))
      {
	 throw new OutOfBoundsException(
	    __METHOD__ . ' offset: ' . var_export($offset, true) .
	    ' does not exist to be unfrozen.');
      }

      $this->offsetUnset($offset);
   }

   public function unfreezeAll()
   {
      $this->variable = array_merge_recursive($this->frozen, $this->variable);
      $this->frozen = array();
   }

   /******************************************/
   /* Public Methods - ArrayAccess interface */
   /******************************************/

   /** Check whether the offset exists in either the frozen or variable
    *  settings.
    *  @param offset \mixed String or array specifying the offset.
    */
   public function offsetExists($offset)
   {
      $frozenValue = $this->getFrozenReference($offset);
      $variableValue = $this->getVariableReference($offset);

      return isset($frozenValue) || isset($variableValue);
   }

   /** Get the value at the offset.
    *  @param offset \mixed String or Array specifying the offset.
    */
   public function offsetGet($offset)
   {
      $frozenValue = $this->getFrozenReference($offset);

      if (isset($frozenValue))
      {
	 return $frozenValue;
      }
      
      $variableValue = $this->getVariableReference($offset);

      if (isset($variableValue))
      {
	 return $variableValue;
      }
      
      throw new RuntimeException(
	 __METHOD__ . ' offset: ' . var_export($offset, true) . ' not set.');
   }

   public function offsetSet($offset, $value)
   {
      $frozenValue = $this->getFrozenReference($offset);
      
      if (isset($frozenValue))
      {
	 throw new RuntimeException(
	    __METHOD__ . ' offset: ' . var_export($offset, true) .
	    ' is already frozen and cannot be set.');
      }

      if (!is_array($offset))
      {
	 $offset = explode($this->separator, $offset);
      }
      
      $last = array_pop($offset);
      $variableRef =& $this->getVariableReference($offset);
      $variableRef[$last] = $value;
   }
   
   public function offsetUnset($offset)
   {
      $offset = explode($this->separator, $offset);
      $last = array_pop($offset);

      $frozenRef = $this->getFrozenReference($offset);
      unset($frozenRef[$last]);
      
      $variableRef = $this->getVariableReference($offset);
      unset($variableRef[$last]);
   }
   
   /*******************/
   /* Private Methods */
   /*******************/

   private function &getFrozenReference($offset)
   {
      if (!is_array($offset))
      {
	 $offset = explode($this->separator, $offset);
      }
      
      $reference =& $this->frozen;
      
      foreach ($offset as $part)
      {
	 if (!isset($reference[$part]))
	 {
	    $noReference = NULL;
	    return $noReference;
	 }
	 
	 $reference =& $reference[$part];
      }

      return $reference;
   }

   private function &getVariableReference($offset)
   {
      if (!is_array($offset))
      {
	 $offset = explode($this->separator, $offset);
      }
      
      $reference =& $this->variable;
      
      foreach ($offset as $part)
      {
	 if (!isset($reference[$part]))
	 {
	    $noReference = NULL;
	    return $noReference;
	 }
	 
	 $reference =& $reference[$part];
      }

      return $reference;
   }
}
// EOF
