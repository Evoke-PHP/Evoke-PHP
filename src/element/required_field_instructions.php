<?php
class Element_Required_Field_Instructions extends Element
{ 
   public function __construct(Array $setup)
   {
      $setup += array('Translator' => NULL);

      parent::__construct($setup);

      if (!$this->setup['Translator'] instanceof Translator)
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' requires Translator');
      }

      parent::set(
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