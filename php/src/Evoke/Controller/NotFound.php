<?php
namespace Evoke\Controller;

class NotFound extends Base
{	
	/*********************/
	/* Protected Methods */
	/*********************/

	/// Initialize the controller by setting the response code to 404 Not Found.
	protected function initialize()
	{
		$this->Response->setResponseCode(404);
	}

	protected function html5ALL()
	{
		$this->xhtmlALL();
	}
	
	protected function jsonALL()
	{
		$this->Writer->write(array('Code' => '404',
		                           'Text' => 'Not Found'));
	}
	
	protected function textALL()
	{
		$this->Writer->write('404 Not Found');
	}
	
	protected function xhtmlALL()
	{
		$this->startXHTML();
		$this->writeMessageBoxXML();
		$this->Writer->writeEnd();
	}
	
	protected function xmlALL()
	{
		$this->writeMessageBoxXML();
	}

	/*******************/
	/* Private Methods */
	/*******************/

	/// Write a Message Box in XML showing the Not Found message.
	private function writeMessageBoxXML()
	{
		$Element = $this->Factory->build(
			'Evoke\Element\Message\Box',
			array('Attribs' => array('class' => 'Message_Box System')));
		$Translator = $this->Factory->getTranslator();
		
		$this->Writer->write(
			$Element->set(
				array('Description' => $Translator->get('Not_Found_Text'),
				      'Title'       => $Translator->get('Not_Found_Title'))));
	}	
}
// EOF