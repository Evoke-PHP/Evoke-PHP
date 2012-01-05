<?php
/// Provide the bootstrapping for the system.
class Evoke_Bootstrap
{
   public function __construct()
   {
      // First get autoload handler up and running.
      require_once 'container.php';
      require_once 'evoke/handler.php';
      require_once 'evoke/handler/autoload.php';
      
      $c = new Container();
      $c->getNew('Evoke_Handler_Autoload');

      // Initialise the evoke system settings.
      $settings = $c->getShared('Settings', array('Separator' => ':'));
      $c->getNew('Evoke_Initialize',
		 array('Container' => $c,
		       'Settings'  => $settings));
      $settings->freezeAll();

      $em = $c->getShared('Event_Manager');

      $isDevelopmentServer =
	 isset($settings['Constant']['Development_Servers']) &&
	 in_array(php_uname('n'), $settings['Constant']['Development_Servers']);
      
      // Register all of the other handlers.
      $c->getNew('Evoke_Handler_Shutdown',
		 array('Administrator_Email'       => $settings[
			  'Email']['Administrator'],
		       'Detailed_Insecure_Message' => $isDevelopmentServer));
   
      $c->getNew('Evoke_Handler_Exception',
		 array('Detailed_Insecure_Message' => $isDevelopmentServer,
		       'Event_Manager'             => $em));

      $c->getNew('Evoke_Handler_Error',
		 array('Detailed_Insecure_Message' => $isDevelopmentServer,
		       'Event_Manager'             => $em,
		       'XWR'                       => $c->getShared('XWR')));
   }
}

$evokeBootstrap = new Evoke_Bootstrap();
// EOF