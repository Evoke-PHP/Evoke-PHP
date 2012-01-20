<?php
namespace Evoke\Core\Init\Handler;

class Autoload implements \Evoke\Core\Iface\Handler
{
   protected $setup;

   public function __construct(Array $setup=array())
   {
      /** The setup for the Autoload.
	  \verbatim
	  Authorative - Whether we should allow other autoloaders a chance to
	                load the classes for our domain.
	  Base_Dir    - The base directory for the files.
	  Extension   - The file extension to use.
	  Namespace   - The base namespace that we are autoloading.
	  \endverbatim
      */
      $this->setup = array_merge(array('Authorative' => true,
				       'Base_Dir'    => NULL,
				       'Extension'   => '.php',
				       'Namespace'   => NULL),
				 $setup);

      if (!is_string($this->setup['Base_Dir']))
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Base_Dir as string');
      }

      if (!is_string($this->setup['Namespace']))
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Namespace as string');
      }
   }

   /******************/
   /* Public Methods */
   /******************/

   /** Provide the autoload function.
    *  @param name \string The full specification of the class being autoloaded.
    */
   public function handler($name)
   {
      // Only handle the specified namespace (and its subnamespaces).
      if (substr($name, 0, strlen($this->setup['Namespace'])) !==
	  $this->setup['Namespace'])
      {
	 return;
      }

      $filename = $this->setup['Base_Dir'] . DIRECTORY_SEPARATOR;
      $lastSlash = strrpos($name, '\\');
      
      if ($lastSlash === false)
      {
	 $filename .= str_replace('_', DIRECTORY_SEPARATOR, $name);

	 if (strstr($name, 'DB_Joint') !== false)
	 {
	    echo 'FFS: ' . $filename . "\n<br>";
	 }
      }
      else
      {
	 $namespace = substr($name, 0, $lastSlash + 1);
	 $className = substr($name, $lastSlash + 1);
	 $filename .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) .
	    str_replace('_', DIRECTORY_SEPARATOR, $className);

	 if (strstr($name, 'DB_Joint') !== false)
	 {
	    echo 'NS: ' . $namespace .' ' . 'CL: ' . $className . "\n<br>";
	 }

      }

      $filename .= $this->setup['Extension'];

      // echo 'FFS: ' . $filename . "\n<br>";
      
      if (!file_exists($filename) && $this->setup['Authorative'])
      {
	 // We are the authorative autoloader for the namespace - If we can't
	 // find it no-one can.
	 throw new \RuntimeException(
	    __METHOD__ . ' filename: ' . $filename . ' does not exist for ' .
	    'authorative autoloader.');
      }
      
      require $filename;
   }
   
   public function register()
   {
      if (!spl_autoload_register(array($this, 'handler')))
      {
	 throw new \RuntimeException(
	    __METHOD__ . ' spl_autoload_register failed.');
      }
   }

   public function unregister()
   {
      if (!spl_autoload_unregister())
      {
	 throw new \RuntimeException(
	    __METHOD__ . ' spl_autoload_unregister failed.');
      }
   }
}
// EOF