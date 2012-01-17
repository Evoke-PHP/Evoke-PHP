<?php
namespace Evoke;

class Data_Products extends Data_List
{
   protected $currency;
   protected $product;
   
   public function __construct(Array $setup=array())
   {
      $setup += array('List_Item' => NULL);

      if (!$setup['List_Item'] instanceof Data_Product)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' needs List_Item as Data_Product');
      }

      parent::__construct($setup);

      $this->product =& $this->listItem;
   }
   
   /******************/
   /* Public Methods */
   /******************/

   public function addProduct($productRecord)
   {
      $this->data[$productRecord['ID']] = $productRecord;
   }

   public function removeProduct($productID)
   {
      unset($this->data[$productID]);
   }
   
   public function setCurrency($currency)
   {
      $this->product->priceList->setCurrency($currency);
      $this->currency = $currency;
   }

   // Index the product records by product ID.
   public function setData($data)
   {
      $this->data = array();
      
      foreach ($data as $record)
      {
	 $this->data[$record['ID']] = $record;
      }
   }
   
   /*********************/
   /* Protected Methods */
   /*********************/

   protected function setListItem($data)
   {
      $this->product->setData($data);
      $this->product->priceList->setCurrency($this->currency);
   }

   /***********************/
   /* Implements Iterator */
   /***********************/
   /*
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
   */
}
// EOF