<?php


/// Element_Message_Box
class Element_Message_Box extends Element
{ 
   public function __construct(Array $setup)
   {
      $setup = array_merge(array('Class'       => '',
				 'Description' => NULL,
				 'Severity'    => 'Info',
				 'Title'       => NULL),
			   $setup);

      if (!isset($setup['Description']))
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' needs Description');
      }

      if (!isset($setup['Title']))
      {
	 throw new InvalidArgumentException(__METHOD__ . ' needs Title');
      }
	    
      $messageBoxClass = rtrim('Message_Box ' . $setup['Severity'] .
			       $setup['Class']);
      
      parent::__construct(
	 array('div',
	       array('class' => $messageBoxClass),
	       array('Children' => array(
			array('div',
			      array('class' => 'Title'),
			      array('Text' => $setup['Title'])),
			array('div',
			      array('class' => 'Description'),
			      array('Text' => $setup['Description']))))));
   }
}

// EOF