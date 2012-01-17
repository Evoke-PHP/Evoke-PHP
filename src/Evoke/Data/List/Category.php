<?php
namespace Evoke;

class Data_Category_List extends Data
{ 
   /******************/
   /* Public Methods */
   /******************/

   // No rearrangement.
   public function arrangeRecord($record)
   {
      return $record;
   }
}
// EOF