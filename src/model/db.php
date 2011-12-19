<?php


/// Model_DB provides the basic implementation for a database model.
abstract class Model_DB extends Model
{ 
   protected $sql;

   public function __construct(Array $setup)
   {
      $setup += array('SQL' => NULL);
      parent::__construct($setup);

      $this->app->needs(
	 array('Instance' => array('Iface_DB' => $this->setup['SQL'])));
      $this->sql =& $this->setup['SQL'];
   }
}

// EOF