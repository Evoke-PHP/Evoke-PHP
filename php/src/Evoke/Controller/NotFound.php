<?php
namespace Evoke\Controller;

/**
 * NotFound Controller
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Controller
 */
class NotFound extends Controller
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
		if (!headers_sent())
		{
			$this->response->setResponseCode(404);
		}
		
		switch ($outputFormat)
		{
		case JSON:
			$this->writer->write(array('Code' => '404',
			                           'Text' => 'Not Found'));
			break;

		case TEXT:
			$this->writer->write('404 Not Found');
			break;
			
		case HTML5:
		case XHTML:
		case XHML:
		default:
			$this->writeXMLNotFound();
			break;
		}
		
		$this->writer->output();
	}

	/*******************/
	/* Private Methods */
	/*******************/

	/**
	 * Write a Message Box in XML showing the Not Found message.
	 */
	private function writeXMLNotFound()
	{
		$view = $this->provider->make(
			'Evoke\View\Message\Box',
			array('Attribs' => array('class' => 'Message_Box System')));

		$this->params += array(
			'Description' => 'The requested page could not be found.',
			'Title'       => 'Not Found');
		
		$this->writer->write($view->get($this->params));
	}	
}
// EOF