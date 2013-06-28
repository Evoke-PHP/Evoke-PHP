<?php
/**
 * Exception Handler
 *
 * @package Service
 */
namespace Evoke\Service;

use Evoke\HTTP\ResponseIface,
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
	 * @var bool          $messageFullInsecure
	 * Whether to display a detailed insecure message.
	 *
	 * @var int           $messageLengthMax
	 * The maximum length of exception message to display.
	 *
	 * @var ResponseIface $response
	 * Response object.
	 *
	 * @var WriterIface   $writer
	 * Writer object.
	 */
	protected $messageFullInsecure, $messageLengthMax, $response, $writer;

	/**
	 * Construct an Exception Handler object.
	 *
	 * @param bool          Whether to show a detailed insecure message.
	 * @param int           Maximum length of exception message to show.
	 * @param ResponseIface Response object.
	 * @param WriterIface   Writer object.
	 */
	public function __construct(/* Bool */    $messageFullInsecure,
	                            /* Int  */    $messageLengthMax,
	                            ResponseIface $response,
	                            WriterIface   $writer)
	{
		$this->messageFullInsecure = $messageFullInsecure;
		$this->messageLengthMax    = $messageLengthMax;
		$this->response            = $response;
		$this->writer              = $writer;
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

		$messageBoxElements = array(
			array('div', array('class' => 'Title'), 'System Error'),
			array('div',
			      array('class' => 'Message'),
			      'The administrator has been notified.'));
		
		// Provide extended details for development servers.
		if ($this->messageFullInsecure)
		{
			$exceptionMessage = (string)($uncaughtException);

			// If the exception is huge, only include the start and end on
			// screen.
			if ($this->messageLengthMax > 0 &&
			    mb_strlen($exceptionMessage) > $this->messageLengthMax)
			{
				$halfMessage = $this->messageLengthMax / 2;
				$exceptionMessage =
					mb_substr($exceptionMessage, 0, $halfMessage) . "\n\n" .
					'<<< OUTPUT CUT HERE SEE LOG FOR FULL DETAILS >>>' .
					"\n\n" . mb_substr($exceptionMessage, -$halfMessage);
			}

			$description .= "\n\n" . $exceptionMessage;
			$messageBoxElements[] =
				array('p', array('class' => 'Description'), $description);
		}

		$this->writer->writeStart(
			array('CSS'   => array('/csslib/global.css'),
			      'Title' => '500 Internal Server Error'));

		
		$this->writer->write(array('div',
		                           array('class' => 'Message_Box System'),
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