<?php
namespace Evoke\Core\Init\Handler;

/// The system exception handler.
class Exception implements \Evoke\Core\Iface\Handler
{
	protected $setup;
   
	public function __construct(Array $setup)
	{
		$this->setup = array_merge(array('Detailed_Insecure_Message'    => NULL,
		                                 'Event_Manager'                 => NULL,
		                                 'Max_Length_Exception_Message' => NULL),
		                           $setup);
				 
		if (!is_bool($this->detailedInsecureMessage))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Detailed_Insecure_Message to be boolean');
		}

		if (!$this->setup['Event_Manager'] instanceof \Evoke\Core\EventManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires EventManager');
		}

		if (!isset($this->maxLengthExceptionMessage))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Max_Length_Exception_Message');
		}
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

			$this->setup['Event_Manager']->notify(
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
			    mb_strlen($exceptionMessage) > $this->setup[
				    'Max_Length_Exception_Message'])
			{
				$halfMessage = $this->maxLengthExceptionMessage / 2;
				$exceptionMessage = mb_substr($exceptionMessage, 0, $halfMessage) .
					"\n\n <<< OUTPUT CUT HERE SEE LOG FOR FULL DETAILS >>> \n\n" .
					mb_substr($exceptionMessage, -$halfMessage);
			}

			$message .= "<br>\n<br>\n" .
				preg_replace('/\n/', '<br>' . "\n", $exceptionMessage); //(string)($uncaughtException));
		}
      
		$errorDocument =
			'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" ' . 
			'"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . "\n" .
			'<html xmlns="http://www.w3.org/1999/xhtml">' . "\n" .
			'   <head>' . "\n" .
			'      <title>Error</title>' . "\n" .
			'      <link type="text/css" href="/csslib/global.css" ' .
			'rel="stylesheet"></link>' . "\n" .
			'   </head>' . "\n" .
			'   <body>' . "\n" .
			'      <div class="Exception_Handler Message_Box System">' . "\n" .
			'         <div class="Title">' . $title . '</div>' . "\n" .
			'         <div class="Description">' . $message . '</div>' . "\n" .
			'      </div>' . "\n" .
			'   </body>' . "\n" .
			'</html>';
	 
		echo $errorDocument;
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