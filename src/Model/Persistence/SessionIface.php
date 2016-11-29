<?php
declare(strict_types = 1);
/**
 * Session Interface
 *
 * @package Model\Persistence
 */
namespace Evoke\Model\Persistence;

/**
 * Session Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Model\Persistence
 */
interface SessionIface
{
    /**
     * Add a value to the array stored in the session domain.
     *
     * @param mixed $value The value to add to the session.
     */
    public function addValue($value);

    /**
     * Delete the portion of the session stored at the offset.
     *
     * @param mixed[] $offset The offset to the part of the session to delete.
     */
    public function deleteAtOffset(Array $offset = []);

    /**
     * Ensure the session is started and the session domain is set or created.
     */
    public function ensure();

    /**
     * Return the value of the key in the session domain.
     *
     * @param string $key The index of the value to retrieve.
     * @return mixed The value from the session.
     */
    public function get(string $key);

    /**
     * Get a copy of the session domain that we are managing.
     *
     * @return mixed[] The session data.
     */
    public function getCopy() : array;

    /**
     * Get a copy of the data in the session at the offset specified.
     *
     * @param mixed[] $offset The offset to the data.
     */
    public function getAtOffset(Array $offset = []);

    /**
     * Return the domain as a flat array.
     *
     * @return string[]
     */
    public function getFlatDomain() : array;

    /**
     * Return the string of the session ID.
     *
     * @return string
     */
    public function getID() : string;

    /**
     * Increment the value in the session by the offset.
     *
     * @param string $key    The session key to increment.
     * @param int    $offset The amount to increment the value.
     */
    public function increment(string $key, int $offset = 1);

    /**
     * Return whether the session domain is empty or not.
     *
     * @return bool
     */
    public function isEmpty() : bool;

    /**
     * Return whether the key is set to the specified value.
     *
     * @param string $key The session key to check.
     * @param mixed $val The value to check it against.
     * @return bool
     */
    public function isEqual(string $key, $val) : bool;

    /**
     * Whether the key has been set in the session domain.
     *
     * @param string $key The session key to check.
     * @return bool
     */
    public function issetKey(string $key) : bool;

    /**
     * Return the number of keys stored by the session.
     *
     * @return int
     */
    public function keyCount() : int;

    /**
     * Remove the session domain from the session.  This does not remove the hierarchy above the session domain.
     */
    public function remove();

    /**
     * Reset the session to a blank start.
     */
    public function reset();

    /**
     * Set the value of the key in the session domain.
     *
     * @param string $key   The index in the session to set.
     * @param mixed  $value The value to set.
     */
    public function set(string $key, $value);

    /**
     * Set the session to the specified data.
     *
     * @param mixed $data The new data to set the session to.
     */
    public function setData($data);

    /**
     * Set the session data at an offset.
     *
     * @param mixed   $data   The data to set.
     * @param mixed[] $offset The offset to set the data at.
     */
    public function setDataAtOffset($data, Array $offset = []);

    /**
     * Unset the key in the session domain.
     *
     * @param string $key The index in the session to unset.
     */
    public function unsetKey(string $key);
}
// EOF
