<?php

require_once 'data.php';

/// Data_Category_List
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