<?php
namespace Evoke\Service;

interface ServiceIface
{
	/**
	 * Check whether there is a service object cached for the specified class
	 * and parameter combination.
	 *
	 * @param string  The name of the service (class or interface).
	 * @param mixed[] The construction parameters of the service.
	 *
	 * @return bool Whether the service object exists in the cache.
	 */
	public function exists($name, Array $params);

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
	public function get($name, $params);
		
	/**
	 * Check whether the named service has been registered.
	 *
	 * @param string The name of the service (class or interface).
	 *
	 * @return bool Whether the name is registered as a service.
	 */
	public function isService($name);

	/**
	 * Register the name as a service, re-registering has no effect.
	 *
	 * @param string The name of the service (class or interface).
	 */
	public function register($name);

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
	                    Array        $params = array());

	/**
	 * Unregister the name as a service (clearing all cached service objects for
	 * the named service. Unregistering a non-existant service has no effect.
	 *
	 * @param string The name of the service to unregister (class or interface).
	 */
	public function unregister($name);
}
// EOF