<?php
/**
 * Controller
 *
 * @package Controller
 */
namespace Evoke\Controller;

use Evoke\Network\HTTP\ResponseIface,
	Evoke\Writer\WriterIface,
	Evoke\View\ViewIface;
	
/**
 * Controller
 *
 * Controllers are responsible for processing input and passing the data to the
 * views.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Controller
 */
class Controller extends ControllerAbstract
{
	/**
	 * View.
	 * @var ViewIface
	 */
	protected $view;
	
	/**
	 * Construct the Controller.
	 *
	 * @param mixed[]		Parameters.
	 * @param ResponseIface Response.
	 * @param WriterIface 	Writer.
	 * @param ViewIface     View.
	 */
	public function __construct(Array         $params,
	                            ResponseIface $response,
	                            WriterIface   $writer,
	                            ViewIface     $view)
	{
		parent::__construct($params, $response, $writer);

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
		$this->writer->writeStart();
		$this->writer->write($this->view->get());
		$this->response->setBody((string)$this->writer);
		$this->response->send();
	}
}
// EOF