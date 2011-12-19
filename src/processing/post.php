<?php


class Processing_Post extends Processing
{ 
   public function __construct(Array $setup)
   {
      $setup += array('Event_Prefix'   => 'Post.',
		      'Request_Method' => 'POST');
      
      parent::__construct($setup);
   }

   /******************/
   /* Public Methods */
   /******************/

   public function getRequest()
   {
      if (empty($_POST))
      {
	 return array('' => '');
      }

      return $_POST;
   }
}

// EOF