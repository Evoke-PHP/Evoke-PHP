<?php
namespace Evoke\View;

class Ajax extends Base
{ 
   /******************/
   /* Public Methods */
   /******************/

   public function write($data)
   {
      if (isset($data['AJAX_Data']))
      {
	 echo json_encode($data['AJAX_Data']);
      }
      else
      {
	 echo json_encode($data);
      }
   }
}
// EOF