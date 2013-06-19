<?php
/**
 * PSR0Namespace Autoload
 *
 * @package Service
 */
namespace Evoke\Service\Autoload;

/**
 * PSR0Namespace Autoload
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Service
 */
class PSR0Namespace extends Autoload
{
	protected
		/**
		 * The base directory for the files.
		 * @var string
		 */
		$baseDir,

		/**
		 * The file extension to use.
		 * @var string
		 */
		$extension,

		/**
		 * The base namespace that we are autoloading.
		 * @var string
		 */
		$namespace;

	/**
	 * Construct an Autoload object.
	 *
	 * @param string Base directory.
	 * @param string Namespace.
	 * @param string Extension
	 */
	public function __construct(/* String */ $baseDir,
	                            /* String */ $namespace,
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

		$this->baseDir   = rtrim($baseDir, DIRECTORY_SEPARATOR);
		$this->extension = $extension;
		$this->namespace = $namespace;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Autoload the specified class.
	 *
	 * @param string The fully namespaced class to load.
	 */
	public function load(/* String */ $name)
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
	}	
}
// EOF