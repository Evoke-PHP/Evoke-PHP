<?php
namespace Evoke;

class Element_Form_Dialog extends Element_Form
{
   public function __construct(Array $setup)
   {
      $setup += array(
	 'Attribs'                  => array('class'  => 'Dialog',
					     'action' => '',
					     'method' => 'post'),
	 'Heading_Attribs'          => array('class' => 'Heading'),
	 'Heading_Elements'         => array(),
	 'Heading_Elements_Attribs' => array('class' => 'Heading_Elements'),
	 'Heading_Text'             => '',
	 'Heading_Text_Attribs'     => array('class' => 'Heading_Text'),
	 'Message_Attribs'          => array('class' => 'Message'),
	 'Message_Elements'         => array(),
	 'Message_Elements_Attribs' => array('class' => 'Message_Elements'),
	 'Message_Text'             => '',
	 'Message_Text_Attribs'     => array('class' => 'Message_Text'),
	 'Submit_Buttons'           => array(),
	 'Submit_Button_Attribs'    => array('class' => 'Buttons'));

      parent::__construct($setup);
   }
   
   /*********************/
   /* Protected Methods */
   /*********************/

   /// Build the Form Elements.
   protected function buildFormElements()
   {
      $this->buildHeading();
      $this->buildMessage();
   }

   /// Build the heading.
   protected function buildHeading()
   {
      $headingChildren = array();
      
      if (!empty($this->setup['Heading_Text']))
      {
	 $headingChildren[] = array(
	    'div',
	    $this->setup['Heading_Text_Attribs'],
	    array('Text' => $this->setup['Heading_Text']));
      }

      $headingChildren[] = array(
	 'div',
	 $this->setup['Heading_Elements_Attribs'],
	 array('Children' => $this->setup['Heading_Elements']));
      
      $this->addElement(array('div',
			      $this->setup['Heading_Attribs'],
			      array('Children' => $headingChildren)));
   }

   /// Build the content.
   protected function buildMessage()
   {
      $messageChildren = array();

      if (!empty($this->setup['Message_Text']))
      {
	 $messageChildren[] = array(
	    'div',
	    $this->setup['Message_Text_Attribs'],
	    array('Text' => $this->setup['Message_Text']));
      }

      $messageChildren[] = array(
	 'div',
	 $this->setup['Message_Elements_Attribs'],
	 array('Children' => $this->setup['Message_Elements']));
      
      
      $this->addElement(array('div',
			      $this->setup['Message_Attribs'],
			      array('Children' => $messageChildren)));
   }
}
// EOF