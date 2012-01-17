<?php
namespace Evoke;

class Data_Size_List extends Data
{ 
   /******************/
   /* Public Methods */
   /******************/

   /** Get the size record from the enhanced size list.
    *  @param sizeID \string Size_ID of the record to get.
    *  \return \array The size record that was found.
    */
   public function getSize($sizeID)
   {
      foreach ($this->data as $record)
      {
	 if ($record['Size_ID'] === $sizeID)
	 {
	    return $record[$this->setup['Joint_Key']]['Size_ID'];
	 }
      }

      // We should have already returned.
      throw new \RuntimeException(__METHOD__ . ' no size found for sizeID: '  .
				 var_export($sizeID, true));
   }

   public function setData($data)
   {
      
   }
   
   /***********************/
   /* Implements Iterator */
   /***********************/

   // Return the current product.
   public function current()
   {
      return $this->product;
   }

   public function key()
   {
      Return $this->product->getID();
   }
   
   // Adjust the next function to move between products.
   public function next()
   {
      $nextItem = next($this->data);
      
      if ($nextItem === false)
      {
	 return false;
      }

      $this->product->setData($nextItem);
      $this->product->priceList->setCurrency($this->currency);
      
      return $this->product;
   }

   public function rewind()
   {
      $this->product->setData(reset($this->data));
      $this->product->priceList->setCurrency($this->currency);
   }

   public function valid()
   {
      return (current($this->data) !== false);
   }

}
// EOF