<?php
namespace Evoke\DB;

/// PDO_Wrapped class to ensure DB implements DB interface.
class PDO_Wrapped extends \PDO implements \Evoke\Iface\DB
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