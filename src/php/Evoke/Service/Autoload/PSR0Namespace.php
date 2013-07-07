<?php
/**
 * PSR0Namespace Autoload
 *
 * @package Service\Autoload
 */
namespace Evoke\Service\Autoload;

/**
 * PSR0Namespace Autoload
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Service\Autoload
 */
class PSR0Namespace implements AutoloadIface
{
	/**
	 * Protected Properties
	 * 
	 * @var string $baseDir     Base directory for the files.
	 * @var string $extension   File extension to use.
	 * @var string $nsWithSlash Base namespace that we are autoloading with
	 *                          slash at the end.
	 */
	protected $baseDir, $extension, $nsWithSlash;

	/**
	 * Private Properties
	 *
	 * @var int $nameMinLen     Minimum length of name required to load.
	 * @var int $nsWithSlashLen Length of the namespace with slash.
	 */
	private $nameMinLen, $nsWithSlashLen;
	
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
		$this->baseDir     = rtrim($baseDir, DIRECTORY_SEPARATOR);
		$this->extension   = $extension;
		$this->nsWithSlash = rtrim($namespace, '\\') . '\\';

		$this->nsWithSlashLen = strlen($this->nsWithSlash);
		$this->nameMinLen     = $this->nsWithSlashLen + 1;
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
		if (strlen($name) >= $this->nameMinLen &&
		    substr($name, 0, $this->nsWithSlashLen) !== $this->nsWithSlash)
		{
			return;
		}

		// Name has a slash because we checked it against nsWithSlash.
		$lastSlash = strrpos($name, '\\');
		$namespace = substr($name, 0, $lastSlash + 1);
		$className = substr($name, $lastSlash + 1);
		$filename = $this->baseDir . DIRECTORY_SEPARATOR .
			str_replace('\\', DIRECTORY_SEPARATOR, $namespace) .
			str_replace('_', DIRECTORY_SEPARATOR, $className) .
			$this->extension;

		if (file_exists($filename))
		{	
			require $filename;
		}
	}	
}
// EOF