<?php
class Bootstrap
{
   protected $evokeBootstrap;
   protected $evokeMoreBootstrap;
   protected $objectCreator;
   protected $objectHandler;
   
   /// Bring up the libraries that we use and the Rosalia autoloader.
   public function __construct()
   {
      if (php_uname('n') === 'bernie')
      {
	 $baseDir = '/home/pyoung/Coding/';
	 $evokeDir = $baseDir . 'Evoke-PHP-Framework/php/src/Evoke/';
      }

      require_once $evokeDir . 'Core/Iface/InstanceManager.php';
      require_once $evokeDir . 'Core/InstanceManager.php';
      $this->instanceManager = new \Evoke\Core\InstanceManager();
      
      require_once $evokeDir . 'Core/Init/Bootstrap.php';
      $this->evokeBootstrap = $this->instanceManager->create(
	 '\Evoke\Core\Init\Bootstrap');
   }

   /******************/
   /* Public Methods */
   /******************/

   public function initializeAutoload()
   {
      $this->evokeBootstrap->initializeAutoload();
   }

   /*
   public function initializeHandlers()
   {
      $this->evokeBootstrap->initializeHandlers();
   }
   */
   
   public function initializeLogger()
   {
      $this->evokeBootstrap->initializeLogger();
   }
   
   public function initializeSettings()
   {
      $this->evokeBootstrap->initializeSettings();
   }
}

$bootstrap = new Bootstrap();
$bootstrap->initializeAutoload();
$bootstrap->initializeSettings();
$bootstrap->initializeLogger();
// EOF
