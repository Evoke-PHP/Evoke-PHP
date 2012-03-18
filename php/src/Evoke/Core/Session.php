<?php
namespace Evoke\Core;

class Session implements Iface\Session
{
	public function __construct()
	{
		$this->ensure();
	}

	/******************/
	/* Public Methods */
	/******************/

	public function ensure()
	{
		if (!isset($_SESSION))
		{
			// If we are run from the command line interface then we do not care
			// about headers sent using the session_start.
			if (PHP_SAPI === 'cli')
			{
				$_SESSION = array();
			}
			elseif (!headers_sent())
			{
				if (!session_start())
				{
					throw new \RuntimeException(
						__METHOD__ . ' session_start failed.');
				}
			}
			else
			{
				throw new \RuntimeException(
					__METHOD__ . ' Session started after headers sent.');
			}
		}
	}

	/// Return the string of the session ID.
	public function getID()
	{
		if (PHP_SAPI === 'cli')
		{
			return 'CLI_SESSION';
		}
		else
		{
			return session_id();
		}
	}
}
// EOF