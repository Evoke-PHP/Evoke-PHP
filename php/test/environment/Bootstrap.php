<?php
class Bootstrap
{
	protected $EvokeBootstrap;
	protected $InstanceManager;
   
	/// Bring up the libraries that we use and the Rosalia autoloader.
	public function __construct()
	{
		if (php_uname('n') === 'bernie')
		{
			$baseDir = '/home/pyoung/Coding/';
			$evokeDir = $baseDir . 'Evoke-PHP/php/src/Evoke/';
		}

		require_once $evokeDir . 'Core/Iface/InstanceManager.php';
		require_once $evokeDir . 'Core/InstanceManager.php';
		$this->InstanceManager = new \Evoke\Core\InstanceManager();
      
		require_once $evokeDir . 'Core/Init/Bootstrap.php';
		$this->EvokeBootstrap = $this->InstanceManager->create(
			'\Evoke\Core\Init\Bootstrap');
	}

	/******************/
	/* Public Methods */
	/******************/

	public function initializeAutoload()
	{
		$this->EvokeBootstrap->initializeAutoload();
	}

	/*
	  public function initializeHandlers()
	  {
	  $this->EvokeBootstrap->initializeHandlers();
	  }
	*/
   
	public function initializeLogger()
	{
		$this->EvokeBootstrap->initializeLogger();
	}
   
	public function initializeSettings()
	{
		$this->EvokeBootstrap->initializeSettings();
	}
}

$Bootstrap = new Bootstrap();
$Bootstrap->initializeAutoload();
$Bootstrap->initializeSettings();
$Bootstrap->initializeLogger();
// EOF
