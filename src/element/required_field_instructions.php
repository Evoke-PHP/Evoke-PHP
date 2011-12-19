<?php


/// Element_Required_Field_Instructions
class Element_Required_Field_Instructions extends Element
{ 
   public function __construct($setup=array())
   {
      $setup = array_merge(
	 array('Translator' => NULL),
	 $setup);

      if (!$setup['Translator'] instanceof Translator)
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' needs Translator');
      }
      
      parent::__construct(
	 array(
	    'div',
	    array('class' => 'Required_Field_Instructions'),
	    array('Children' => array(
		     array('span',
			   array('class' => 'Required_Field_Instructions_Text'),
			   array('Text' => $setup['Translator']->get(
				    'Required_Field_Instructions'))),
		     array('span',
			   array('class' => 'Required'),
			   array('Text' => '*')))));
		     
   }
}

// EOF