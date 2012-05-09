<?php
namespace Evoke\Controller;

use Evoke\Iface;

class Error extends \Evoke\Controller
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
		$this->writer->write(array('Code'    => '500',
		                           'Title'   => 'Internal Server Error'));
	}
	
	/** Respond with the error code first.
	 *  @param outputFormat @string The output format to use.
	 */
	protected function respond($outputFormat)
	{
		if (!headers_sent())
		{
			$this->response->setResponseCode(500);
		}

		parent::respond($outputFormat);
	}

	protected function textALL()
	{
		$this->writer->write('500 Internal Server Error');
	}
	
	protected function xhtmlALL()
	{
		$this->writer->writeStart(
			array_merge($this->pageSetup,
			            array('Description' => 'Internal Server Error',
			                  'Title'       => 'Internal Server Error')));
		$this->writeXMLError();
		$this->writer->writeEnd();
	}
	
	protected function xmlALL()
	{
		$this->writeXMLError();
	}

	/*******************/
	/* Private Methods */
	/*******************/
	
	/// Write the error in XML.
	private function writeXMLError()
	{
		$element = $this->provider->make(
			'Evoke\Element\Message\Box',
			array('Attribs' => array('class' => 'Message_Box System')));

		$this->params += array(
			'Description' => 'An error has occurred.  We have been notified.' .
			'  We will fix this as soon as possible.',
			'Title'       => 'System Error');
		
		$this->writer->write($element->set($this->params));		
	}	
}
// EOF