<?php
namespace Evoke\Controller;

use Evoke\HTTP\ResponseIface,
	Evoke\View\ViewIface,
	Evoke\Writer\WriterIface;

/**
 * Error Controller
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Controller
 */
class Error extends Controller
{
	/**
	 * isDetailed
	 * @var bool
	 */
	protected $isDetailed;

	/**
	 * View for the Error page.
	 * @var ViewIface
	 */
	protected $view;
	
	/**
	 * Construct the Controller.
	 *
	 * @param string        The output format to use in uppercase.
	 * @param mixed[]		Parameters.
	 * @param ResponseIface Response object.
	 * @param WriterIface 	Writer object.
	 * @param ViewIface     View
	 * @param bool          Whether the error message is detailed.
	 * @param mixed[]		Setup for page based output formats.
	 */
	public function __construct(/* String */  $outputFormat,
	                            Array         $params,
	                            ResponseIface $response,
	                            WriterIface   $writer,
	                            ViewIface     $view,
	                            /* Bool */    $isDetailed,
	                            Array         $pageSetup = array())
	{
		parent::__construct($outputFormat, $params, $response,
		                    $writer, $pageSetup);

		$this->isDetailed = $isDetailed;
		$this->view       = $view;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Execute the controller.
	 */
	public function execute()
	{			
		$currentBuffer = (string)($this->writer);

		if (!empty($currentBuffer))
		{
			trigger_error(
				'Buffer needs to be flushed for clean error page, was: ' .
				$currentBuffer, E_USER_WARNING);
			$this->writer->flush();
		}

		switch ($this->outputFormat)
		{
		case 'JSON':
			$this->writer->write(array('Code'    => '500',
			                           'Title'   => 'Internal Server Error'));
			break;

		case 'TEXT':
			$this->writer->write('500 Internal Server Error');
			break;
			
		case 'HTML5':
		case 'XHTML':
		case 'XML':
		default:
			$this->writer->writeStart($this->pageSetup);
			
			if ($this->isDetailed)
			{
				$this->writer->write($this->view->get(error_get_last()));
			}
			else
			{
				$this->writer->write($this->view->get());
			}
			
			$this->writer->writeEnd();
			break;
		}
		
		$this->response->setStatus(500);
		$this->response->setBody($this->writer);
		$this->response->send();
	}
}
// EOF