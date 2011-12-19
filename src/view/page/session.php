<?php

class View_Page_Session extends View_Page
{ 
   public function __construct(Array $setup)
   {
      $setup += array('Start_Base' => array(
			 'CSS' => array('/styleslib/global.css',
					'/styleslib/common.css',
					'/styleslib/session.css')));
      
      parent::__construct($setup);
   }
   
   /******************/
   /* Public Methods */
   /******************/

   /// Write the session so that we can see it.
   public function writeContent($data)
   {
      $this->xwr->write(
	 array('div',
	       array('class' => 'Heading'),
	       array('Text' => 'Session')));

      $this->xwr->write(
	 array('form',
	       array('action' => '',
		     'class'  => 'Clear_Form',
		     'method' => 'POST'),
	       array('Children' => array(
			array('input',
			      array('name'  => 'Clear',
				    'type'  => 'submit',
				    'value' => 'Clear Session'))))));
      $this->xwr->write(array('div'));
      $this->xwr->write(
	 array('p',
	       array('class' => 'Session_Data'),
	       array('Text' => var_export($data, true))));
   }
}

// EOF