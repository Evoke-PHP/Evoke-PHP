<?php
namespace Evoke\DB;

/// PDO wrapper class to ensure DB implements the Evoke Core DB interface.
class PDO extends \PDO implements \Evoke\Iface\DB
{
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

		parent::__construct($dataSourceName,
		                    $username,
		                    $password,
		                    $options);
	}
}
// EOF