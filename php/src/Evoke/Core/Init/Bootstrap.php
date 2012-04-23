<?php
namespace Evoke\Core\Init;
/** Provide the bootstrapping for the system.
 *  Start the autoloading of classes.
 *  Initialize the system settings.
 *  Register the Shutdown, Exception and Error handlers.
 */
class Bootstrap
{
	/** @property $provider
	 *  Provider \object that provides the objects for the system.
	 */
	protected $provider;
	
	public function __construct()
	{
		require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Iface' .
			DIRECTORY_SEPARATOR . 'Provider.php';
		require_once dirname(__DIR__) . DIRECTORY_SEPARATOR .
			'Provider.php';

		$this->provider = $this->buildProvider();
	}
   
	/******************/
	/* Public Methods */
	/******************/

	public function initializeAutoload()
	{
		require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Iface' .
			DIRECTORY_SEPARATOR . 'Handler.php';      
      
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'Handler' .
			DIRECTORY_SEPARATOR . 'Autoload.php';

		$autoload = $this->provider->build(
			__NAMESPACE__ . '\Handler\Autoload',
			array('Base_Dir'  => dirname(dirname(dirname(__DIR__))),
			      'Namespace' => 'Evoke\\'));
		$autoload->register();
	}
   
	public function initializeHandlers()
	{
		$settings = $instanceManager->get('\Evoke\Core\Settings');
		$writer = $instanceManager->get('\Evoke\Core\Writer\XHTML');

		/* ,
			array('XMLWriter' => $instanceManager->get('XMLWriter')));
		*/
		
		$isDevelopmentServer =
			isset($settings['Constant']['Development_Servers']) &&
			in_array(php_uname('n'),
			         $settings['Constant']['Development_Servers']);

		// Register the Shutdown, Exception and Error handlers.
		$shutdownHandler = $this->provider->build(
			'\Evoke\Core\Init\Handler\Shutdown',
			array('Administrator_Email'       => $settings['Email'][
				      'Administrator'],
			      'Detailed_Insecure_Message' => $isDevelopmentServer,
			      'Writer'                    => $writer));
		$shutdownHandler->register();
      
		$exceptionHandler = $this->provider->build(
			'\Evoke\Core\Init\Handler\Exception',
			array('Detailed_Insecure_Message'    => $isDevelopmentServer,
			      'Max_Length_Exception_Message' => $settings['Constant'][
				      'Max_Length_Exception_Message'],
			      'Writer'                       => $writer);
		$exceptionHandler->register();

		$errorHandler = $this->provider->build(
			'\Evoke\Core\Init\Handler\Error',
			array('Detailed_Insecure_Message' => $isDevelopmentServer,
			      'Writer'                    => $writer));
		$errorHandler->register();
	}

	public function InitializeProvider()
	{
		echo 'Initializing provider...';
		$this->provider->shareAll(
			array('\Evoke\Core\EventManager',
			      '\Evoke\Core\Logger',
			      '\Evoke\Core\Settings',
			      '\Evoke\Core\Writer\XHTML'
			      '\XMLWriter'));

		echo 'DONE';
	}		
	
	public function initializeLogger()
	{
		$this->provider->build('\Evoke\Core\Logger');
		/* ,
			array('DateTime'        => $instanceManager->get('DateTime'),
			      'EventManager'    => $instanceManager->get(
			      '\Evoke\Core\EventManager'))); */
	}

	public function initializeSettings()
	{
		/*
		$settings = $this->provider->build('\Evoke\Core\Settings');
		$settings->unfreezeAll();
		*/
		$settingsLoader = $this->provider->build(
			'\Evoke\Core\Init\Settings\Loader');
		$settingsLoader->load();
		$settings->freezeAll();
	}

	
	/*********************/
	/* Protected Methods */
	/*********************/

	protected function buildProvider()
	{
		return new \Evoke\Core\InstanceManager();
	}
}
// EOF