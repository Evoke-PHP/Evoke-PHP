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
		$this->response->setResponseCode(404);
	}

	protected function html5ALL()
	{
		$this->xhtmlALL();
	}
	
	protected function jsonALL()
	{
		$this->writer->write(array('Code' => '404',
		                           'Text' => 'Not Found'));
	}
	
	protected function textALL()
	{
		$this->writer->write('404 Not Found');
	}
	
	protected function xhtmlALL()
	{
		$this->startXHTML();
		$this->writeMessageBoxXML();
		$this->writer->writeEnd();
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
		$element = $this->provider->make(
			'Evoke\Element\Message\Box',
			array('Attribs' => array('class' => 'Message_Box System')));
		$translator = $this->provider->make('Evoke\Translator');
		
		$this->writer->write(
			$element->set(
				array('Description' => $translator->get('Not_Found_Text'),
				      'Title'       => $translator->get('Not_Found_Title'))));
	}	
}
// EOF