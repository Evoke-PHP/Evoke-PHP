<?php
namespace Evoke\Service\Handler;

use InvalidArgumentException,
	RuntimeException;

/**
 * Autoload
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
class Autoload implements HandlerIface
{
	/** 
	 * Whether we have complete authority over the namespace, or we should allow
	 * other autoloaders a chance to load the classes for our domain (if we are
	 * not able to).  This gives us the opportunity to throw an exception and
	 * avoid the __fatal error__ that is *almost* sure to follow an unloaded
	 * class.
	 * @var bool
	 */
	protected $authoritative;

	/**
	 * The base directory for the files.
	 * @var string
	 */
	protected $baseDir;

	/**
	 * The file extension to use.
	 * @var string
	 */
	protected $extension;

	/**
	 * The base namespace that we are autoloading.
	 * @var string
	 */
	protected $namespace;

	/**
	 * Construct an Autoload object.
	 *
	 * @param string Base directory.
	 * @param string Namespace.
	 * @param bool   Authoritative
	 * @param string Extension
	 */
	public function __construct(/* String */ $baseDir,
	                            /* String */ $namespace,
	                            /* Bool   */ $authoritative=true,
	                            /* String */ $extension='.php')
	{
		if (!is_string($baseDir))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires baseDir as string');
		}

		if (!is_string($namespace))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires namespace as string');
		}

		$this->authoritative = $authoritative;
		$this->baseDir       = rtrim($baseDir, DIRECTORY_SEPARATOR);
		$this->extension     = $extension;
		$this->namespace     = $namespace;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Provide the autoload function.
	 *
	 * @param string The full specification of the class being autoloaded.
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
			trigger_error(
				__METHOD__ . ' Authoritative autoloader can\'t load: ' .
				$filename);
			// We are the authoritative autoloader for the namespace - If we
			// can't find it no-one can.
			throw new RuntimeException(
				__METHOD__ . ' filename: ' . $filename .
				' does not exist for authoritative autoloader.');
		}
	}

	/**
	 * Register the handler.
	 */
	public function register()
	{
		spl_autoload_register(array($this, 'handler'), true);
	}

	/**
	 * Unregister the handler
	 */
	public function unregister()
	{
		if (!spl_autoload_unregister())
		{
			throw new RuntimeException(
				__METHOD__ . ' spl_autoload_unregister failed.');
		}
	}
}
// EOF