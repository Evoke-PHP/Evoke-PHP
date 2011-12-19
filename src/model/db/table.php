<?php


/// Model_DB_Table - Provide a read only model to a table of data.
class Model_DB_Table extends Model_DB
{
   public function __construct($setup=array())
   {
      $setup += array('App'          => NULL,
		      'Select_Setup' => array(
			 'Fields'     => '*',
			 'Conditions' => '',
			 'Order'      => '',
			 'Limit'      => 0),
		      'Table_Name'   => NULL);

      parent::__construct($setup);

      $this->app->needs(
	 array('Set' => array('Table_Name' => $this->setup['Table_Name'])));
  }

   /******************/
   /* Public Methods */
   /******************/

   public function getData($selectSetup=array())
   {
      parent::getData();

      $selectSetup = array_merge($this->setup['Select_Setup'],
				 $selectSetup);
      
      $results = $this->sql->select($this->setup['Table_Name'],
				    $selectSetup['Fields'],
				    $selectSetup['Conditions'],
				    $selectSetup['Order'],
				    $selectSetup['Limit']);

      return $this->offsetData($results);
   }
}

// EOF