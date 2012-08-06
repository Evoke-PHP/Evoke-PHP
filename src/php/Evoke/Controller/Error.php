<?php
namespace Evoke\Controller;

/**
 * Error Controller
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Controller
 */
class Error extends Controller
{
	/**
	 * Execute the controller responding to the request method in the correct
	 * output format.
	 *
	 * @param string The Request method (POST, GET, PUT, DELETE, etc.)
	 * @param string The output format to use in uppercase.
	 */
	public function execute($method, $outputFormat)
	{			
		$currentBuffer = (string)($this->writer);

		if (!empty($currentBuffer))
		{
			trigger_error(
				'Buffer needs to be flushed for clean error page, was: ' .
				$currentBuffer, E_USER_WARNING);
			$this->writer->flush();
		}

		switch ($outputFormat)
		{
		case 'JSON':
			$this->writer->write(array('Code'    => '500',
			                           'Title'   => 'Internal Server Error'));
			break;

		case 'TEXT':
			$this->writer->write('500 Internal Server Error');
			break;
			
		case 'HTML5':
		case 'XHTML':
		case 'XML':
		default:
			$this->writer->writeStart($this->pageSetup);
			$this->writeXMLError();
			$this->writer->writeEnd();
			break;
		}
		
		$this->response->setStatus(500);
		$this->response->setBody($this->writer);
		$this->response->send();
	}

	/*******************/
	/* Private Methods */
	/*******************/
	
	/**
	 * Write the error in XML.
	 */
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