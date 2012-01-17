<?php
namespace Evoke;

/// Web location constants for the evoke system.
class Init_Web extends Init
{
   /** Initialize the web locations for the evoke system.
       \verbatim
       DB_Incoming    - Incoming DB files.
       DB_Storage     - Stored DB files.
       Lang_Image_Dir - Languages images directory.
       No_Photo       - No Photo Image File.
       \endverbatim
   */
   public function __construct(Array $setup)
   {
      parent::__construct($setup);
      
      $this->set(
	 'Web',
	 array('DB_Incoming'     => '/db/incoming/',
	       'DB_Storage'     => '/db/storage/',
	       'Lang_Image_Dir' => 'images/languages/',
	       'No_Photo'       => 'images/No_Photo/No_Photo_%LANG%.png'));
   }
}
// EOF
   