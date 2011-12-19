<?php

/// Provide the bootstrapping for the system.
class System_Bootstrap
{
   public function __construct()
   {
      // First we need to get autoload up and running.
      require_once 'container.php';
      require_once 'system/handler.php';
      require_once 'system/handler/autoload.php';
      
      $c = new Container();
      $c->get('System_Handler_Autoload');

      $c->get('System_Handler_Error');
      $c->get('System_Handler_Exception');
      $c->get('System_Handler_Shutdown');

      // Provide a file logger for the system.
      require_once 'system/files.php';

      $loggerFile = $c->getShared(
	 'Logger_File',
	 array('Event_Manager' => $c->getShared('Event_Manager'),
	       'File_System'   => $c->getShared('File_System'),
	       'Filename'      => LOG_FILE));
   }
}

$systemBootstrap = new System_Bootstrap();

// EOF