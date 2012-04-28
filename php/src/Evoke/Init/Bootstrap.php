<?php
namespace Evoke\Init;
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
		$autoload = $this->buildAutoload();
		$autoload->register();
	}
   
	public function initializeHandlers()
	{
		$settings = $this->provider->make('\Evoke\Settings');
		$writer = $this->provider->make('\Evoke\Writer\XHTML');

		$isDevelopmentServer =
			isset($settings['Constant']['Development_Servers']) &&
			in_array(php_uname('n'),
			         $settings['Constant']['Development_Servers']);

		// Register the Shutdown, Exception and Error handlers.
		$shutdownHandler = $this->provider->make(
			'\Evoke\Init\Handler\Shutdown',
			array('Administrator_Email'       => $settings['Email'][
				      'Administrator'],
			      'Detailed_Insecure_Message' => $isDevelopmentServer,
			      'Writer'                    => $writer));
		$shutdownHandler->register();
      
		$exceptionHandler = $this->provider->make(
			'\Evoke\Init\Handler\Exception',
			array('Detailed_Insecure_Message'    => $isDevelopmentServer,
			      'Max_Length_Exception_Message' => $settings['Constant'][
				      'Max_Length_Exception_Message'],
			      'Writer'                       => $writer));
		$exceptionHandler->register();

		$errorHandler = $this->provider->make(
			'\Evoke\Init\Handler\Error',
			array('Detailed_Insecure_Message' => $isDevelopmentServer,
			      'Writer'                    => $writer));
		$errorHandler->register();
	}

	public function initializeProvider()
	{
		$this->provider->share('\Evoke\EventManager');
		$this->provider->share('\Evoke\Logger');
		$this->provider->share('\Evoke\Settings');
		$this->provider->share('\XMLWriter');
	}		
	
	public function initializeLogger()
	{
		$this->provider->make('\Evoke\Logger');
	}

	public function initializeSettings()
	{
		$settings = $this->provider->make('\Evoke\Settings');
		$settings->unfreezeAll();
		$settingsLoader = $this->provider->make(
			'\Evoke\Init\Settings\Loader',
			array('Settings' => $settings));
		$settingsLoader->load();
		$settings->freezeAll();
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	protected function buildAutoload()
	{
		$DS = DIRECTORY_SEPARATOR;
		
		require_once dirname(__DIR__) . $DS . 'Iface' . $DS . 'Init' .
			$DS . 'Handler.php';
		require_once __DIR__ . $DS . 'Handler' . $DS . 'Autoload.php';
		$autoloadClass = __NAMESPACE__ . '\Handler\Autoload';

		return new $autoloadClass(
			dirname(dirname(dirname(__DIR__))), // baseDir
			'Evoke\\');                         // namespace
	}

	/** Build the Provider for dependency injection.
	 */
	protected function buildProvider()
	{
		$DS = DIRECTORY_SEPARATOR;

		$Iface = dirname(__DIR__) . $DS . 'Iface' . $DS;
		
		require_once $Iface . 'Provider.php';
		require_once $Iface . 'Provider' . $DS . 'Iface' . $DS . 'Router.php';
		require_once $Iface . 'Provider' . $DS . 'Iface' . $DS . 'Rule.php';
		
		require_once dirname(__DIR__) . $DS . 'Provider.php';

		$interfaceRouter = new \Evoke\Provider\Iface\Router;
		$interfaceRouter->addRule(
			new \Evoke\Provider\Iface\Rule\StrReplace('\Iface\\', '\\'));
		return new \Evoke\Provider($interfaceRouter);
	}
}
// EOF