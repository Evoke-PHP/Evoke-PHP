<?php
namespace Evoke\Core\Init\Handler;

use Evoke\Core\Iface;

/// The system exception handler.
class Exception implements \Evoke\Core\Iface\Handler
{
	/** @property $detailedInsecureMessage
	 *  \bool Whether to display a detailed insecure message.
	 */
	protected $detailedInsecureMessage;

	/** @property $EventManager
	 *  EventManager \object
	 */
	protected $EventManager;

	/** @property $maxLengthExceptionMessage
	 *  \int The maximum length of exception message to display.
	 */
	protected $maxLengthExceptionMessage;

	/** @property $Writer
	 *  Writer \object
	 */
	protected $Writer;

	
	public function __construct($detailedInsecureMessage,
	                            $maxLengthExceptionMessage,
	                            Iface\EventManager $EventManager,
	                            Iface\Writer $Writer)
	{
		if (!is_bool($detailedInsecureMessage))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires detailedInsecureMessage to be boolean');
		}

		$this->detailedInsecureMessage   = $detailedInsecureMessage;
		$this->EventManager              = $EventManager;
		$this->maxLengthExceptionMessage = $maxLengthExceptionMessage;
		$this->Writer                    = $Writer;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/** Handle uncaught exceptions for the system by logging information and
	 *  displaying a generic notice to the user so that they are informaed of an
	 *  error without exposing information that could be used for an attack.
	 *
	 *  @param uncaughtException An exception that was not caught in the system.
	 */
	public function handler($uncaughtException)
	{
		try
		{
			if (!headers_sent())
			{
				header('HTTP/1.1 500 Internal Server Error');
			}

			$this->EventManager->notify(
				'Log',
				array('Level'   => LOG_CRIT,
				      'Message' => $uncaughtException->getMessage(),
				      'Method'  => __METHOD__));
			$loggedError = true;
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

			$message .= "<br>\n<br>\n" .
				preg_replace('/\n/', '<br>' . "\n", $exceptionMessage); //(string)($uncaughtException));
		}
		
		$this->Writer->write(
			array('div',
			      array('class' => 'Exception_Handler Message_Box System'),
			      array('Children' => array(
				            array('div',
				                  array('class' => 'Title'),
				                  array('Text' => $title)),
				            array('div',
				                  array('class' => 'Description'),
				                  array('Text' => $message))))));
		$this->Writer->output();
	}

	public function register()
	{
		return set_exception_handler(array($this, 'handler'));
	}

	public function unregister()
	{
		return restore_exception_handler();      
	}
}
// EOF