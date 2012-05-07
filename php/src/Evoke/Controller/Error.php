<?php
namespace Evoke\Controller;

use Evoke\Iface;

class Error extends \Evoke\Controller
{
	/*********************/
	/* Protected Methods */
	/*********************/
	
	protected function html5ALL(Iface\Writer\Page $writer)
	{
		$this->xhtmlALL($writer);
	}
	
	protected function jsonALL(Iface\Writer $writer)
	{
		$writer->write(array('Code'    => '500',
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

	protected function textALL(Iface\Writer $writer)
	{
		$writer->write('500 Internal Server Error');
	}
	
	protected function xhtmlALL(Iface\Writer\Page $writer)
	{
		$writer->writeStart(
			array_merge($this->pageSetup,
			            array('Description' => 'Internal Server Error',
			                  'Title'       => 'Internal Server Error')));
		$this->writeMessageBoxXML($writer);
		$writer->writeEnd();
	}
	
	protected function xmlALL(Iface\Writer $writer)
	{
		$this->writeMessageBoxXML($writer);
	}

	/*******************/
	/* Private Methods */
	/*******************/
	
	/// Write a Message Box in XML showing the Not Found message.
	private function writeMessageBoxXML(Iface\Writer $writer)
	{
		$element = $this->provider->make(
			'Evoke\Element\Message\Box',
			array('Attribs' => array('class' => 'Message_Box System')));

		$detailedDescription = array();

		if (isset($this->params['Exception']) &&
		    $this->params['Exception'] instanceof \Exception)
		{
			$eParts = explode("\n",
			                  $this->params['Exception']->getMessage() . "\n" .
			                  $this->params['Exception']->getTraceAsString());
			
			foreach ($eParts as $part)
			{
				$detailedDescription[] = $part;
				$detailedDescription[] = array('br');
			}

			array_pop($detailedDescription);
		}


		$writer->write(
			array('div',
			      array('class' => 'Message_Box System'),
			      array(array('div',
			                  array('class' => 'Title'),
			                  'System Error'),
			            array('div',
			                  array('class' => 'Description'),
			                  'An error has occurred. We have been notified.' .
			                  '  We will fix this as soon as possible.'),
			            array('div',
			                  array('class' => 'Detailed Description'),
			                  $detailedDescription))));
	}	
}
// EOF