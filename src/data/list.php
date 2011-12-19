<?php


abstract class Data_List extends Data
{
   protected $listItem;
   
   public function __construct(Array $setup)
   {
      $setup += array('List_Item' => NULL);

      if (!isset($setup['List_Item']))
      {
	 throw new InvalidArgumentException(__METHOD__ . ' requires List_Item');
      }

      $this->listItem = $setup['List_Item'];
   }

   /*********************/
   /* Protected Methods */
   /*********************/

   abstract protected function setListItem($data);
   
   /***********************/
   /* Implements Iterator */
   /***********************/

   // Return the current product.
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
      $this->setListItem(reset($this->data));
   }

   public function valid()
   {
      return (current($this->data) !== false);
   }   
}

// EOF