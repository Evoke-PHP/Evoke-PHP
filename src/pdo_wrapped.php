<?php


/// PDO_Wrapped class to ensure DB implements DB interface.
class PDO_Wrapped extends PDO implements Iface_DB
{
   public function __construct(Array $setup)
   {
      $setup += array(
	 'App'      => NULL,
	 'DSN'      => NULL,
	 'Options'  => array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION),
	 'Password' => NULL,
	 'Username' => NULL);

      $setup['App']->needs(
	 array('Set' => array('DSN'      => $setup['DSN'],
			      'Password' => $setup['Password'],
			      'Username' => $setup['Username'])));

      parent::__construct($setup['DSN'],
			  $setup['Username'],
			  $setup['Password'],
			  $setup['Options']);
   }
}

// EOF