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
		require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Iface' .
			DIRECTORY_SEPARATOR . 'Handler.php';      
		require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Iface' .
			DIRECTORY_SEPARATOR . 'InstanceManager.php';
      
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'Handler' .
			DIRECTORY_SEPARATOR . 'Autoload.php';
		require_once dirname(__DIR__) . DIRECTORY_SEPARATOR .
			'InstanceManager.php';

		$InstanceManager = new \Evoke\Core\InstanceManager();
		$autoload = $InstanceManager->create(
			__NAMESPACE__ . '\Handler\Autoload',
			array('Base_Dir'  => dirname(dirname(dirname(__DIR__))),
			      'Namespace' => 'Evoke\\'));
		$autoload->register();
	}
   
	public function initializeHandlers()
	{
		$InstanceManager = new \Evoke\Core\InstanceManager();
		$settings = $InstanceManager->get('\Evoke\Core\Settings');
		$eventManager = $InstanceManager->get('\Evoke\Core\EventManager');

		$isDevelopmentServer =
			isset($settings['Constant']['Development_Servers']) &&
			in_array(php_uname('n'), $settings['Constant']['Development_Servers']);

		// Register the Shutdown, Exception and Error handlers.
		$shutdownHandler = $InstanceManager->create(
			'\Evoke\Core\Init\Handler\Shutdown',
			array('Administrator_Email'       => $settings[
				      'Email']['Administrator'],
			      'Detailed_Insecure_Message' => $isDevelopmentServer));
		$shutdownHandler->register();
      
		$exceptionHandler = $InstanceManager->create(
			'\Evoke\Core\Init\Handler\Exception',
			array('Detailed_Insecure_Message'    => $isDevelopmentServer,
			      'EventManager'                 => $eventManager,
			      'Max_Length_Exception_Message' => $settings['Constant'][
				      'Max_Length_Exception_Message']));
		$exceptionHandler->register();

		$errorHandler = $InstanceManager->create(
			'\Evoke\Core\Init\Handler\Error',
			array('Detailed_Insecure_Message' => $isDevelopmentServer,
			      'EventManager'              => $eventManager,
			      'XWR'                       => $InstanceManager->get(
				      '\Evoke\Core\XWR')));
		$errorHandler->register();
	}

	public function initializeLogger()
	{
		$InstanceManager = new \Evoke\Core\InstanceManager();
		$InstanceManager->get(
			'\Evoke\Core\Logger',
			array('EventManager'    => $InstanceManager->get(
				      '\Evoke\Core\EventManager'),
			      'InstanceManager' => $InstanceManager));
	}

	public function initializeSettings()
	{
		$InstanceManager = new \Evoke\Core\InstanceManager();
		$settings = $InstanceManager->get('\Evoke\Core\Settings');
		$settings->unfreezeAll();
		$settingsLoader = $InstanceManager->create(
			'\Evoke\Core\Init\Settings\Loader',
			array('Settings' => $settings));
		$settingsLoader->load();
		$settings->freezeAll();
	}   
}
// EOF