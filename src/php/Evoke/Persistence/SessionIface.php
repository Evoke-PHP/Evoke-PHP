<?php
/**
 * Session Interface
 *
 * @package Persistence
 */
namespace Evoke\Persistence;

/**
 * Session Interface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistence
 */
interface SessionIface
{
	/**
	 * Add a value to the array stored in the session domain.
	 *
	 * @param mixed The value to add to the session.
	 */
	public function addValue($value);

	/**
	 * Delete the portion of the session stored at the offset.
	 *
	 * @param mixed[] The offset to the part of the session to delete.
	 */
	public function deleteAtOffset(Array $offset = array());
	
	/**
	 * Ensure the session is started and the session domain is set or created.
	 */
	public function ensure();

	/**
	 * Return the value of the key in the session domain.
	 *
	 * @param mixed The index of the value to retrieve.
	 *
	 * @return mixed The value from the session.
	 */
	public function get($key);
   
	/**
	 * Get a copy of the session domain that we are managing.
	 *
	 * @return mixed[] The sesssion data.
	 */
	public function getCopy();

	/**
	 * Get a copy of the data in the session at the offset specified.
	 *
	 * @param mixed[] The offset to the data.
	 */
	public function getAtOffset(Array $offset = array());
	
	/**
	 * Return the domain as a flat array.
	 *
	 * @return string[]
	 */
	public function getFlatDomain();
   
	/**
	 * Return the string of the session ID.
	 *
	 * @return string
	 */
	public function getID();

	/**
	 * Increment the value in the session by the offset.
	 *
	 * @param mixed The session key to increment.
	 * @param int   The amount to increment the value.
	 */
	public function increment($key, $offset=1);
   
	/**
	 * Return whether the session domain is empty or not.
	 *
	 * @return bool
	 */
	public function isEmpty();

	/**
	 * Return whether the key is set to the specified value.
	 *
	 * @param mixed The session key to check.
	 * @param mixed The value to check it against.
	 *
	 * @return bool
	 */
	public function isEqual($key, $val);

	/**
	 * Whether the key has been set in the session domain.
	 *
	 * @param mixed The session key to check.
	 *
	 * @return bool
	 */
	public function issetKey($key);

	/**
	 * Return the number of keys stored by the session.
	 *
	 * @return int
	 */
	public function keyCount();
   
	/**
	 * Remove the session domain from the session.  This does not remove the
	 * hierarchy above the session domain.
	 */
	public function remove();

	/**
	 * Reset the session to a blank start.
	 */
	public function reset();
   
	/**
	 * Set the value of the key in the session domain.
	 *
	 * @param mixed The index in the session to set.
	 * @param mixed The value to set.
	 */
	public function set($key, $value);

	/**
	 * Set the session to the specified data.
	 *
	 * @param mixed[]|mixed The new data to set the session to.
	 */
	public function setData($data);
	
	/**
	 * Unset the key in the session domain.
	 *
	 * @param mixed The index in the session to unset.
	 */
	public function unsetKey($key);
}
// EOF