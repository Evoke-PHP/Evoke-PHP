<?php
/**
 * StaticMap Autoloader
 *
 * @package Service
 */
namespace Evoke\Service\Autoload;

use RuntimeException;

/**
 * StaticMap Autoloader
 *
 * Autoload using a fixed mapping of classnames to filenames.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
class StaticMap extends Autoload
{
	/**
	 * The static map of classnames to filenames.
	 * @var string[]
	 */
	protected $staticMap;

	/**
	 * Construct an Autoload object.
	 *
	 * @param string[] The static map of classnames to filenames.
	 */
	public function __construct(Array $staticMap)
	{
		$this->staticMap = $staticMap;
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
		if (isset($this->staticMap[$name]))
		{
			if (!file_exists($this->staticMap[$name]))
			{
				throw new RuntimeException('File: ' $this->staticMap[$name] .
				                           ' does not exist.');
			}
			
			require $this->staticMap[$name];
		}
	}	
}
// EOF