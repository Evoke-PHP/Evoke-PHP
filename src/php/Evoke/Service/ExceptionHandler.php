<?php
/**
 * Exception Handler
 *
 * @package Service
 */
namespace Evoke\Service;

use Evoke\HTTP\ResponseIface,
	Evoke\View\ExceptionIface as ViewExceptionIface,
	Evoke\Writer\WriterIface;

/**
 * Exception Handler
 *
 * The system exception handler.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Service
 */
class ExceptionHandler
{
	/**
	 * Properties for the Exception Handler.
	 *
	 * @var bool               $showException Whether to display the exception.
	 * @var ResponseIface      $response      Response object.
	 * @var ViewExceptionIface $view          Exception view.
	 * @var WriterIface        $writer        Writer object.
	 */
	protected $response, $showException, $writer;

	/**
	 * Construct an Exception Handler object.
	 *
	 * @param ResponseIface Response object.
	 * @param bool          Whether to show the exception.
	 * @param WriterIface   Writer object.
	 * @param ViewExceptionIface View of the exception (if shown).
	 */
	public function __construct(ResponseIface $response,
	                            /* Bool */    $showException,
	                            WriterIface   $writer,
	                            ViewException $viewException = NULL)
	{
		if ($showException && !isset($viewException))
		{
			throw new InvalidArgumentException(
				'needs Exception view if we are showing the exception.');
		}
		
		$this->showException = $showException;
		$this->response      = $response;
		$this->viewException = $viewException;
		$this->writer        = $writer;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Handle uncaught exceptions for the system by logging information and
	 * displaying a generic notice to the user so that they are informaed of an
	 * error without exposing information that could be used for an attack.
	 *
	 * @param \Exception An exception that was not caught in the system.
	 *
	 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function handler(\Exception $uncaughtException)
	{
		trigger_error($uncaughtException->getMessage(), E_USER_ERROR);

		if (!headers_sent())
		{
			header('HTTP/1.1 500 Internal Server Error');
		}

		$currentBuffer = (string)($this->writer);
		
		if (!empty($currentBuffer))
		{
			trigger_error(
				'Bufffer needs to be flushed in exception handler for ' .
				'clean error page.  Buffer was: ' .	$currentBuffer,
				E_USER_WARNING);
			$this->writer->flush();
		}

		$this->writer->writeStart(
			array('CSS'   => array('/csslib/global.css'),
			      'Title' => '500 Internal Server Error'));

		$messageBoxElements = array(
			array('div', array('class' => 'Title'), 'System Error'),
			array('div',
			      array('class' => 'Description'),
			      'The administrator has been notified.'));

		if ($this->displayException)
		{
			$this->viewException->setException($uncaughtException);
			$messageBoxElements[] = $this->viewException->get();
		}
		
		$this->writer->write(
			array('div',
			      array('class' => 'Message_Box System Exception'),
			      $messageBoxElements));
		$this->writer->writeEnd();

		$this->response->setStatus(500);
		$this->response->setBody($this->writer);
		$this->response->send();
	}

	/**
	 * Register the exception handler.
	 *
	 * @return mixed NULL or the previously defined exception handler function.
	 */
	public function register()
	{
		return set_exception_handler(array($this, 'handler'));
	}

	/**
	 * Unregister the exception handler.
	 *
	 * @return bool TRUE (as per restore_exception_handler()).
	 */
	public function unregister()
	{
		return restore_exception_handler();
	}
}
// EOF