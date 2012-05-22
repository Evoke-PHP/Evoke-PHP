<?php
namespace Evoke\Controller;

class NotFound extends \Evoke\Controller
{
	/** Execute the controller responding to the request method in the correct
	 *  output format.
	 *  @param method       @string The Request method.
	 *  @param outputFormat @string The output format to use.
	 */
	public function execute($method, $outputFormat)
	{
		if (!headers_sent())
		{
			$this->response->setResponseCode(404);
		}
		
		parent::execute($method, $outputFormat);
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	protected function html5All()
	{
		$this->xhtmlAll();
	}
	
	protected function jsonAll()
	{
		$this->writer->write(array('Code' => '404',
		                           'Text' => 'Not Found'));
	}
	
	protected function textAll()
	{
		$this->writer->write('404 Not Found');
	}
	
	protected function xhtmlAll()
	{
		
		$this->writer->writeStart(
			array_merge($this->pageSetup,
			            array('Description' => 'Not Found',
			                  'Title'       => 'Page Not Found')));
		$this->writeXMLNotFound();
		$this->writer->writeEnd();
	}
	
	protected function xmlAll()
	{
		$this->writeXMLNotFound();
	}

	/*******************/
	/* Private Methods */
	/*******************/

	/// Write a Message Box in XML showing the Not Found message.
	private function writeXMLNotFound()
	{
		$element = $this->provider->make(
			'Evoke\Element\Message\Box',
			array('Attribs' => array('class' => 'Message_Box System')));

		$this->params += array(
			'Description' => 'The requested page could not be found.',
			'Title'       => 'Not Found');
		
		$this->writer->write($element->set($this->params));
	}	
}
// EOF