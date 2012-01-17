<?php
namespace Evoke;

class Processing_Get extends Processing
{ 
   public function __construct(Array $setup)
   {
      $setup += array('Event_Prefix'   => 'Get.',
		      'Request_Method' => 'GET');
      
      parent::__construct($setup);
   }

   /******************/
   /* Public Methods */
   /******************/

   public function getRequest()
   {
      $getRequest = $_GET;

      /// \todo Deal with the language from the get request properly.
      unset($getRequest['l']);
      
      if (empty($getRequest))
      {
	 return array('' => '');
      }

      return $getRequest;
   }
}
// EOF