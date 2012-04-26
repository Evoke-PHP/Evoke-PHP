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
		$this->provider = $this->buildProvider();
	}
   
	/******************/
	/* Public Methods */
	/******************/

	public function initializeAutoload()
	{
		require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Iface' .
			DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Init' .
			DIRECTORY_SEPARATOR . 'Handler.php';
      
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'Handler' .
			DIRECTORY_SEPARATOR . 'Autoload.php';

		$autoload = $this->provider->make(
			__NAMESPACE__ . '\Handler\Autoload',
			array('Base_Dir'  => dirname(dirname(dirname(__DIR__))),
			      'Namespace' => 'Evoke\\'));
		$autoload->register();
	}
   
	public function initializeHandlers()
	{
		$settings = $this->provider->make('\Evoke\Core\Settings');
		$writer = $this->provider->make('\Evoke\Core\Writer\XHTML');

		$isDevelopmentServer =
			isset($settings['Constant']['Development_Servers']) &&
			in_array(php_uname('n'),
			         $settings['Constant']['Development_Servers']);

		// Register the Shutdown, Exception and Error handlers.
		$shutdownHandler = $this->provider->make(
			'\Evoke\Core\Init\Handler\Shutdown',
			array('Administrator_Email'       => $settings['Email'][
				      'Administrator'],
			      'Detailed_Insecure_Message' => $isDevelopmentServer,
			      'Writer'                    => $writer));
		$shutdownHandler->register();
      
		$exceptionHandler = $this->provider->make(
			'\Evoke\Core\Init\Handler\Exception',
			array('Detailed_Insecure_Message'    => $isDevelopmentServer,
			      'Max_Length_Exception_Message' => $settings['Constant'][
				      'Max_Length_Exception_Message'],
			      'Writer'                       => $writer));
		$exceptionHandler->register();

		$errorHandler = $this->provider->make(
			'\Evoke\Core\Init\Handler\Error',
			array('Detailed_Insecure_Message' => $isDevelopmentServer,
			      'Writer'                    => $writer));
		$errorHandler->register();
	}

	public function initializeProvider()
	{
		$this->provider->share('\Evoke\Core\EventManager');
		$this->provider->share('\Evoke\Core\Logger');
		$this->provider->share('\Evoke\Core\Settings');
		$this->provider->share('\XMLWriter');
	}		
	
	public function initializeLogger()
	{
		$this->provider->make('\Evoke\Core\Logger');
	}

	public function initializeSettings()
	{
		$settings = $this->provider->make('\Evoke\Core\Settings');
		$settings->unfreezeAll();
		$settingsLoader = $this->provider->make(
			'\Evoke\Core\Init\Settings\Loader',
			array('Settings' => $settings));
		$settingsLoader->load();
		$settings->freezeAll();
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/** Build the Provider for dependency injection.
	 */
	protected function buildProvider()
	{
		require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Iface' .
			DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Provider.php';
		require_once dirname(__DIR__) . DIRECTORY_SEPARATOR .
			'Provider.php';

		return new \Evoke\Core\Provider;
	}
}
// EOF