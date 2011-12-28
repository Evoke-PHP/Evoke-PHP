<?php

/// Initialize the file constants for the evoke system.
class Evoke_Init_Files extends Evoke_Init
{
   /** Define the file constants:
       
       DIR - Directories (listed from the root directory).
       \verbatim
       DB_INCOMING   - Incoming DB files.
       DB_STORAGE    - Stored DB files.
       SITE_INCLUDES - Site include files.
       WEB_ROOT      - Website root.
       \endverbatim

       FILES - Files.
       \verbatim
       LOG_FILE          - Log file.
       NO_PHOTO          - No Photo Image File.
       TRANSLATIONS_FILE - Translations File.
       \endverbatim
       
       WEB - Web locations.
       \verbatim
       DB_INCOMING  - Incoming DB files.
       DB_STORAGE   - Stored DB files.
       LANG_IMG_DIR - Languages images directory.
       \endverbatim
   */
   public function __construct(Array $setup)
   {
      parent::__construct($setup);
      
      $this->setSettings(
	 array('DB_ROOT'         => '',
	       'DB_INCOMING'     => '',
	       'DB_STORAGE'      => '',
	       'PHP_SITE_LIB'    => WEB_ROOT . '/php/',
	       'WEB_DB_INCOMING' =>
	       'WEB_ROOT'        => '',
	       );
      
      /** The location of the incoming directory for image management referenced from
       *  the server root.
       */
      $this->set('WEB_IMG_DB_INCOMING', WEB_DB_ROOT . '/Incoming/Img/');
      
      /** The location of the storage directory for image management referenced from
       *  the server root.
       */
      $this->set('WEB_IMG_DB_STORAGE',  WEB_DB_ROOT . '/Storage/Img/');
      
      
      /** The location of the incoming directory for image management.
       *  This is used while users are editing images in their sessions.
       */
      $this->set('IMG_DB_INCOMING', WEB_ROOT . WEB_IMG_DB_INCOMING);
      
      
      /// The location of the storage directory for image management.
      $this->set('IMG_DB_STORAGE',  WEB_ROOT . WEB_IMG_DB_STORAGE);
      
      /// The location of the language images directory referenced from the WEB_ROOT.
      $this->set('LANG_IMG_DIR', '/images/languages/');
      
      $this->set('LOG_FILE', WEB_ROOT . WEB_DB_ROOT . '/Logs/evoke.log');
      $this->set('TRANSLATIONS_FILE', 'system/translations.php');
      
      $this->set('NO_PHOTO', '/images/No_Photo/No_Photo_%LANG%.png');
   }

   /******************/
   /* Public Methods */
   /******************/

}
// EOF
