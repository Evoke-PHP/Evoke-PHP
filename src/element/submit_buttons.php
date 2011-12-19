<?php


/// Element_Submit_Buttons
class Element_Submit_Buttons extends Element
{  
   public function __construct($buttons, $setup=array())
   {
      $setup = array_merge(array('Container_Attribs' => array(),
				 'Prefix' => '',
				 'Translator' => NULL,
				 'Translator_Prefix' => 'Button'),
			   $setup);
      
      $setup['Prefix'] = rtrim($setup['Prefix'], '_');
      $setup['Translator_Prefix'] = rtrim($setup['Translator_Prefix'], '_');

      if (!$setup['Translator'] instanceof Translator)
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' needs Translator');
      }
      
      $buttonAttribs = array('type' => 'submit',
			     'class' => $setup['Prefix']);
      $buttonElems = array();

      foreach ($buttons as $button)
      {
	 $buttonElems[] = array(
	    'input',
	    array_merge(
	       $buttonAttribs,
	       array('id' =>  $setup['Prefix'] . '_' . $button,
		     'name' => $button,
		     'value' => $setup['Translator']->get(
			$setup['Translator_Prefix'] . '_' . $button))));
      }

      parent::__construct(array('div',
				$setup['Container_Attribs'],
				array('Children' => $buttonElems)));
   }
}

// EOF