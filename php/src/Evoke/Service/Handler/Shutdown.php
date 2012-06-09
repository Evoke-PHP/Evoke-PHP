<?php
namespace Evoke\Service\Handler;

use BadMethodCallException,
	Evoke\Writer\WriterIface,
	InvalidArgumentException;

/// The system shutdown handler.
class Shutdown implements HandlerIface
{
	/** @property $administratorEmail
	 *  @string The administrator's email address.
	 */
	protected $administratorEmail;

	/** @property $detailedInsecureMessage
	 *  @bool Whether to display a detailed insecure message.
	 */
	protected $detailedInsecureMessage;

	/** @property $writer
	 *  @object writer
	 */
	protected $writer;

	/** Construct the System Shutdown handler.
	 *  @param administratorEmail @string Admin's Email to use as a contact.
	 *  @param detailedInsecureMessage @bool Whether to show detailed logging
	 *  information (which is insecure).
	 *  @param writer @object The writer object to write the fatal message.
	 */
	public function __construct(/* String */ $administratorEmail,
	                            /* Bool   */ $detailedInsecureMessage,
	                            WriterIface  $writer)
	{
		if (!is_string($administratorEmail))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires administratorEmail to be a string');
		}
      
		if (!is_bool($detailedInsecureMessage))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires detailedInsecureMessage to be boolean');
		}

		$this->administratorEmail      = $administratorEmail;
		$this->detailedInsecureMessage = $detailedInsecureMessage;
		$this->writer                  = $writer;
	}

	/******************/
	/* Public Methods */
	/******************/

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
			'tell us any information that could help us avoid this error in the ' .
			'future.  Useful information such as the date, time and what you ' .
			'were doing when the error occurred should help us fix this.';

		if (!empty($this->administratorEmail))
		{
			$message .= "<br/>\n<br/>\n" .
				'Email us at ' . $this->administratorEmail;
		}
      
		if ($this->detailedInsecureMessage)
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
			            array('div', array('class' => 'Description'), $message))));
		$this->writer->writeEnd();
		$this->writer->output();
	}

	/// Register the shutdown handler.
	public function register()
	{
		register_shutdown_function(array($this, 'handler'));
	}

	/// Unregister the shutdown handler (which is not currently possible).
	public function unregister()
	{
		throw new BadMethodCallException(
			__METHOD__ . ' PHP does not have an unregister_shutdown_function.');
	}
}
// EOF