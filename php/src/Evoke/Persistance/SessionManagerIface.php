<?php
namespace Evoke\Persistance;

/**
 * SessionManagerIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistance
 */
interface SessionManagerIface
{
	/**
	 * Add a value to the array stored in the session domain.
	 *
	 * @param mixed The value to add to the session.
	 */
	public function addValue($value);

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
	 * Get the session domain that we are managing and return a reference to it.
	 *
	 * @return mixed[] A reference to the session data.
	 */
	public function &getAccess();

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
	 * Return whether the key is set to the specified value.
	 *
	 * @param mixed The session key to check.
	 * @param mixed The value to check it against.
	 *
	 * @return bool
	 */
	public function is($key, $val);

	/**
	 * Return whether the session domain is empty or not.
	 *
	 * @return bool
	 */
	public function isEmpty();

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
	 * Remove all of the values in the session domain.
	 */
	public function removeValues();

	/**
	 * Replace the session with the passed value.
	 *
	 * @param mixed The new value(s) for the session.
	 */
	public function replaceWith($newValue);
   
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
	 * Unset the key in the session domain.
	 *
	 * @param mixed The index in the session to unset.
	 */
	public function unsetKey($key);
}
// EOF