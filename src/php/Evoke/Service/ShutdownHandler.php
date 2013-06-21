<?php
/**
 * Shutdown Handler
 *
 * @package Service
 */
namespace Evoke\Service;

use Evoke\HTTP\ResponseIface,
	Evoke\Writer\WriterIface,
	InvalidArgumentException;

/**
 * Shutdown Handler
 *
 * The system shutdown handler called upon every shutdown if it is registered.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Service
 */
class ShutdownHandler
{
	
	/** 
	 * The administrator's email address.
	 * @var string
	 */
	protected $administratorEmail;
	
	/**
	 * Whether to display a detailed insecure message.
	 * @var bool
	 */
	protected $messageFullInsecure;
		
	/**
	 * Response object.
	 * @var ResponseIface
	 */
	protected $response;
		
	/**
	 * Writer object.
	 * @var WriterIface
	 */
	protected $writer;

	/**
	 * Construct the System Shutdown handler.
	 *
	 * @param string        Administrators Email to use as a contact.
	 * @param bool          Whether to show detailed logging information (which
	 *                      is insecure).
	 * @param ResponseIface Response object.
	 * @param WriterIface   The writer object to write the fatal message.
	 */
	public function __construct(/* String */  $administratorEmail,
	                            /* Bool   */  $messageFullInsecure,
	                            ResponseIface $response,
	                            WriterIface   $writer)
	{
		$this->administratorEmail  = $administratorEmail;
		$this->messageFullInsecure = $messageFullInsecure;
		$this->response            = $response;
		$this->writer              = $writer;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Handle the shutdown of the system, recording any fatal errors.
	 */
	public function handler()
	{
		$err = error_get_last();

		if (!isset($err))
		{
			return;
		}

		$handledErrorTypes = array(
			E_USER_ERROR      => 'USER ERROR',
			E_ERROR           => 'ERROR',
			E_PARSE           => 'PARSE',
			E_CORE_ERROR      => 'CORE_ERROR',
			E_CORE_WARNING    => 'CORE_WARNING',
			E_COMPILE_ERROR   => 'COMPILE_ERROR',
			E_COMPILE_WARNING => 'COMPILE_WARNING');
		
		if (!isset($handledErrorTypes[$err['type']]))
		{
			return;
		}

		if (!headers_sent())
		{
			header('HTTP/1.1 500 Internal Server Error');
		}
      
		$title = 'Fatal Error';
		$message = 'This is an error that we were unable to handle.  Please ' .
			'tell us any information that could help us avoid this error in ' .
			'the future.  Useful information such as the date, time and what ' .
			'you were doing when the error occurred should help us fix this.';

		if (!empty($this->administratorEmail))
		{
			$message .= "<br/>\n<br/>\n" .
				'Email us at ' . $this->administratorEmail;
		}
      
		if ($this->messageFullInsecure)
		{
			$message .= "<br/>\n<br/>\n" .
				'PHP [' . $handledErrorTypes[$err['type']] . '] ' .
				$err['message'] . "<br/>\n" .
				' in file ' . $err['file'] . ' at ' . $err['line'];
		}

		$this->writer->write(
			array('div',
			      array('class' => 'Shutdown_Handler Message_Box System'),
			      array(array('div', array('class' => 'Title'),       $title),
			            array('div', array('class' => 'Description'), $message)
				      )));
		$this->writer->writeEnd();

		$this->response->setStatus(500);
		$this->response->setBody($this->writer);
		$this->response->send();
	}

	/**
	 * Register the shutdown handler.
	 */
	public function register()
	{
		register_shutdown_function(array($this, 'handler'));
	}
}
// EOF