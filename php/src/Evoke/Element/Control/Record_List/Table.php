<?php
namespace Evoke\Element\Control\Record_List;

/// Element to display a list of records from a table.
class Table extends \Base
{ 
   public function __construct(Array $setup)
   {
      $setup +=	array('Data'         => NULL,
		      'TableInfo'   => NULL);

      if (!$this->setup['TableInfo'] instanceof \Evoke\Core\DB\Table\Info)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires TableInfo');
      }

      // Set specific fields in the setup for a table.
      $setup['Fields'] = $setup['Table_Info']->getFields();
      $setup['Primary_Keys'] = $setup['Table_Info']->getPrimaryKeys();
      $setup['Table_Name'] = $setup['Table_Info']->getTableName();

      if (!isset($setup['Attribs']))
      {
	 $setup['Attribs'] =
	    array('class' => 'Record_List ' . $setup['Table_Name']);
      }
  
      parent::__construct($setup);
   }
}
// EOF