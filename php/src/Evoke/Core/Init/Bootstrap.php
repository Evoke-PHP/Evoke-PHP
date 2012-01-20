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
      require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Container.php';
      require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Iface' .
	 DIRECTORY_SEPARATOR . 'Handler.php';
      require_once __DIR__ . DIRECTORY_SEPARATOR . 'Handler' .
	  DIRECTORY_SEPARATOR . 'Autoload.php';

      $container = new \Evoke\Core\Container();
      $autoload = $container->getNew(
	 __NAMESPACE__ . '\Handler\Autoload',
	 array('Base_Dir'  => dirname(dirname(dirname(__DIR__))),
	       'Namespace' => 'Evoke\\'));
      $autoload->register();
   }
   
   public function initializeHandlers()
   {
      $container = new \Evoke\Core\Container();
      $settings = $container->getShared('\Evoke\Core\Settings');
      $eventManager = $container->getShared('\Evoke\Core\EventManager');

      $isDevelopmentServer =
	 isset($settings['Constant']['Development_Servers']) &&
	 in_array(php_uname('n'), $settings['Constant']['Development_Servers']);

      // Register the Shutdown, Exception and Error handlers.
      $shutdownHandler = $container->getNew(
	 '\Evoke\Core\Init\Handler\Shutdown',
	 array('Administrator_Email'       => $settings[
		  'Email']['Administrator'],
	       'Detailed_Insecure_Message' => $isDevelopmentServer));
      $shutdownHandler->register();
      
      $exceptionHandler = $container->getNew(
	 '\Evoke\Core\Init\Handler\Exception',
	 array('Detailed_Insecure_Message'    => $isDevelopmentServer,
	       'EventManager'                 => $eventManager,
	       'Max_Length_Exception_Message' => $settings['Constant'][
		  'Max_Length_Exception_Message']));
      $exceptionHandler->register();

      $errorHandler = $container->getNew(
	 '\Evoke\Core\Init\Handler\Error',
	 array('Detailed_Insecure_Message' => $isDevelopmentServer,
	       'EventManager'              => $eventManager,
	       'XWR'                       => $container->getShared(
		  '\Evoke\Core\XWR')));
      $errorHandler->register();
   }

   public function initializeLogger()
   {
      $container = new \Evoke\Core\Container();
      $container->getShared(
	 '\Evoke\Core\Logger',
	 array('Container'    => $container,
	       'EventManager' => $container->getShared(
		  '\Evoke\Core\EventManager')));
   }

   public function initializeSettings()
   {
      $container = new \Evoke\Core\Container();
      $settings = $container->getShared('\Evoke\Core\Settings');
      $settings->unfreezeAll();
      $settingsLoader = $container->getNew('\Evoke\Core\Init\Settings\Loader',
					   (array('Settings' => $settings)));
      $settingsLoader->load();
      $settings->freezeAll();
   }   
}
// EOF