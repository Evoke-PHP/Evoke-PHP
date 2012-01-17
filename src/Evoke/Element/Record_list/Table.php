<?php
namespace Evoke;

/// Element to display a list of records from a table.
class Element_Record_List_Table extends Element_Record_List
{ 
   public function __construct(Array $setup)
   {
      $setup +=	array('App'          => NULL,
		      'Data'         => NULL,
		      'Table_Info'   => NULL);

      /// \todo Remove dependency on App.
      $setup['App']->needs(
	 array('Instance' => array('Table_Info' => $setup['Table_Info'])));

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