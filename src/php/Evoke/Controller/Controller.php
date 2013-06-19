<?php
/**
 * Controller
 *
 * @package Controller
 */
namespace Evoke\Controller;

use Evoke\HTTP\ResponseIface,
	Evoke\Writer\WriterIface,
	Evoke\View\ViewIface;
	
/**
 * Controller
 *
 * Controllers are responsible for processing input and passing the data to the
 * views.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Controller
 */
class Controller extends ControllerAbstract
{
	protected
		/**
		 * View.
		 * @var ViewIface
		 */
		$view;
	
	/**
	 * Construct the Controller.
	 *
	 * @param string        The output format to use in uppercase.
	 * @param mixed[]		Setup for page based output formats.
	 * @param mixed[]		Parameters.
	 * @param ResponseIface Response.
	 * @param WriterIface 	Writer.
	 * @param ViewIface     View.
	 */
	public function __construct(/* String */  $outputFormat,
	                            Array         $pageSetup,
	                            Array         $params,
	                            ResponseIface $response,
	                            WriterIface   $writer,
	                            ViewIface     $view)
	{
		parent::__construct(
			$outputFormat, $pageSetup, $params, $response, $writer);

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
		$this->requireCleanWriter();
		$pageBased = $this->isPageBased();

		if ($pageBased)
		{
			$this->writer->writeStart($this->pageSetup);
		}

		$this->writer->write($this->view->get());

		if ($pageBased)
		{
			$this->writer->writeEnd();
		}

		$this->response->setBody($this->writer);
		$this->response->send();
	}
}
// EOF