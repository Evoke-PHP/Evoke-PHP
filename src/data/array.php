<?php

class Data_Array extends Data
{ 
   protected $arrayItem;
   
   public function __construct(Array $setup)
   {
      $setup += array('App'          => NULL,
		      'Array_Item'   => NULL);

      $setup['App']->needs(
	 array('Instance' => array('Data' => $setup['Array_Item'])));

      $this->arrayItem =& $setup['Array_Item'];
   }

   /******************/
   /* Public Methods */
   /******************/

   public function setData($data)
   {
      $this->data = $data;
   }

   /*********************/
   /* Protected Methods */
   /*********************/

   protected function setArrayItem($data)
   {
      $this->arrayItem->setData($data);
   }
   
   /***********************/
   /* Implements Iterator */
   /***********************/

   // Return the current item.
   public function current()
   {
      return $this->arrayItem;
   }

   public function key()
   {
      Return $this->arrayItem->getID();
   }
   
   // Adjust the next function to move between list items.
   public function next()
   {
      $nextItem = next($this->data);
      
      if ($nextItem === false)
      {
	 return false;
      }

      $this->setArrayItem($nextItem);
      
      return $this->arrayItem;
   }

   public function rewind()
   {
      $first = reset($this->data);

      if ($first !== false)
      {
	 $this->setArrayItem($first);
      }
   }

   public function valid()
   {
      return (current($this->data) !== false);
   }
}

// EOF