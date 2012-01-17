<?php
namespace Evoke\Init\Settings;

/// DB connection settings for the evoke system.
class DB extends \Evoke\Init\Settings
{
   /** Initialize the DB connections for the evoke system. This should be an
    *  array of connection settings.  Each item in the array should be of the
    *  form:       
       \verbatim
       DSN      - Data Source Name.
       Options  - Options for passing to PDO.
       Password - Password.
       Username - Username.
       \endverbatim
   */
   public function __construct(Array $setup)
   {
      parent::__construct($setup);

      // No database connections by default.  Create your own in your site
      // specific area at a higher priority php_include_path.
      $this->set('DB', array());
   }
}
// EOF
