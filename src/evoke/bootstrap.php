<?php

/// Provide the bootstrapping for the system.
class Evoke_Bootstrap
{
   public function __construct()
   {
      // First get autoload up and running.
      require_once 'container.php';
      require_once 'evoke/handler.php';
      require_once 'evoke/handler/autoload.php';
      
      $c = new Container();
      $c->get('Evoke_Handler_Autoload');

      // Initialise the evoke system settings.
      $settings = $c->getShared('Settings');
      $c->get('Evoke_Initialize',
	      array('Container' => $c,
		    'Settings'  => $settings));
      $settings->freezeAll();
      
      
      // Provide a file logger for the system.
      $loggerFile = $c->getShared(
	 'Logger_File',
	 array('Event_Manager' => $c->getShared('Event_Manager'),
	       'File_System'   => $c->getShared('File_System'),
	       'Filename'      => LOG_FILE));

      $c->get('Evoke_Handler_Error',
	      array());
      $c->get('Evoke_Handler_Exception');
      $c->get('Evoke_Handler_Shutdown');
   }
}

$evokeBootstrap = new Evoke_Bootstrap();

// EOF