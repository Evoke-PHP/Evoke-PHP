<?php
namespace Evoke\Controller;

use Evoke\HTTP\ResponseIface,
	Evoke\View\ViewIface,
	Evoke\Writer\WriterIface;

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
	 * View for the Not Found page.
	 * @var ViewIface
	 */
	protected $view;
	
	/**
	 * Construct the Controller.
	 *
	 * @param string          The output format to use in uppercase.
	 * @param mixed[]		  Parameters.
	 * @param ResponseIface   Response object.
	 * @param WriterIface 	  Writer object.
	 * @param ViewIface       View
	 * @param mixed[]		  Setup for page based output formats.
	 */
	public function __construct(/* String */  $outputFormat,
	                            Array         $params,
	                            ResponseIface $response,
	                            WriterIface   $writer,
	                            ViewIface     $view,
	                            Array         $pageSetup = array())
	{
		parent::__construct($outputFormat, $params, $response,
		                    $writer, $pageSetup);

		$this->view = $view;
	}

	/******************/
	/* Public Methods */
	/******************/

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
			$this->writer->write($this->view->get());
			$this->writer->writeEnd();
			break;
		}

		$this->response->setStatus(404);
		$this->response->setBody($this->writer);
		$this->response->send();
	}
}
// EOF