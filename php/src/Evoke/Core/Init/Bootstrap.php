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

		$instanceManager = new \Evoke\Core\InstanceManager();
		$autoload = $instanceManager->create(
			__NAMESPACE__ . '\Handler\Autoload',
			array('Base_Dir'  => dirname(dirname(dirname(__DIR__))),
			      'Namespace' => 'Evoke\\'));
		$autoload->register();
	}
   
	public function initializeHandlers()
	{
		$instanceManager = new \Evoke\Core\InstanceManager();
		$settings = $instanceManager->get('\Evoke\Core\Settings');
		$eventManager = $instanceManager->get('\Evoke\Core\EventManager');

		$isDevelopmentServer =
			isset($settings['Constant']['Development_Servers']) &&
			in_array(php_uname('n'), $settings['Constant']['Development_Servers']);

		// Register the Shutdown, Exception and Error handlers.
		$shutdownHandler = $instanceManager->create(
			'\Evoke\Core\Init\Handler\Shutdown',
			array('Administrator_Email'       => $settings[
				      'Email']['Administrator'],
			      'Detailed_Insecure_Message' => $isDevelopmentServer));
		$shutdownHandler->register();
      
		$exceptionHandler = $instanceManager->create(
			'\Evoke\Core\Init\Handler\Exception',
			array('Detailed_Insecure_Message'    => $isDevelopmentServer,
			      'EventManager'                 => $eventManager,
			      'Max_Length_Exception_Message' => $settings['Constant'][
				      'Max_Length_Exception_Message']));
		$exceptionHandler->register();

		$errorHandler = $instanceManager->create(
			'\Evoke\Core\Init\Handler\Error',
			array('Detailed_Insecure_Message' => $isDevelopmentServer,
			      'EventManager'              => $eventManager,
			      'XWR'                       => $instanceManager->get(
				      '\Evoke\Core\XWR')));
		$errorHandler->register();
	}

	public function initializeLogger()
	{
		$instanceManager = new \Evoke\Core\InstanceManager();
		$instanceManager->get(
			'\Evoke\Core\Logger',
			array('EventManager'    => $instanceManager->get(
				      '\Evoke\Core\EventManager'),
			      'InstanceManager' => $instanceManager));
	}

	public function initializeSettings()
	{
		$instanceManager = new \Evoke\Core\InstanceManager();
		$settings = $instanceManager->get('\Evoke\Core\Settings');
		$settings->unfreezeAll();
		$settingsLoader = $instanceManager->create(
			'\Evoke\Core\Init\Settings\Loader',
			array('Settings' => $settings));
		$settingsLoader->load();
		$settings->freezeAll();
	}   
}
// EOF