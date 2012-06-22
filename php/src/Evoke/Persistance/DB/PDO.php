<?php
namespace Evoke\Persistance\DB;

use InvalidArgumentException;

/**
 * PDO
 *
 * PDO wrapper class to ensure DB implements the Evoke DB interface.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistance
 */
class PDO extends \PDO implements DBIface
{
	/**
	 * Construct the PDO object.
	 *
	 * @param string  Data source name.
	 * @param string  Password.
	 * @param string  Username.
	 * @param mixed[] Options.
	 *
	 * @throw InvalidArgumentException	 
	 */
	public function __construct(
		/* String */ $dataSourceName,
		/* String */ $password,
		/* String */ $username,
		Array        $options=array(
			\PDO::ATTR_ERRMODE          => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_EMULATE_PREPARES => false,))
	{
		if (!is_string($dataSourceName))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires dsn as string');
		}

		if (!is_string($password))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires password as string');
		}

		if (!is_string($username))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires username as string');
		}

		parent::__construct($dataSourceName,
		                    $username,
		                    $password,
		                    $options);
	}
}
// EOF