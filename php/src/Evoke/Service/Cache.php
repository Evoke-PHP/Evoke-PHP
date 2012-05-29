<?php
namespace Evoke\Service;

use DomainException;

class Cache implements CacheIface
{
	/** @property $items
	 *  @array Collection of cache entries.
	 */
	protected $items = array();

	/******************/
	/* Public Methods */
	/******************/

	/** Clear an item from the cache.
	 *  @param key @string The key from the cache to clear.
	 */
	public function clear($key)
	{
		unset($this->items[$key]);
	}

	/// Clear all items from the cache.	
	public function clearAll()
	{
		$this->items = array();
	}

	/** Check whether the key exists in the cache (the value could still be
	 *  NULL).
	 *  @param key @string The key of the item to check for existance.
	 *  @return @bool Whether the key exists in the cache.
	 */
	public function exists($key)
	{
		// We can store NULL in the cache, so we must use array_key_exists.
		return array_key_exists($key, $this->items);
	}
	
	/** Get an item from the cache (or if none has been defined return NULL).
	 *  @param key @string The key for the item to get.
	 *  @return @mixed The value of the item.
	 *  @throws DomainException when the key does not exist.
	 */
	public function get($key)
	{
		// We can store (and return) NULL using the cache so we must use
		// array_key_exists.
		if (!array_key_exists($key, $this->items))
		{
			throw new DomainException(
				__METHOD__ . ' key: ' . $key . ' does not exist.  You must ' .
				'check for values before you get them.');
		}

		return $this->items[$key];
	}

	/** Set an item in the cache.  If the value already exists it will be
	 *  overwritten.
	 *  @param key   @string The key for the item to set.
	 *  @param value @mixed  The value to set in the cache.
	 */
	public function set($key, $value)
	{
		$this->items[$key] = $value;
	}
}
// EOF