<?php

class Data_List extends Data
{
   protected $listItem;
   
   public function __construct(Array $setup)
   {
      $setup += array('App'          => NULL,
		      'List_Item'    => NULL,
		      'Parent_Field' => NULL);

      $setup['App']->needs(
	 array('Instance' => array('Data' => $setup['List_Item']),
	       'Set'      => array('Parent_Field' => $setup['Parent_Field'])));

      $this->listItem = $setup['List_Item'];
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

   protected function setListItem($data)
   {
      $jKey = $this->setup['Joint_Key'];
      $data += array($jKey);
      $data[$jKey] += array($this->setup['Parent_Field'] => array());
      
      $this->listItem->setData($data[$jKey][$this->setup['Parent_Field']]);
   }
   
   /***********************/
   /* Implements Iterator */
   /***********************/

   // Return the current item.
   public function current()
   {
      return $this->listItem;
   }

   public function key()
   {
      Return $this->listItem->getID();
   }
   
   // Adjust the next function to move between list items.
   public function next()
   {
      $nextItem = next($this->data);
      
      if ($nextItem === false)
      {
	 return false;
      }

      $this->setListItem($nextItem);
      
      return $this->listItem;
   }

   public function rewind()
   {
      $first = reset($this->data);

      if ($first !== false)
      {
	 $this->setListItem($first);
      }
   }

   public function valid()
   {
      return (current($this->data) !== false);
   }   
}

// EOF