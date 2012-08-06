<?php
namespace Evoke\Service;

use DomainException,
	OverflowException;

/**
 * Service
 *
 * Control of the service objects.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
class Service implements ServiceIface
{
	/** 
	 * The cache for the shared services.  The cache stores the entries in the
	 * following format:
	 *
	 * <pre><code>
	 *	array('Service\Name'    => array(
	 *	          array('Object' => $objectInstance,
	 *                 'Params' => array($arg1, $arg2, $etc)),
	 *           array('Object' => $objectOther,
	 *                 'Params' => array($differentParamsSameObject))),
	 *       'Another\Service' => array())
	 * </code></pre>
	 *
	 * @var mixed[]
	 */
	protected $cache = array();
	
	/******************/
	/* Public Methods */
	/******************/
	
	/**
	 * Check whether there is a service object cached for the specified class
	 * and parameter combination.
	 *
	 * @param string  The name of the service (class or interface).
	 * @param mixed[] The construction parameters of the service.
	 *
	 * @return bool Whether the service object exists in the cache.
	 */
	public function exists($name, Array $params)
	{
		foreach ($this->cache[$name] as $service)
		{
			if ($service['Params'] === $params)
			{
				return true;
			}
		}

		return false;		
	}

	/**
	 * Get the service object cached for the specified class and parameter
	 * combination.  A check must already have been made using isService before
	 * calling this.
	 *
	 * @param string  The name of the service (class or interface).
	 * @param mixed[] The construction parameters of the service.
	 *
	 * @return mixed The service object.
	 *
	 * @throws DomainException If the service cannot be retrieved.
	 */
	public function get($name, $params)
	{
		if (!isset($this->cache[$name]))
		{
			throw new DomainException(
				__METHOD__ . ' service: ' . $name . ' is not registered.');
		}

		foreach ($this->cache[$name] as $service)
		{
			if ($service['Params'] === $params)
			{
				return $service['Object'];
			}
		}

		throw new DomainException(__METHOD__ . ' service has not been set.');
	}

	/**
	 * Check whether the named service has been registered.
	 *
	 * @param string The name of the service (class or interface).
	 *
	 * @return bool Whether the name is registered as a service.
	 */
	public function isService($name)
	{
		return isset($this->cache[$name]);
	}

	/**
	 * Register the name as a service, re-registering has no effect.
	 *
	 * @param string The name of the service (class or interface).
	 */
	public function register($name)
	{
		if (!isset($this->cache[$name]))
		{
			$this->cache[$name] = array();
		}			  
	}

	/**
	 * Set the service object for the named service with the specified
	 * parameters.
	 *
	 *  @param string  The name of the service (class or interface).
	 *  @param mixed   The object to be set as the service instance.
	 *  @param mixed[] The construction parameters of the service.
	 *
	 *  @throw OverflowException If the service has already been set.
	 *  @throw DomainException   If the named service is not a registered
	 *                           service.
	 */
	public function set(/* String */ $name,
	                    /* Object */ $object,
	                    Array        $params = array())
	{
		if (!isset($this->cache[$name]))
		{
			throw new DomainException(
				__METHOD__ . $name . ' is not a service.');
		}
		
		if ($this->exists($name, $params))
		{
			throw new OverflowException(
				__METHOD__ . ' service: ' . $name . ' already exists.');
		}

		$this->cache[$name][] = array('Object' => $object,
		                              'Params' => $params);
	}

	/**
	 * Unregister the name as a service (clearing all cached service objects for
	 * the named service. Unregistering a non-existant service has no effect.
	 *
	 * @param string The name of the service to unregister (class or interface).
	 */
	public function unregister($name)
	{
		unset($this->cache[$name]);
	}
}
// EOF