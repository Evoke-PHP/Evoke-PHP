<?php
namespace Evoke\Iface;

interface Services
{
	/** Check whether there is a service object cached for the specified class
	 *  and parameter combination.
	 *  @param name   @string The name of the service (class or interface).
	 *  @param params @array  The construction parameters of the service.
	 *  @returns @bool Whether the service object exists in the cache.
	 */
	public function exists($name, Array $params);

	/** Get the service object cached for the specified class and parameter
	 *  combination.  A check must already have been made using isService before
	 *  calling this.
	 *  @param name   @string The name of the service (class or interface).
	 *  @param params @array  The construction parameters of the service.
	 *  @returns @object The service object.
	 *  @throws DomainException If the service cannot be retrieved.
	 */
	public function get($name, $params);

	/** Check whether the named service has been registered.
	 *  @param name @string The name of the service (class or interface).
	 *  @returns @bool Whether the name is registered as a service.
	 */
	public function isService($name);

	/** Register the name as a service.
	 *  @param name @string The name of the service (class or interface).
	 */
	public function register($name);

	/** Set the service object for the named service with the specified
	 *  parameters.
	 *  @param name   @string The name of the service (class or interface).
	 *  @param params @array  The construction parameters of the service.
	 *  @param object @object The object to be set as the service instance.
	 *  @throws DomainException   If the named service is not a registered
	 *                            service.
	 *  @throws OverflowException If the service has already been set.
	 */
	public function set($name, Array $params, $object);
	
	/** Unregister the name as a service (clearing all cached service objects
	 *  for the named service.
	 */
	public function unregister($name);
}
// EOF