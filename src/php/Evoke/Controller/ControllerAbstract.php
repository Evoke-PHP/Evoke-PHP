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
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Controller
 */
abstract class ControllerAbstract implements ControllerIface
{
	/**
	 * Abstract Controller properties.
	 *
	 * @var mixed[]       $params   Parameters for the controller.
	 * @var ResponseIface $response Response object
	 * @var WriterIface   $writer   Writer object
	 */
	protected $params, $response, $writer;
	
	/**
	 * Construct the Controller.
	 *
	 * @param mixed[]		Parameters.
	 * @param ResponseIface Response.
	 * @param WriterIface 	Writer.
	 */
	public function __construct(Array         $params,
	                            ResponseIface $response,
	                            WriterIface   $writer)
	{
		$this->params   = $params;
		$this->response	= $response;
		$this->writer   = $writer;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Execute the controller.
	 */
	abstract public function execute();

	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Ensure a clean writer, triggering an error if the buffer is not clear.
	 */
	protected function requireCleanWriter()
	{
		$currentBuffer = (string)($this->writer);

		if (!empty($currentBuffer))
		{
			trigger_error(
				'Writer is required to be clean, found "' .	$currentBuffer .
				'" cleaning and continuing.',
				E_USER_WARNING);
			$this->writer->clean();
		}
	}
}
// EOF