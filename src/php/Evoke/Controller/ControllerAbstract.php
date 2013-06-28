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
	 * @var string        $outputFormat Output format as an upcase string (JSON,
	 *                                  XHTML, etc.)
	 * @var mixed[]       $pageSetup    Setup for page based output formats
	 *                                  (XHTML, HTML5).
	 * @var mixed[]       $params       Parameters for the controller.
	 * @var ResponseIface $response     Response object
	 * @var WriterIface   $writer       Writer object
	 */
	protected $outputFormat, $pageSetup, $params, $response, $writer;
	
	/**
	 * Construct the Controller.
	 *
	 * @param string        The output format to use in uppercase.
	 * @param mixed[]		Setup for page based output formats.
	 * @param mixed[]		Parameters.
	 * @param ResponseIface Response.
	 * @param WriterIface 	Writer.
	 */
	public function __construct(/* String */  $outputFormat,
	                            Array         $pageSetup,
	                            Array         $params,
	                            ResponseIface $response,
	                            WriterIface   $writer)
	{
		$this->outputFormat = $outputFormat;
		$this->pageSetup    = array_merge(array('CSS'         => array(),
		                                        'Description' => '',
		                                        'Keywords'    => '',
		                                        'JS'          => array(),
		                                        'Title'       => ''),
		                                  $pageSetup);		
		$this->params  	    = $params;
		$this->response	   	= $response;
		$this->writer  	    = $writer;
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
	 * Whether the controller is for a page based output.
	 *
	 * @return bool Whether the contoller is to produce a page based ouptut.
	 */
	protected function isPageBased()
	{
		return !in_array(strtolower($this->outputFormat),
		                 array('text', 'json'));
	}
	
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
				'" flushing and continuing.',
				E_USER_WARNING);
			$this->writer->flush();
		}
	}
}
// EOF