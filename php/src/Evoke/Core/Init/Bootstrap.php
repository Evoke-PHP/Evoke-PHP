<?php
namespace Evoke\Core\Init;
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
      require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'ObjectHandler.php';
      require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Iface' .
	 DIRECTORY_SEPARATOR . 'Handler.php';
      require_once __DIR__ . DIRECTORY_SEPARATOR . 'Handler' .
	  DIRECTORY_SEPARATOR . 'Autoload.php';

      $objectHandler = new \Evoke\Core\ObjectHandler();
      $autoload = $objectHandler->getNew(
	 __NAMESPACE__ . '\Handler\Autoload',
	 array('Base_Dir'  => dirname(dirname(dirname(__DIR__))),
	       'Namespace' => 'Evoke\\'));
      $autoload->register();
   }
   
   public function initializeHandlers()
   {
      $objectHandler = new \Evoke\Core\ObjectHandler();
      $settings = $objectHandler->getShared('\Evoke\Core\Settings');
      $eventManager = $objectHandler->getShared('\Evoke\Core\EventManager');

      $isDevelopmentServer =
	 isset($settings['Constant']['Development_Servers']) &&
	 in_array(php_uname('n'), $settings['Constant']['Development_Servers']);

      // Register the Shutdown, Exception and Error handlers.
      $shutdownHandler = $objectHandler->getNew(
	 '\Evoke\Core\Init\Handler\Shutdown',
	 array('Administrator_Email'       => $settings[
		  'Email']['Administrator'],
	       'Detailed_Insecure_Message' => $isDevelopmentServer));
      $shutdownHandler->register();
      
      $exceptionHandler = $objectHandler->getNew(
	 '\Evoke\Core\Init\Handler\Exception',
	 array('Detailed_Insecure_Message'    => $isDevelopmentServer,
	       'EventManager'                 => $eventManager,
	       'Max_Length_Exception_Message' => $settings['Constant'][
		  'Max_Length_Exception_Message']));
      $exceptionHandler->register();

      $errorHandler = $objectHandler->getNew(
	 '\Evoke\Core\Init\Handler\Error',
	 array('Detailed_Insecure_Message' => $isDevelopmentServer,
	       'EventManager'              => $eventManager,
	       'XWR'                       => $objectHandler->getShared(
		  '\Evoke\Core\XWR')));
      $errorHandler->register();
   }

   public function initializeLogger()
   {
      $objectHandler = new \Evoke\Core\ObjectHandler();
      $objectHandler->getShared(
	 '\Evoke\Core\Logger',
	 array('EventManager' => $objectHandler->getShared(
		  '\Evoke\Core\EventManager'),
	       'ObjectHandler'    => $objectHandler));
   }

   public function initializeSettings()
   {
      $objectHandler = new \Evoke\Core\ObjectHandler();
      $settings = $objectHandler->getShared('\Evoke\Core\Settings');
      $settings->unfreezeAll();
      $settingsLoader = $objectHandler->getNew(
	 '\Evoke\Core\Init\Settings\Loader',
	 (array('Settings' => $settings)));
      $settingsLoader->load();
      $settings->freezeAll();
   }   
}
// EOF