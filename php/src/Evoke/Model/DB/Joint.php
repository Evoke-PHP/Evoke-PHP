<?php
namespace Evoke\Model\DB;
/// Provide a read only model for joint table data.
class Joint extends Base
{
   public function __construct(Array $setup)
   {
      $setup += array('Select_Setup'    => array('Conditions' => '',
						 'Fields'     => '*',
						 'Order'      => '',
						 'Limit'      => 0),
		      'Table_Name'      => NULL,
		      'TableReferences' => NULL);

      parent::__construct($setup);

      if (!$this->setup['TableReferences'] instanceof
	  \Evoke\Core\DB\Table\References)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires TableReferences');
      }

      if (!isset($this->setup['Table_Name']))
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Table_Name');
      }
   }

   /******************/
   /* Public Methods */
   /******************/

   /// Get the data for the model.   
   public function getData($getSetup=array())
   {
      
      $getSetup = array_merge($this->setup['Select_Setup'], $getSetup);

      $tables = $this->setup['Table_Name'] .
	 $this->setup['TableReferences']->getJoins();

      if ($getSetup['Fields'] === '*')
      {
	 $getSetup['Fields'] =
	    $this->setup['TableReferences']->getAllFields();
      }

      $results = $this->sql->select($tables,
				    $getSetup['Fields'],
				    $getSetup['Conditions'],
				    $getSetup['Order'],
				    $getSetup['Limit']);

      return array_merge(
	 parent::getData(),
	 $this->offsetData(
	    $this->setup['TableReferences']->arrangeResults($results)));
   }
}
// EOF