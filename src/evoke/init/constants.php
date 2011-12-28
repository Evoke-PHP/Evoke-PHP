<?php
/// \file Define constants for the system.

/// Default Language for the system.
define('DEFAULT_LANGUAGE', 'EN');

/** The development machine where name where logging should show exception and
 *  error information. You should override this files NULL value via a local
 *  modification or include path priority if you want to logging output visible
 *  within the pages that you create.
 */
define('DEVELOPMENT_SERVER', NULL);

/// The format for a DATETIME in mysql.
define('MYSQL_DATETIME_FORMAT', 'Y-m-d H:i:s');  

// EOF