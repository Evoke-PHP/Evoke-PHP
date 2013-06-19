<?php
/**
 * Autoload Interface
 *
 * @package Service\Autoload
 */
namespace Evoke\Service\Autoload;

/**
 * Autoload Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Service\Autoload
 */
interface AutoloadIface
{
	/**
	 * Autoload the specified class.
	 *
	 * @param string The fully namespaced class to load.
	 */
	public function load(/* String */ $name);
		
	/**
	 * Register the Autoloader.
	 *
	 * @param bool Whether to throw an exception if the autoload function can't
	 *             be registered.
	 * @param bool Whether to prepend the autoloader to the autoload stack.
	 */
	public function register(/* Bool */ $throw   = true,
	                         /* Bool */ $prepend = false);

	/**
	 * Unregister the Autoloader.
	 */
	public function unregister();
}
// EOF