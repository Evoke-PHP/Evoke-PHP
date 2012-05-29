<?php
namespace Evoke;

/** Control of service objects.
 */
class Services implements Iface\Services
{
	/** @property $cache
	 *  @array The cache for the shared services.  The cache stores the entries
	 *         in the following format:
	 *  @code
	 *	array('Service\Name'    => array(
	 *	          array('Object' => $objectInstance,
	 *                  'Params' => array($arg1, $arg2, $etc)),
	 *            array('Object' => $objectOther,
	 *                  'Params' => array($differentParamsSameObject))),
	 *        'Another\Service' => array())
	 *  @endcode
	 */
	protected $cache = array();
	
	/******************/
	/* Public Methods */
	/******************/
	
	/** Check whether there is a service object cached for the specified class
	 *  and parameter combination.
	 *  @param name   @string The name of the service (class or interface).
	 *  @param params @array  The construction parameters of the service.
	 *
	 *  @returns @bool Whether the service object exists in the cache.
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

	/** Get the service object cached for the specified class and parameter
	 *  combination.  A check must already have been made using isService before
	 *  calling this.
	 *  @param name   @string The name of the service (class or interface).
	 *  @param params @array  The construction parameters of the service.
	 *
	 *  @returns @object The service object.
	 *
	 *  @throws DomainException If the service cannot be retrieved.
	 */
	public function get($name, $params)
	{
		if (!isset($this->cache[$name]))
		{
			throw new \DomainException(
				__METHOD__ . ' service: ' . $name . ' is not registered.');
		}

		foreach ($this->cache[$name] as $service)
		{
			if ($service['Params'] === $params)
			{
				return $service['Object'];
			}
		}

		throw new \DomainException(__METHOD__ . ' service has not been set.');
	}

	/** Check whether the named service has been registered.
	 *  @param name @string The name of the service (class or interface).
	 *
	 *  @returns @bool Whether the name is registered as a service.
	 */
	public function isService($name)
	{
		return isset($this->cache[$name]);
	}

	/** Register the name as a service, re-registering has no effect.
	 *  @param name @string The name of the service (class or interface).
	 */
	public function register($name)
	{
		if (!isset($this->cache[$name]))
		{
			$this->cache[$name] = array();
		}			  
	}

	/** Set the service object for the named service with the specified
	 *  parameters.
	 *  @param name   @string The name of the service (class or interface).
	 *  @param object @object The object to be set as the service instance.
	 *  @param params @array  The construction parameters of the service.
	 *
	 *  @throws OverflowException If the service has already been set.
	 *  @throws DomainException   If the named service is not a registered
	 *                            service.
	 */
	public function set(/* String */ $name,
	                    /* Object */ $object,
	                    Array        $params = array())
	{
		if (!isset($this->cache[$name]))
		{
			throw new \DomainException(
				__METHOD__ . $name . ' is not a service.');
		}
		
		if ($this->exists($name, $params))
		{
			throw new \OverflowException(
				__METHOD__ . ' service: ' . $name . ' already exists.');
		}

		$this->cache[$name][] = array('Object' => $object,
		                              'Params' => $params);
	}

	/** Unregister the name as a service (clearing all cached service objects
	 *  for the named service. Unregistering a non-existant service has no
	 *  effect.
	 *  @param name @string The name of the service (class or interface).
	 */
	public function unregister($name)
	{
		unset($this->cache[$name]);
	}
}
// EOF