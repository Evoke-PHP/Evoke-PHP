<?php
namespace Evoke\Core\Init\Handler;

class Autoload implements \Evoke\Core\Iface\Handler
{
	/** @property $authoritative
	 *  Whether we have complete authority over the namespace, or we should allow
	 *  other autoloaders a chance to load the classes for our domain (if we are
	 *  not able to).
	 */
	protected $authoritative;

	/** @property $baseDir
	 *  The base directory for the files.
	 */
	protected $baseDir;

	/** @property $extension
	 *  The file extension to use.
	 */
	protected $extension;

	/** @property $namespace
	 *  The base namespace that we are autoloading.
	 */
	protected $namespace;

	public function __construct(/*s*/ $baseDir,
	                            /*s*/ $namespace,
	                            /*b*/ $authoritative=true,
	                            /*s*/ $extension='.php')
	{
		if (!is_string($baseDir))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires baseDir as string');
		}

		if (!is_string($namespace))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires namespace as string');
		}

		$this->authoritative = $authoritative;
		$this->baseDir       = $baseDir;
		$this->extension     = $extension;
		$this->namespace     = $namespace;
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
		if (substr($name, 0, strlen($this->namespace)) !== $this->namespace)
		{
			return;
		}

		$filename = $this->baseDir . DIRECTORY_SEPARATOR;
		$lastSlash = strrpos($name, '\\');
      
		if ($lastSlash === false)
		{
			$filename .= str_replace('_', DIRECTORY_SEPARATOR, $name);
		}
		else
		{
			$namespace = substr($name, 0, $lastSlash + 1);
			$className = substr($name, $lastSlash + 1);
			$filename .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) .
				str_replace('_', DIRECTORY_SEPARATOR, $className);
		}

		$filename .= $this->extension;

		if (file_exists($filename))
		{
			require $filename;
		}
		elseif ($this->authoritative)
		{
			// We are the authoritative autoloader for the namespace - If we can't
			// find it no-one can.
			throw new \RuntimeException(
				__METHOD__ . ' filename: ' . $filename . ' does not exist for ' .
				'authoritative autoloader.');
		}
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