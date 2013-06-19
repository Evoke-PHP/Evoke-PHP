<?php
/**
 * Autoload
 *
 * @package Service
 */
namespace Evoke\Service\Autoload;

use RuntimeException;

/**
 * Autoload
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Service
 */
abstract class Autoload implements AutoloadIface
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Register the Autoloader.
	 *
	 * @param bool Whether to throw an exception if the autoload function can't
	 *             be registered.
	 * @param bool Whether to prepend the autoloader to the autoload stack.
	 */
	public function register(/* Bool */ $throw   = true,
	                         /* Bool */ $prepend = false)
	{
		spl_autoload_register(array($this, 'load'), $throw, $prepend);
	}

	/**
	 * Unregister the Autoloader.
	 */
	public function unregister()
	{
        if (!spl_autoload_unregister(array($this, 'load')))
        {
            throw new RuntimeException('spl_autoload_unregister failed.');
        }
	}
}
// EOF