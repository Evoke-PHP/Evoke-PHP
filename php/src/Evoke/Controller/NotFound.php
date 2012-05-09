<?php
namespace Evoke\Controller;

use Evoke\Iface;

class NotFound extends \Evoke\Controller
{
	/*********************/
	/* Protected Methods */
	/*********************/

	protected function html5ALL()
	{
		$this->xhtmlALL();
	}
	
	protected function jsonALL()
	{
		$this->writer->write(array('Code' => '404',
		                           'Text' => 'Not Found'));
	}
	
	/** Respond with the error code first.
	 *  @param outputFormat @string The output format to use.
	 */
	protected function respond($outputFormat)
	{
		if (!headers_sent())
		{
			$this->response->setResponseCode(404);
		}
		
		parent::respond($outputFormat);
	}

	protected function textALL()
	{
		$this->writer->write('404 Not Found');
	}
	
	protected function xhtmlALL()
	{
		
		$this->writer->writeStart(
			array_merge($this->pageSetup,
			            array('Description' => 'Not Found',
			                  'Title'       => 'Page Not Found')));
		$this->writeXMLNotFound();
		$this->writer->writeEnd();
	}
	
	protected function xmlALL()
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