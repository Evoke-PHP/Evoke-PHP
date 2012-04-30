<?php
namespace Evoke\Element\Form;

class Dialog extends Base
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
      
		if (!empty($this->headingText))
		{
			$headingChildren[] = array('div',
			                           $this->headingTextAttribs,
			                           $this->headingText);
		}

		$headingChildren[] = array('div',
		                           $this->headingElementsAttribs,
		                           $this->headingElements);
      
		$this->addElement(array('div',
		                        $this->headingAttribs,
		                        $headingChildren));
	}

	/// Build the content.
	protected function buildMessage()
	{
		$messageChildren = array();

		if (!empty($this->messageText))
		{
			$messageChildren[] = array('div',
			                           $this->messageTextAttribs,
			                           $this->messageText);
		}

		$messageChildren[] = array('div',
		                           $this->messageElementsAttribs,
		                           $this->messageElements);
      
		$this->addElement(array('div',
		                        $this->messageAttribs,
		                        $messageChildren));
	}
}
// EOF