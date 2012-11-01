<?php
namespace Evoke\Controller;

use Evoke\HTTP\ResponseIface,
	Evoke\Writer\WriterIface;
	
/**
 * Abstract Controller
 *
 * Controllers are responsible for providing and using the correct objects
 * from the processing, model and view layers to execute the desired request
 * from the user.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Controller
 */
abstract class Controller
{
	/**
	 * Output format as an uppercase string (JSON, XHTML, etc.)
	 * @var string
	 */
	protected $outputFormat;
	
	/** 
	 * Setup for the page based output formats (XHTML, HTML5).
	 * @var mixed[]
	 */
	protected $pageSetup;

	/**
	 * Parameters for the Controller.
	 * @var mixed[]
	 */
	protected $params;
	
	/**
	 * Response Object
	 * @var ResponseIface
	 */
	protected $response;

	/**
	 * Writer Object
	 * @var WriterIface
	 */
	protected $writer;
	
	/**
	 * Construct the Controller.
	 *
	 * @param string        The output format to use in uppercase.
	 * @param mixed[]		Parameters.
	 * @param ResponseIface Response object.
	 * @param WriterIface 	Writer object.
	 * @param mixed[]		Setup for page based output formats.
	 */
	public function __construct(/* String */  $outputFormat,
	                            Array         $params,
	                            ResponseIface $response,
	                            WriterIface   $writer,
	                            Array         $pageSetup = array())
	{
		$this->outputFormat = $outputFormat;
		$this->pageSetup    = $pageSetup;
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
}
// EOF