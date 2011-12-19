<?php


class Element_Dialog extends Element
{
   protected $setup;
   
   public function __construct(Array $setup)
   {
      $this->setup = array_merge(
	 array('Buttons'         => array(),
	       'Buttons_Attribs' => array('class' => 'Buttons'),
	       'Content'         => NULL,
	       'Content_Attribs' => array('class' => 'Content'),
	       'Form_Attribs'    => array('class'  => 'Dialog',
					  'action' => '',
					  'method' => 'POST'),
	       'Heading'         => NULL,
	       'Heading_Attribs' => array('class' => 'Heading')),
	 $setup);

      if (!isset($this->setup['Content']))
      {
	 throw new InvalidArgumentException(__METHOD__ . ' needs Content');
      }

      if (!isset($this->setup['Heading']))
      {
	 throw new InvalidArgumentException(__METHOD__ . ' needs Heading');
      }
      
      $dialogItems = array(
	 array('div',
	       $this->setup['Heading_Attribs'],
	       array('Text' => $this->setup['Heading'])),
	 array('div',
	       $this->setup['Content_Attribs'],
	       array('Text' => $this->setup['Content'])));

      if (!empty($this->setup['Buttons']))
      {
	 $dialogItems[] =
	    array('div',
		  $this->setup['Buttons_Attribs'],
		  array('Children' => $this->setup['Buttons']));
      }
      
      parent::__construct(array('form',
				$this->setup['Form_Attribs'],
				array('Children' => $dialogItems)));
   }
}

// EOF