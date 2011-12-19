<?php

/** \file
 *  Define the file system locations used throughout the system.
 */

// Define the full file system directory for the Website base.
define('WEB_ROOT', NULL);

// Define the Database directory for linked files.
define('WEB_DB_ROOT', NULL);

/// The location of the site specific php includes directory.
define('PHP_SITE_SPECIFIC', WEB_ROOT . '/php/');

/** The location of the incoming directory for image management referenced from
 *  the server root.
 */
define('WEB_IMG_DB_INCOMING', WEB_DB_ROOT . '/Incoming/Img/');

/** The location of the storage directory for image management referenced from
 *  the server root.
 */
define('WEB_IMG_DB_STORAGE',  WEB_DB_ROOT . '/Storage/Img/');


/** The location of the incoming directory for image management.
 *  This is used while users are editing images in their sessions.
 */
define('IMG_DB_INCOMING', WEB_ROOT . WEB_IMG_DB_INCOMING);

   
/// The location of the storage directory for image management.
define('IMG_DB_STORAGE',  WEB_ROOT . WEB_IMG_DB_STORAGE);

/// The location of the language images directory referenced from the WEB_ROOT.
define('LANG_IMG_DIR', '/images/languages/');

// EOF
