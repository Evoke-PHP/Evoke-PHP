<?php


/// Data_Price_List.
class Data_Price_List extends Data
{
   protected $currency;

   /******************/
   /* Public Methods */
   /******************/
   
   /** Get the price at the specified quantity.
    *  @param qty \int Desired quantity of product.
    *  \return \string The price for each unit at the qty.
    */
   public function getPricePerUnit($qty=1)
   {
      foreach ($this->data as $priceEntry)
      {
	 // If the quantity is sufficient to obtain the lower pricing of the
	 // higher volume then update the selected price.
	 if ($qty >= $priceEntry['Min'])
	 {
	    $price = $priceEntry;
	 }
      }

      if (!isset($price))
      {
	 throw new RuntimeException(
	    __METHOD__ . ' No price found at qty: ' . $qty);
      }
      
      return $price;
   }

   /// Get the price per unit at the minimum saleable quantity of the product.
   public function getPricePerUnitAtMinQty()
   {
      if (empty($this->data))
      {
	 throw new RuntimeException(
	    __METHOD__ . ' should not be called unless the product isPriced');
      }
      
      // Set the result to the first entry so that the loop is clean and easy.
      $firstResult = reset($this->data);
      $min = $firstResult['Min'];
      $price = $firstResult['Price'];

      // We have already processed the first entry so we can loop with next.
      while ($priceEntry = next($this->data))
      {
	 if ($priceEntry['Min'] < $min)
	 {
	    $min = $priceEntry['Min'];
	    $price = $priceEntry['Price'];
	 }
      }

      return $this->getPriceInCurrency($price);
   }

   /// Return whether the product is priced or not.
   public function isPriced()
   {
      return !empty($this->data);
   }

   /// Set the currency for the prices.
   public function setCurrency($currency)
   {
      $this->currency = $currency;
   }

   /*********************/
   /* Protected Methods */
   /*********************/

   /** Get the price in the current currency.
    *  @param price \float The price to convert using the currency.
    */
   protected function getPriceInCurrency($price)
   {
      // Round the number to the nearest specified.
      $priceInCurrency = $price / $this->currency['Rate'];
      $remainder = fmod($priceInCurrency, $this->currency['Rounding']);

      if ($remainder > ($this->currency['Rounding'] / 2))
      {
	 $priceInCurrency += $this->currency['Rounding'];
      }

      return $priceInCurrency - $remainder;
   }
}

// EOF