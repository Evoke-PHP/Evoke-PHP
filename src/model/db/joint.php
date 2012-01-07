<?php
/// Model_DB_Joint - Provide a read only model for joint table data.
class Model_DB_Joint extends Model_DB
{
   public function __construct(Array $setup)
   {
      $setup += array('Select_Setup'     => array('Conditions' => '',
						  'Fields'     => '*',
						  'Order'      => '',
						  'Limit'      => 0),
		      'Table_Name'       => NULL,
		      'Table_References' => NULL);

      parent::__construct($setup);

      $this->app->needs(
	 array('Instance' => array(
		  'Table_References' => $this->setup['Table_References']),
	       'Set' => array('Table_Name' => $this->setup['Table_Name'])));
   }

   /******************/
   /* Public Methods */
   /******************/

   /// Get the data for the model.   
   public function getData($getSetup=array())
   {
      
      $getSetup = array_merge($this->setup['Select_Setup'], $getSetup);

      $tables = $this->setup['Table_Name'] .
	 $this->setup['Table_References']->getJoins();

      if ($getSetup['Fields'] === '*')
      {
	 $getSetup['Fields'] =
	    $this->setup['Table_References']->getAllFields();
      }

      $results = $this->sql->select($tables,
				    $getSetup['Fields'],
				    $getSetup['Conditions'],
				    $getSetup['Order'],
				    $getSetup['Limit']);

      return array_merge(
	 parent::getData(),
	 $this->offsetData(
	    $this->setup['Table_References']->arrangeResults($results)));
   }
}

// EOF