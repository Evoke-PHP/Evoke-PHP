<?php
namespace Evoke\Persistence;

use RuntimeException;

/**
 * Session
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistence
 */
class Session implements SessionIface
{
	/**
	 * Construct a session object.
	 */
	public function __construct()
	{
		$this->ensure();
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Ensure that the session is initialized.
	 */
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
					throw new RuntimeException(
						__METHOD__ . ' session_start failed.');
				}
			}
			else
			{
				throw new RuntimeException(
					__METHOD__ . ' Session started after headers sent.');
			}
		}
	}

	/**
	 * Return the string of the session ID.
	 *
	 * @return string
	 */
	public function getID()
	{
		return (PHP_SAPI === 'cli') ? 'CLI_SESSION' : session_id();
	}
}
// EOF