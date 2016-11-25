<?php
declare(strict_types = 1);
/**
 * PSR0Namespace Autoload
 *
 * @package Service\Autoload
 */
namespace Evoke\Service\Autoload;

/**
 * PSR0Namespace Autoload
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Service\Autoload
 */
class PSR0Namespace implements AutoloadIface
{
    /**
     * Base directory for the files.
     *
     * @var string
     */
    protected $baseDir;

    /**
     * File extension to use.
     *
     * @var string
     */
    protected $extension;

    /**
     * Base namespace that we are autoloading with slash at the end.
     *
     * @var string
     */
    protected $nsWithSlash;

    /**
     * Minimum length of name required to load.
     *
     * @var int
     */
    private $nameMinLen;

    /**
     * Length of the namespace with slash.
     *
     * @var int
     */
    private $nsWithSlashLen;

    /**
     * Construct an Autoload object.
     *
     * @param string $baseDir
     * @param string $namespace
     * @param string $extension
     */
    public function __construct(
        $baseDir,
        $namespace,
        $extension = '.php'
    ) {
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
     * @param string $name The fully namespaced class to load.
     */
    public function load($name)
    {
        // Only handle the specified namespace (and its sub-namespaces).
        if (strlen($name) >= $this->nameMinLen &&
            substr($name, 0, $this->nsWithSlashLen) !== $this->nsWithSlash
        ) {
            return;
        }

        // Name has a slash because we checked it against nsWithSlash.
        $lastSlash = strrpos($name, '\\');
        $namespace = substr($name, 0, $lastSlash + 1);
        $className = substr($name, $lastSlash + 1);
        $filename  = $this->baseDir . DIRECTORY_SEPARATOR .
            str_replace('\\', DIRECTORY_SEPARATOR, $namespace) .
            str_replace('_', DIRECTORY_SEPARATOR, $className) .
            $this->extension;

        if (file_exists($filename)) {
            /** @noinspection PhpIncludeInspection */
            require $filename;
        }
    }
}
// EOF
