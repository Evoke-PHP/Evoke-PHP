<?php
namespace Evoke\Element\Admin;

class Tables extends \Evoke\Element\Base
{ 
	public function __construct(Array $setup)
	{
		$setup += array('Tables'     => NULL,
		                'Translator' => NULL);

		if (!isset($setup['Tables']))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' needs Tables');
		}

		if (!$setup['Translator'] instanceof \Evoke\Core\Translator)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' needs Translator');
		}
      
		$adminTableEntries = array();

		foreach($setup['Tables'] as $name)
		{
			$button = array(
				array('a',
				      array('class' => 'Table_Button',
				            'href' => '/admin/' . strtolower($name) . '.php?' .
				            $setup['Translator']->getLanguageHTTPQuery()),
				      $setup['Translator']->get('Table_' . $name, __FILE__))));
	 
			$description = array(
				array('div',
				      array('class' => 'Table_Description'),
				      $setup['Translator']->get(
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