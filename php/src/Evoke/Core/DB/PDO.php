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

		if (!isset($setup['DSN']))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires DSN');
		}

		if (!isset($setup['Password']))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Password');
		}

		if (!isset($setup['Username']))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Username');
		}

		parent::__construct($setup['DSN'],
		                    $setup['Username'],
		                    $setup['Password'],
		                    $setup['Options']);
	}
}
// EOF