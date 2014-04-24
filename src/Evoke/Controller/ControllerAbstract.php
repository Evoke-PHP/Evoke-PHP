<?php
/**
 * Abstract Controller
 *
 * @package Controller
 */
namespace Evoke\Controller;

use Evoke\Network\HTTP\ResponseIface,
	Evoke\Writer\WriterIface;
	
/**
 * ControllerAbstract
 *
 * Controllers are responsible for processing input and passing the data to the
 * views.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Controller
 */
abstract class ControllerAbstract implements ControllerIface
{
	/**
	 * Abstract Controller properties.
	 *
	 * @var string        $outputFormat Output format required.
	 * @var mixed[]       $params       Parameters for the controller.
	 * @var ResponseIface $response     Response object
	 */
	protected $outputFormat, $params, $response;
	
	/**
	 * Construct the Controller.
	 *
	 * @param string        Output format.
	 * @param mixed[]		Parameters.
	 * @param ResponseIface Response.
	 */
	public function __construct(/* String */  $outputFormat,
	                            Array         $params,
	                            ResponseIface $response)
	{
		$this->outputFormat = $outputFormat;
		$this->params       = $params;
		$this->response     = $response;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Execute the controller.
	 */
	abstract public function execute();
}
// EOF