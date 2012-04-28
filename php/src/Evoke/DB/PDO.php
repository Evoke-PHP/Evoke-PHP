<?php
namespace Evoke\DB;

/// PDO wrapper class to ensure DB implements the Evoke Core DB interface.
class PDO extends \PDO implements \Evoke\Iface\DB
{
	public function __construct(
		/* String */ $dsn,
		/* String */ $password,
		/* String */ $username,
		Array        $options=array(
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION))
	{
		if (!is_string($dsn))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires dsn as string');
		}

		if (!is_string($password))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires password as string');
		}

		if (!is_string($username))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires username as string');
		}

		parent::__construct($dSN,
		                    $username,
		                    $password,
		                    $options);
	}
}
// EOF