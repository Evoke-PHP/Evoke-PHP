<?php
namespace Evoke\Core\DB;

/// PDO wrapper class to ensure DB implements the Evoke Core DB interface.
class PDO extends \PDO implements \Evoke\Core\Iface\DB
{
	public function __construct(Array $setup)
	{
		$setup += array(
			'DSN'      => NULL,
			'Options'  => array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION),
			'Password' => NULL,
			'Username' => NULL);

		if (!isset($dSN))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires DSN');
		}

		if (!isset($password))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Password');
		}

		if (!isset($username))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Username');
		}

		parent::__construct($dSN,
		                    $username,
		                    $password,
		                    $options);
	}
}
// EOF