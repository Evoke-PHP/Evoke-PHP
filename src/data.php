<?php
/** Data Transfer Object.
 *  Provide access to data via records (\ref Record) and allow methods to be
 *  added to provide aggregated details of the data.
 */
class Data implements Iterator
{
   protected $data;
   protected $record;
   protected $setup;
   
   public function __construct(Array $setup)
   {
      $this->setup = array_merge(array('Record' => NULL), $setup);

      if (!$this->setup['Record'] instanceof Record)
      {
	 throw new InvalidArgumentException(__METHOD__ . ' requires Record');
      }

      $this->record =& $this->setup['Record'];
   }
   
   /******************/
   /* Public Methods */
   /******************/
      
   /// Set the data which is used when iterating over the object.
   public function setData($data)
   {
      $first = reset($data);

      if (!is_array($first))
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' invalid data: ' . var_export($data, true));
      }

      $this->record->setRecord($first);
      $this->data = $data;
   }
   
   /***********************/
   /* Implements Iterator */
   /***********************/

   public function current()
   {
      return $this->record;
   }
   
   public function key()
   {
      return key($this->data);
   }

   public function next()
   {
      $nextItem = next($this->data);

      if ($nextItem === false)
      {
	 return false;
      }

      $this->record->setRecord($nextItem);
      return $this->record;
   }

   public function rewind()
   {
      $first = reset($this->data);

      if ($first !== false)
      {
	 $this->record->setRecord($first);
      }
   }
   
   public function valid()
   {
      return (current($this->data) !== false);
   }
}
// EOF