<?php
namespace Evoke\Init;
/** Provide the bootstrapping for the system.
 *  Start the autoloading of classes.
 *  Initialize the system settings.
 *  Register the Shutdown, Exception and Error handlers.
 */
class Bootstrap
{
   public function __construct()
   {
   }
   
   /******************/
   /* Public Methods */
   /******************/

   public function initializeAutoload()
   {
      require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Container.php';
      require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Iface' .
	 DIRECTORY_SEPARATOR . 'Handler.php';
      require_once __DIR__ . DIRECTORY_SEPARATOR . 'Handler' .
	  DIRECTORY_SEPARATOR . 'Autoload.php';

      $container = new \Evoke\Container();
      $autoload = $container->getNew(
	 __NAMESPACE__ . '\Handler\Autoload',
	 array('Base_Dir'  => dirname(dirname(__DIR__)),
	       'Namespace' => 'Evoke\\'));
      $autoload->register();
   }
   
   public function initializeHandlers()
   {
      $container = new \Evoke\Container();
      $settings = $container->getShared('\Evoke\Settings');
      $eventManager = $container->getShared('\Evoke\Event_Manager');

      $isDevelopmentServer =
	 isset($settings['Constant']['Development_Servers']) &&
	 in_array(php_uname('n'), $settings['Constant']['Development_Servers']);

      // Register the Shutdown, Exception and Error handlers.
      $shutdownHandler = $container->getNew(
	 '\Evoke\Init\Handler\Shutdown',
	 array('Administrator_Email'       => $settings[
		  'Email']['Administrator'],
	       'Detailed_Insecure_Message' => $isDevelopmentServer));
      $shutdownHandler->register();
      
      $exceptionHandler = $container->getNew(
	 '\Evoke\Init\Handler\Exception',
	 array('Detailed_Insecure_Message'    => $isDevelopmentServer,
	       'Event_Manager'                => $eventManager,
	       'Max_Length_Exception_Message' => $settings['Constant'][
		  'Max_Length_Exception_Message']));
      $exceptionHandler->register();

      $errorHandler = $container->getNew(
	 '\Evoke\Init\Handler\Error',
	 array('Detailed_Insecure_Message' => $isDevelopmentServer,
	       'Event_Manager'             => $eventManager,
	       'XWR'                       => $container->getShared(
		  '\Evoke\XWR')));
      $errorHandler->register();
   }

   public function initializeLogger()
   {
      $container = new \Evoke\Container();
      $container->getShared(
	 '\Evoke\Logger',
	 array('Container'     => $container,
	       'Event_Manager' => $container->getShared(
		  '\Evoke\Event_Manager')));
   }

   public function initializeSettings()
   {
      $container = new \Evoke\Container();
      $settings = $container->getShared('\Evoke\Settings');
      $settings->unfreezeAll();
      $settingsLoader = $container->getNew('\Evoke\Init\Settings_Loader',
					   (array('Settings' => $settings)));
      $settingsLoader->load();
      $settings->freezeAll();
   }   
}
// EOF