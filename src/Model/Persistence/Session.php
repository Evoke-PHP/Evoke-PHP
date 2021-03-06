<?php
declare(strict_types = 1);
/**
 * Session
 *
 * @package Model\Persistence
 */
namespace Evoke\Model\Persistence;

use RuntimeException;

/**
 * Session
 *
 * Provide persistence for a session domain.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Model\Persistence
 */
class Session implements SessionIface
{
    /**
     * The domain within the session that we are managing.  This is an ordered list of the keys required to reach the
     * domain:
     *
     *     `['k1', 'k2', 'k3'] == $_SESSION['k1']['k2']['k3']`
     *
     * @var string[]
     */
    protected $domain;

    /**
     * Construct the persistence for a session domain.
     *
     * @param string[] $domain Domain to manage.
     */
    public function __construct(Array $domain = [])
    {
        $this->domain = $domain;
        $this->ensure();
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Add a value to the array stored in the session domain.
     *
     * @param mixed $value The value to add to the session.
     */
    public function addValue($value)
    {
        $session   =& $this->getAccess();
        $session[] = $value;
    }

    /**
     * Delete the portion of the session stored at the offset.
     *
     * @param mixed[] $offset The offset to the part of the session to delete.
     */
    public function deleteAtOffset(Array $offset = [])
    {
        $sessionOffset =& $this->getAccess();

        foreach ($offset as $part) {
            if (!isset($sessionOffset[$part])) {
                // It is already deleted.
                return;
            }

            $sessionOffset =& $sessionOffset[$part];
        }

        $sessionOffset = [];
    }

    /**
     * Ensure the session is started and the session domain is set or created.
     *
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function ensure()
    {
        if (!isset($_SESSION)) {
            // If we are run from the command line interface then we do not care
            // about headers sent using the session_start.
            if (php_sapi_name() === 'cli') {
                $_SESSION = [];
            } elseif (!headers_sent()) {
                if (!session_start()) {
                    throw new RuntimeException('session_start failed.');
                }
            } else {
                throw new RuntimeException('session started after headers sent.');
            }
        }

        // Make currentDomain a reference to $_SESSION so that when we change it we are modifying the session.
        $currentDomain =& $_SESSION;

        foreach ($this->domain as $subdomain) {
            if (!isset($currentDomain[$subdomain])) {
                $currentDomain[$subdomain] = [];
            }

            // Update the currentDomain to reference the session subdomain.
            $currentDomain =& $currentDomain[$subdomain];
        }
    }

    /**
     * Return the value of the key in the session domain.
     *
     * @param string $key The index of the value to retrieve.
     * @return mixed The value from the session.
     */
    public function get(string $key)
    {
        $session = $this->getCopy();

        return $session[$key];
    }

    /**
     * Get a copy of the data in the session at the offset specified.
     *
     * @param mixed[] $offset The offset to the data.
     * @return mixed|null The data at the offset (NULL if the offset doesn't exist).
     */
    public function getAtOffset(Array $offset = [])
    {
        $sessionOffset = $this->getCopy();

        foreach ($offset as $part) {
            // If there is no data at the offset return NULL.
            if (!isset($sessionOffset[$part])) {
                return null;
            }

            $sessionOffset = $sessionOffset[$part];
        }

        return $sessionOffset;
    }

    /**
     * Get a copy of the session domain that we are managing.
     *
     * @return mixed[] The session data.
     *
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getCopy()
    {
        $currentDomain = $_SESSION;

        foreach ($this->domain as $subdomain) {
            // Update the currentDomain to reference the session subdomain.
            $currentDomain = $currentDomain[$subdomain];
        }

        return $currentDomain;
    }

    /**
     * Return the domain as a flat array.
     *
     * @return string[]
     */
    public function getFlatDomain() : array
    {
        return $this->domain;
    }

    /**
     * Return the string of the session ID.
     *
     * @return string
     */
    public function getID() : string
    {
        return (PHP_SAPI === 'cli') ? 'CLI_SESSION' : session_id();
    }

    /**
     * Increment the value in the session by the offset.
     *
     * @param string $key    The session key to increment.
     * @param int    $offset The amount to increment the value.
     */
    public function increment(string $key, int $offset = 1)
    {
        $session =& $this->getAccess();
        $session[$key] += $offset;
    }

    /**
     * Return whether the session domain is empty or not.
     *
     * @return bool
     */
    public function isEmpty() : bool
    {
        $session = $this->getCopy();

        return empty($session);
    }

    /**
     * Return whether the key is set to the specified value.
     *
     * @param string $key The session key to check.
     * @param mixed $val The value to check it against.
     * @return bool
     */
    public function isEqual(string $key, $val) : bool
    {
        $session = $this->getCopy();

        return (isset($session[$key]) && ($session[$key] === $val));
    }

    /**
     * Whether the key has been set in the session domain.
     *
     * @param string $key The session key to check.
     * @return bool
     */
    public function issetKey(string $key) : bool
    {
        $session = $this->getCopy();

        return isset($session[$key]);
    }

    /**
     * Return the number of keys stored by the session.
     *
     * @return int
     */
    public function keyCount() : int
    {
        return count($this->getCopy());
    }

    /**
     * Remove the session domain from the session.  This does not remove the hierarchy above the session domain.
     *
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function remove()
    {
        if (empty($this->domain)) {
            session_unset();
        } else {
            // Set currentDomain to reference $_SESSION.
            $currentDomain     =& $_SESSION;
            $previousSubdomain = $currentDomain;
            $lastSubdomain     = null;

            foreach ($this->domain as $subdomain) {
                // Update the currentDomain to reference the session subdomain.
                $previousSubdomain =& $currentDomain;
                $lastSubdomain     = $subdomain;
                $currentDomain     =& $currentDomain[$subdomain];
            }

            unset($previousSubdomain[$lastSubdomain]);
        }
    }

    /**
     * Reset the session to a blank start.
     */
    public function reset()
    {
        $this->remove();
        $this->ensure();
    }

    /**
     * Set the value of the key in the session domain.
     *
     * @param string $key   The index in the session to set.
     * @param mixed  $value The value to set.
     */
    public function set(string $key, $value)
    {
        $session       =& $this->getAccess();
        $session[$key] = $value;
    }

    /**
     * Set the session to the specified data.
     *
     * @param mixed $data The new data to set the session to.
     */
    public function setData($data)
    {
        $session =& $this->getAccess();
        $session = $data;
    }

    /**
     * Set the session data at an offset.
     *
     * @param mixed   $data   The data to set.
     * @param mixed[] $offset The offset to set the data at.
     */
    public function setDataAtOffset($data, Array $offset = [])
    {
        $sessionOffset =& $this->getAccess();

        foreach ($offset as $part) {
            // If there is offset is not already set then set it.
            if (!isset($sessionOffset[$part])) {
                $sessionOffset[$part] = [];
            }

            $sessionOffset = &$sessionOffset[$part];
        }

        $sessionOffset = $data;
    }

    /**
     * Unset the key in the session domain.
     *
     * @param string $key The index in the session to unset.
     */
    public function unsetKey(string $key)
    {
        $session =& $this->getAccess();
        unset($session[$key]);
    }

    /*********************/
    /* Protected Methods */
    /*********************/

    /**
     * Get the session domain that we are managing and return a reference to it.
     *
     * @return mixed[] A reference to the session data.
     *
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function &getAccess()
    {
        // Set currentDomain to reference $_SESSION.
        $currentDomain =& $_SESSION;

        foreach ($this->domain as $subdomain) {
            // Update the currentDomain to reference the session subdomain.
            $currentDomain =& $currentDomain[$subdomain];
        }

        return $currentDomain;
    }
}
// EOF
