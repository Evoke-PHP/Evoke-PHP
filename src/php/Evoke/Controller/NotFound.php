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
	 * Execute the controller.
	 */
	public function execute()
	{
		switch ($this->outputFormat)
		{
		case 'JSON':
			$this->writer->write(array('Code' => '404',
			                           'Text' => 'Not Found'));
			break;

		case 'TEXT':
			$this->writer->write('404 Not Found');
			break;
			
		case 'HTML5':
		case 'XHTML':
		case 'XML':
		default:
			$this->writer->writeStart($this->pageSetup);
			$this->writeXMLNotFound();
			$this->writer->writeEnd();
			break;
		}

		$this->response->setStatus(404);
		$this->response->setBody($this->writer);
		$this->response->send();
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