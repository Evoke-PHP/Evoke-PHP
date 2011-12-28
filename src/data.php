<?php

/// Data class to allow arrangement of data within processing.
abstract class Data implements Iterator
{
   protected $data;
   protected $setup;
   
   public function __construct(Array $setup=array())
   {
      $this->setup = array_merge(array('Joint_Key' => 'Joint_Data'),
				 $setup);
   }
   
   /******************/
   /* Public Methods */
   /******************/

   /// Set the data which is used when iterating over the object.
   public function setData($data)
   {
      $this->data = $data;
   }
   
   /***********************/
   /* Implements Iterator */
   /***********************/

   public function current()
   {
      return current($this->data);
   }
   
   public function key()
   {
      return key($this->data);
   }

   // Return the next item, skipping Joint Data.
   public function next()
   {
      $nextItem = next($this->data);

      // If we have run out of items or the next one is not the Joint Key then
      // we have found the next record.
      if (current($this->data) === false ||
	  key($this->data) !== $this->setup['Joint_Key'])
      {
	 return $nextItem;
      }

      // Otherwise get the next one as this one didn't count.
      return $this->next();
   }

   public function rewind()
   {
      reset($this->data);
   }
   
   public function valid()
   {
      return (current($this->data) !== false);
   }
}

// EOF