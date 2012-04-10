<?php
namespace Evoke\Controller;

class Error extends Base
{
	/*********************/
	/* Protected Methods */
	/*********************/

	/// Initialize the controller by setting the response code to 404 Not Found.
	protected function initialize()
	{
		$this->Response->setResponseCode(500);
	}

	protected function html5ALL()
	{
		$this->xhtmlALL();
	}
	
	protected function jsonALL()
	{
		$this->Writer->write(array('Code'    => '500',
		                           'Message' => $this->getMessage(),
		                           'Title'   => 'Internal Server Error'));
	}
	
	protected function textALL()
	{
		$this->Writer->write(
			rtrim('500 Internal Server Error ' . $this->getMessage()));
	}
	
	protected function xhtmlALL()
	{
		$this->startXHTML();
		$this->writeMessageBoxXML();
		$this->endXHTML();
	}
	
	protected function xmlALL()
	{
		$this->writeMessageBoxXML();
	}

	/*******************/
	/* Private Methods */
	/*******************/

	// Get the description of the error.
	private function getMessage()
	{
		$Translator = $this->Factory->getTranslator();

		$description = $Translator->get('Error_Text');

		if (isset($this->params['Exception']) &&
		    $this->params['Exception'] instanceof \Exception)
		{
			$description .= "\n" . $this->params['Exception']->getMessage();
		}

		return $description;
	}
	
	/// Write a Message Box in XML showing the Not Found message.
	private function writeMessageBoxXML()
	{
		$Element = $this->Factory->build(
			'Evoke\Element\Message\Box',
			array('Attribs' => array('class' => 'Message_Box System')));
		$Translator = $this->Factory->getTranslator();

		$description = explode("\n", $this->getMessage());
		$descriptionWithBreaks = array();

		foreach ($description as $entry)
		{
			$descriptionWithBreaks[] = $entry;
			$descriptionWithBreaks[] = array('br');
		}

		array_pop($descriptionWithBreaks);

		$this->Writer->write(
			array('div',
			      array('class' => 'Message_Box System'),
			      array(array('div',
			                  array('class' => 'Title'),
			                  $Translator->get('Error_Title')),
			            array('div',
			                  array('class' => 'Description'),
			                  $descriptionWithBreaks))));
	}	
}
// EOF