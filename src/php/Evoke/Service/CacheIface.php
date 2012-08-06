<?php
namespace Evoke\Service;

interface CacheIface
{
	/**
	 * Clear an item from the cache.
	 *
	 * @param string The key from the cache to clear.
	 */
	public function clear($key);

	/**
	 * Clear all items from the cache.
	 */
	public function clearAll();

	/**
	 * Check whether the key exists in the cache (the value could still be NULL)
	 *
	 *  @param string The key of the item to check for existance.
	 *
	 *  @return bool Whether the key exists in the cache.
	 */
	public function exists($key);
	
	/**
	 * Get an item from the cache (or if none has been defined return NULL).
	 *
	 * @param string|null The key for the item to get.
	 *
	 * @return mixed The value of the item.
	 *
	 * @throw DomainException when the key does not exist.
	 */
	public function get($key);

	/**
	 * Set an item in the cache.  If the value already exists it will be
	 * overwritten.
	 *
	 * @param string The key for the item to set.
	 * @param mixed  The value to set in the cache.
	 */
	public function set($key, $value);
}
// EOF