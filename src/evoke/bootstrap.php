<?php
/** Provide the bootstrapping for the system.
 *  Start the autoloading of classes.
 *  Initialize the system settings.
 *  Register the Shutdown, Exception and Error handlers.
 */
class Evoke_Bootstrap
{
   public function __construct()
   {
      // Get the autoload up and running so that we don't need requires.
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
      
      // Register the Shutdown, Exception and Error handlers.
      $c->getNew('Evoke_Handler_Shutdown',
		 array('Administrator_Email'       => $settings[
			  'Email']['Administrator'],
		       'Detailed_Insecure_Message' => $isDevelopmentServer));

      $c->getNew('Evoke_Handler_Exception',
		 array('Detailed_Insecure_Message'    => $isDevelopmentServer,
		       'Event_Manager'                => $em,
		       'Max_Length_Exception_Message' => $settings['Constant'][
			  'Max_Length_Exception_Message']));

      $c->getNew('Evoke_Handler_Error',
		 array('Detailed_Insecure_Message' => $isDevelopmentServer,
		       'Event_Manager'             => $em,
		       'XWR'                       => $c->getShared('XWR')));
   }
}

$evokeBootstrap = new Evoke_Bootstrap();
// EOF