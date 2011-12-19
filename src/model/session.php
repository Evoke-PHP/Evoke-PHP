<?php


class Model_Session extends Model
{ 
   public function __construct(Array $setup)
   {
      $setup += array('Session_Manager' => NULL);
      
      parent::__construct($setup);

      $this->app->needs(
	 array('Instance' => array(
		  'Session_Manager' => $this->setup['Session_Manager'])));
   }

   /******************/
   /* Public Methods */
   /******************/

   // Get the data from the session.
   public function getData()
   {
      $session = $this->setup['Session_Manager']->getAccess();

      if (!is_array($session))
      {
	 return $this->offsetData(parent::getData());
      }
      
      return $this->offsetData(array_merge(parent::getData(), $session));
   }
}

// EOF