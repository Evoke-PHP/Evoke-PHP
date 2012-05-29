<?php
namespace Evoke\Controller;

class Error extends Controller
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
			$this->response->setResponseCode(500);
		}

		$currentBuffer = (string)($this->writer);

		if (!empty($currentBuffer))
		{
			trigger_error(
				'Buffer needs to be flushed for clean error page, was: ' .
				$currentBuffer, E_USER_WARNING);
			$this->writer->flush();
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
		$this->writer->write(array('Code'    => '500',
		                           'Title'   => 'Internal Server Error'));
	}
	
	protected function textAll()
	{
		$this->writer->write('500 Internal Server Error');
	}
	
	protected function xhtmlAll()
	{		
		$this->writer->writeStart(
			array_merge($this->pageSetup,
			            array('Description' => 'Internal Server Error',
			                  'Title'       => 'Internal Server Error')));
		$this->writeXMLError();
		$this->writer->writeEnd();
	}
	
	protected function xmlAll()
	{
		$this->writeXMLError();
	}

	/*******************/
	/* Private Methods */
	/*******************/
	
	/// Write the error in XML.
	private function writeXMLError()
	{
		$view = $this->provider->make(
			'Evoke\View\Message\Box',
			array('Attribs' => array('class' => 'Message_Box System')));

		$this->params += array(
			'Description' => 'An error has occurred.  We have been notified.' .
			'  We will fix this as soon as possible.',
			'Title'       => 'System Error');
		
		$this->writer->write($view->get($this->params));
	}	
}
// EOF