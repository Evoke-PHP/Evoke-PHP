<?php
namespace Evoke\Element\Admin;

class Tables extends \Evoke\Element\Admin
{ 
	public function __construct(Array $setup)
	{
		$setup += array('Tables'     => NULL,
		                'Translator' => NULL);

		if (!isset($tables))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' needs Tables');
		}

		if (!$translator instanceof \Evoke\Core\Translator)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' needs Translator');
		}
      
		$adminTableEntries = array();

		foreach($tables as $name)
		{
			$button = array(
				array('a',
				      array('class' => 'Table_Button',
				            'href' => '/admin/' . strtolower($name) . '.php?' .
				            $translator->getLanguageHTTPQuery()),
				      $translator->get('Table_' . $name, __FILE__))));
	 
			$description = array(
				array('div',
				      array('class' => 'Table_Description'),
				      $translator->get(
					      'Table_' . $name . '_Description', __FILE__)));
   
			$adminTableEntries[] =
				array('div',
				      array('class' => 'Admin_Table_Entry'),
				      array(array('div',
				                  array('class' => 'Table_Button_Div'),
				                  $button),
				            array('div',
				                  array('class' => 'Table_Description_Div'),
				                  $description)));
		}
      
		parent::__construct(array('div',
		                          array('class' => 'Admin_Tables'),
		                          $adminTableEntries));
	}
}
// EOF