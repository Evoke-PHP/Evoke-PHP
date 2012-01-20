<?php
namespace Evoke\Model\DB;
/// Provide a read only model to a table of data.
class Table extends Base
{
   public function __construct($setup=array())
   {
      $setup += array('Select_Setup' => array(
			 'Fields'     => '*',
			 'Conditions' => '',
			 'Order'      => '',
			 'Limit'      => 0),
		      'Table_Name'   => NULL);

      parent::__construct($setup);

      if (!is_string($this->setup['Table_Name']))
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Table_Name as string');
      }
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