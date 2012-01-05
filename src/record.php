<?php
/** Provide access to a record (possibly containing joint data).
 */
class Record implements Iterator
{
   protected $recInternal;
   protected $jointKey;
   protected $references;
   
   public function __construct(Array $setup=array())
   {
      $setup += array('Joint_Key'  => 'Joint_Data',
		      'References' => NULL);

      if (!isset($setup['References']))
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' requires References');
      }

      $this->jointKey = $setup['Joint_Key'];
      $this->references = array();

      foreach ($setup['References'] as $parentField => $dataContainer)
      {
	 if (!$dataContainer instanceof Data)
	 {
	    throw new InvalidArgumentException(
	       __METHOD__ . ' requires Data Container for parent field: ' .
	       $parentField);
	 }

	 $this->references[$parentField] = $dataContainer;
      }
   }

   /******************/
   /* Public Methods */
   /******************/

   /** Provide access to the referenced data.  This allows the object to be used
    *  like so:  $object->referencedData (for joint data with a parent field of
    *  'Referenced_Data').
    *  @param referenceName \string The parent field for the referenced data.
    *  This can be as per the return value of \ref getReferenceName.
    */
   public function __get($referenceName)
   {
      if (isset($this->references[$referenceName]))
      {
	 return $this->references[$referenceName];
      }
      
      foreach ($this->references as $parentField => $dataContainer)
      {
	 if ($referenceName === $this->getReferenceName($parentField))
	 {
	    return $dataContainer;
	 }
      }
      
      throw new OutOfBoundsException(
	 __METHOD__ . ' record does not refer to: ' .
	 var_export($referenceName, true));
   }

   /** Set the record that we are managing.
    */
   public function setRecord($record)
   {
      $record += array($this->jointKey => array());

      foreach ($this->references as $parentField => $dataContainer)
      {
	 $record[$this->jointKey] += array($parentField => array());
	 $dataContainer->setData($record[$this->jointKey][$parentField]);
      }

      unset($record[$this->jointKey]);
      $this->recInternal = $record;      
   }   
   
   /***********************/
   /* Implements Iterator */
   /***********************/

   public function current()
   {
      return current($this->recInternal);
   }
   
   public function key()
   {
      return key($this->recInternal);
   }

   public function next()
   {
      $nextItem = next($this->recInternal);

      if ($nextItem === false)
      {
	 return false;
      }

      return $nextItem;
   }

   public function rewind()
   {
      reset($this->recInternal);
   }
   
   public function valid()
   {
      return (current($this->recInternal) !== false);
   }
   
   /*******************/
   /* Private Methods */
   /*******************/

   /** Get the reference name that will be used for accessing the joint data
    *  from this object.  It should match our standard naming of properties
    *  (camel case) and not contain the final ID which is not needed.
    *  @param parentField \string The parent field for the joint data.
    *  \return \string The reference name.
    */
   private function getReferenceName($parentField)
   {
      $nameParts = mb_split('_', $parentField);
      $lastPart = end($nameParts);

      // Remove any final id.
      if (mb_strtolower($lastPart) === 'id')
      {
	 array_pop($nameParts);
      }

      $name = '';

      foreach ($nameParts as $part)
      {
	 $name .= $part;
      }
      
      $name[0] = mb_strtolower($name[0]);
      
      return $name;
   }
}
// EOF