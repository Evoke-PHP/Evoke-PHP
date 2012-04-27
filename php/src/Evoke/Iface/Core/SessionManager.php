<?php
namespace Evoke\Iface\Core;

interface SessionManager
{
	/// Add a value to the array stored in the session domain.
	public function addValue($value);

	/// Ensure the session is started and the session domain is set or created.
	public function ensure();

	/// Return the value of the key in the session domain.
	public function get($key);
   
	/// Get the session domain that we are managing and return a reference to it.
	public function &getAccess();

	/// Return the domain as a flat array.
	public function getFlatDomain();
   
	/// Return the string of the session ID.
	public function getID();

	/** Increment the value in the session by the offset.
	 *  @param key \string The session key to increment.
	 *  @param offset \int The amount to increment the value.
	 */
	public function increment($key, $offset=1);
   
	/// Return whether the key is set to the specified value.
	public function is($key, $val);

	/// Return whether the session domain is empty or not.
	public function isEmpty();
   
	/// Whether the key has been set in the session domain.
	public function issetKey($key);

	/// Return the number of keys stored by the session.
	public function keyCount();
   
	/** Remove the session domain from the session.
	 *  This does not remove the hierarchy above the session domain.
	 */
	public function remove();

	/// Remove all of the values in the session domain.
	public function removeValues();

	/** Replace the session with the passed value.
	 *  @param newValue @mixed The new value(s) for the session.
	 */
	public function replaceWith($newValue);

    /// Reset the session to a blank start.
	public function reset();
   
	/// Set the value of the key in the session domain.
	public function set($key, $value);

	/// Unset the key in the session domain.
	public function unsetKey($key);
}
// EOF