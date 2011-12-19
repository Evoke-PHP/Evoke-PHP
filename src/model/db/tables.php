<?php


/// Model_Tables - Get a list of tables from the database.
class Model_DB_Tables extends Model_DB
{
   public function __construct(Array $setup)
   {
      $setup += array('Extra_Tables'   => array(),
		      'Ignored_Tables' => array());
      
      parent::__construct($setup);
   }

   /******************/
   /* Public Methods */
   /******************/
      
   /** Get the list of tables in the database.
    *  \returns \bool False for failure or an \array of tables.
    */
   public function getData()
   {
      $tables = array();

      try
      {
	 $tableResults = $this->sql->getAssoc('SHOW TABLES');
      }
      catch (Exception $e)
      {
	 $this->em->notify(
	    'Log',
	    array('Level'   => LOG_ERR,
		  'Method'  => __METHOD__,
		  'Message' => 'Unable to get tables in database due to ' .
		  'exception: ' . $e->getMessage()));

	 return array();
      }
      
      foreach ($tableResults as $result)
      {
	 foreach($result as $tableName)
	 {
	    $tables[$tableName] = $tableName;
	 }
      }

      $tables = array_merge($tables, $this->setup['Extra_Tables']);
      $tables = array_diff($tables, $this->setup['Ignored_Tables']);
      ksort($tables);

      return $this->offsetData($tables);
   }
}

// EOF