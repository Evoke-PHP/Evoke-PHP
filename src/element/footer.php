<?php
class Element_Footer extends Element
{ 
   public function __construct($setup)
   {
      $setup += array('Attribs' => array('class' => 'Footer'),
		      'Text'    => '');
      
      parent::__construct($setup);

      parent::set(array('div',
			$this->setup['Attribs'],
			array('Text' => $this->setup['Text'])));
   }
}
// EOF