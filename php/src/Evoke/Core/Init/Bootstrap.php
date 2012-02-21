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
		$Autoload = $InstanceManager->create(
			__NAMESPACE__ . '\Handler\Autoload',
			array('Base_Dir'  => dirname(dirname(dirname(__DIR__))),
			      'Namespace' => 'Evoke\\'));
		$Autoload->register();
	}
   
	public function initializeHandlers()
	{
		$InstanceManager = new \Evoke\Core\InstanceManager();
		$Settings = $InstanceManager->get('\Evoke\Core\Settings');
		$EventManager = $InstanceManager->get('\Evoke\Core\EventManager');

		$isDevelopmentServer =
			isset($Settings['Constant']['Development_Servers']) &&
			in_array(php_uname('n'), $Settings['Constant']['Development_Servers']);

		// Register the Shutdown, Exception and Error handlers.
		$ShutdownHandler = $InstanceManager->create(
			'\Evoke\Core\Init\Handler\Shutdown',
			array('Administrator_Email'       => $Settings[
				      'Email']['Administrator'],
			      'Detailed_Insecure_Message' => $isDevelopmentServer));
		$ShutdownHandler->register();
      
		$ExceptionHandler = $InstanceManager->create(
			'\Evoke\Core\Init\Handler\Exception',
			array('Detailed_Insecure_Message'    => $isDevelopmentServer,
			      'Event_Manager'                 => $EventManager,
			      'Max_Length_Exception_Message' => $Settings['Constant'][
				      'Max_Length_Exception_Message']));
		$ExceptionHandler->register();

		$ErrorHandler = $InstanceManager->create(
			'\Evoke\Core\Init\Handler\Error',
			array('Detailed_Insecure_Message' => $isDevelopmentServer,
			      'Event_Manager'              => $EventManager,
			      'XWR'                       => $InstanceManager->get(
				      '\Evoke\Core\XWR')));
		$ErrorHandler->register();
	}

	public function initializeLogger()
	{
		$InstanceManager = new \Evoke\Core\InstanceManager();
		$InstanceManager->get(
			'\Evoke\Core\Logger',
			array('Date_Time'        => $InstanceManager->get('Date_Time'),
			      'Event_Manager'    => $InstanceManager->get(
				      '\Evoke\Core\EventManager')));
	}

	public function initializeSettings()
	{
		$InstanceManager = new \Evoke\Core\InstanceManager();
		$Settings = $InstanceManager->get('\Evoke\Core\Settings');
		$Settings->unfreezeAll();
		$SettingsLoader = $InstanceManager->create(
			'\Evoke\Core\Init\Settings\Loader',
			array('Settings' => $Settings));
		$SettingsLoader->load();
		$Settings->freezeAll();
	}   
}
// EOF