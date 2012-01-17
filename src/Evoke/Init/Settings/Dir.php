<?php
namespace Evoke\Init\Settings;

/// Dir (directory) constants for the evoke system.
class Dir extends \Evoke\Settings
{
   /** Initialize the Dir (directory) constants for the evoke system:
       \verbatim
       DB_Incoming   - Incoming DB files.
       DB_Storage    - Stored DB files.
       Site_Includes - Site include files.
       Web_Root      - Website root.
       \endverbatim
  */
   public function __construct(Array $setup)
   {
      parent::__construct($setup);
      
      $this->set(
	 'Dir',
	 array('DB_Incoming'   => '/srv/db/incoming',
	       'DB_Storage'    => '/srv/db/storage',
	       'Site_Includes' => '/srv/site_lib',
	       'Web_Root'      => '/srv/web'));
   }
}
// EOF
