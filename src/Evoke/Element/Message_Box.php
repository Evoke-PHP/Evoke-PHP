<?php
namespace Evoke;

class Element_Message_Box extends Element
{ 
   public function __construct(Array $setup)
   {
      $setup += array('Default_Attribs' => array('class' => 'Message_Box Info'));

      parent::__construct($setup);
   }
   
   /******************/
   /* Public Methods */
   /******************/

   public function set(Array $message)
   {
      $message += array('Description' => NULL,
			'Title'       => NULL);

      if (!isset($message['Description']))
      {
	 throw new \InvalidArgumentException(__METHOD__ . ' needs Description');
      }

      if (!isset($message['Title']))
      {
	 throw new \InvalidArgumentException(__METHOD__ . ' needs Title');
      }
      
      return parent::set(
	 array('div',
	       array(),
	       array('Children' => array(
			array('div',
			      array('class' => 'Title'),
			      array('Text' => $message['Title'])),
			array('div',
			      array('class' => 'Description'),
			      array('Text' => $message['Description']))))));
   }
}
// EOF