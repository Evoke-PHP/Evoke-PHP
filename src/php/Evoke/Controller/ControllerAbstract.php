<?php
namespace Evoke\Controller;

use Evoke\HTTP\ResponseIface,
	Evoke\Writer\WriterIface;
	
/**
 * ControllerAbstract
 *
 * Controllers are responsible for processing input and passing the data to the
 * views.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Controller
 */
abstract class ControllerAbstract implements ControllerIface
{
	protected
		/**
		 * Output format as an uppercase string (JSON, XHTML, etc.)
		 * @var string
		 */
		$outputFormat,
	
		/** 
		 * Setup for the page based output formats (XHTML, HTML5).
		 * @var mixed[]
		 */
		$pageSetup,
		
		/**
		 * Parameters for the Controller.
		 * @var mixed[]
		 */
		$params,
		
		/**
		 * Response
		 * @var ResponseIface
		 */
		$response,
		
		/**
		 * Writer
		 * @var WriterIface
		 */
		$writer;
	
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
				'Buffer needs to be flushed for clean error page, was: ' .
				$currentBuffer, E_USER_WARNING);
			$this->writer->flush();
		}
	}
}
// EOF