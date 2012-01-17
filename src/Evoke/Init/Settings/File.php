<?php
namespace Evoke;

/// File constants for the evoke system.
class Init_File extends Init
{
   /** Initialize the file constants for the evoke system.
       \verbatim
       Log         - Log file.
       Translation - Translations File.
       \endverbatim
   */
   public function __construct(Array $setup)
   {
      parent::__construct($setup);

      $this->set(
	 'File',
	 array('Log'         => '/srv/log/log.txt',
	       'Translation' => '/srv/site_lib/evoke/translations.php'));
   }
}
// EOF
