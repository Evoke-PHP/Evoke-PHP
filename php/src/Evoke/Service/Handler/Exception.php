<?php
namespace Evoke\Service\Handler;

use Evoke\Service\Log\LogIface,
	Evoke\Writer\WriterIface,
	InvalidArgumentException;

/**
 * Exception Handler
 *
 * The system exception handler.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
class Exception implements HandlerIface
{
	/**
	 * Whether to display a detailed insecure message.
	 * @var bool
	 */
	protected $detailedInsecureMessage;

	/**
	 * Log object.
	 * @var Evoke\Service\Log\LogIface
	 */
	protected $log;

	/**
	 * The maximum length of exception message to display.
	 * @var int
	 */
	protected $maxLengthExceptionMessage;

	/**
	 * Writer object.
	 * @var Evoke\Writer\WriterIface
	 */
	protected $writer;

	/**
	 * Construct an Exception Handler object.
	 *
	 * @param bool Whether to show a detailed insecure message.
	 * @param int  Maximum length of exception message to show.
	 * @param Evoke\Service\Log\LogIface
	 *             Log object.
	 * @param Evoke\Writer\WriterIface
	 *             Writer object.
	 */
	public function __construct(/* Bool */  $detailedInsecureMessage,
	                            /* Int  */  $maxLengthExceptionMessage,
	                            LogIface $log,
	                            WriterIface $writer)
	{
		if (!is_bool($detailedInsecureMessage))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires detailedInsecureMessage to be boolean');
		}

		$this->detailedInsecureMessage   = $detailedInsecureMessage;
		$this->log                       = $log;
		$this->maxLengthExceptionMessage = $maxLengthExceptionMessage;
		$this->writer                    = $writer;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Handle uncaught exceptions for the system by logging information and
	 * displaying a generic notice to the user so that they are informaed of an
	 * error without exposing information that could be used for an attack.
	 *
	 * @param Exception An exception that was not caught in the system.
	 */
	public function handler(Exception $uncaughtException)
	{
		try
		{
			if (!headers_sent())
			{
				header('HTTP/1.1 500 Internal Server Error');
			}

			$this->log->log($uncaughtException->getMessage(), E_USER_ERROR);
			$loggedError = true;

			$currentBuffer = (string)($this->writer);

			if (!empty($currentBuffer))
			{
				$this->log->log(
					'Buffer needs to be flushed in exception handler for ' .
					'clean error page.  Buffer was: ' .	$currentBuffer,
					E_USER_WARNING);
				$this->writer->flush();
			}
		}
		catch (\Exception $raisedException)
		{
			$loggedError = false;
		}
		
		if (isset($_GET['l']) && ($_GET['l'] === 'ES'))
		{
			$title = 'Error de Sistema';

			if ($loggedError)
			{
				$message = 'El administrador ha estado notificado del error.  ' .
					'Perdon, vamos a arreglarlo.';
			}
			else
			{
				$message =
					'No pudimos notificar el administrador de esta problema.  ' .
					'Por favor llamanos, queremos arreglarlo.';
			}
		}
		else
		{
			$title = 'System Error';

			if ($loggedError)
			{
				$message = 'The administrator has been notified of this error.  ' .
					'Sorry, we will fix this.';
			}
			else
			{
				$message =
					'The administrator could not be notified of this error.  ' .
					'Please call us, we want to fix this error.';
			}
		}

		// Provide extended details for development servers.
		if ($this->detailedInsecureMessage)
		{
			$exceptionMessage = (string)($uncaughtException);

			// If the exception is huge, only include the start and end on screen.
			if ($loggedError && $this->maxLengthExceptionMessage > 0 &&
			    mb_strlen($exceptionMessage) > $this->maxLengthExceptionMessage)
			{
				$halfMessage = $this->maxLengthExceptionMessage / 2;
				$exceptionMessage = mb_substr($exceptionMessage, 0, $halfMessage) .
					"\n\n <<< OUTPUT CUT HERE SEE LOG FOR FULL DETAILS >>> \n\n" .
					mb_substr($exceptionMessage, -$halfMessage);
			}

			$message .= "\n\n" . $exceptionMessage;
			$messageElements = explode("\n", $message);
			$messageChildren = array();
			
			foreach ($messageElements as $text)
			{
				$messageChildren[] =
					array('div', array('class' => 'Line'), $text);
			}

			$message = array(
				array('div', array('class' => 'Message'), $messageChildren));
		}

		$this->writer->writeStart(
			array('CSS'   => array('/csslib/global.css'),
			      'Title' => '500 Internal Server Error'));
		$this->writer->write(
			array('div',
			      array('class' => 'Exception_Handler Message_Box System'),
			      array(array('div', array('class' => 'Title'), $title),
			            array('div', array('class' => 'Description'), $message))
				));
		$this->writer->writeEnd();
		$this->writer->output();
	}

	/**
	 * Register the handler.
	 */
	public function register()
	{
		return set_exception_handler(array($this, 'handler'));
	}

	/**
	 * Unregister the handler.
	 */
	public function unregister()
	{
		return restore_exception_handler();      
	}
}
// EOF