<?php
namespace Evoke\Persistence;

/**
 * SessionManager
 *
 * Provide management of a session domain.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistence
 */
class SessionManager implements SessionManagerIface
{
	/**
	 * The domain within the session that we are managing.  This is an ordered
	 * list of the keys required to reach the domain:
	 *
	 *     array('L1', 'L2', 'L3') == $_SESSION['L1']['L2']['L3']
	 *
	 * @var string[]
	 */
	protected $domain;

	/**
	 * Session object.
	 * @var Evoke\Persistence\SessionIface
	 */
	protected $session;

	/**
	 * Construct a Session Manager object.
	 *
	 * @param Evoke\Persistence\SessionIface Session
	 * @param string[]                       Domain to manage.
	 */
	public function __construct(SessionIface $session,
	                            Array        $domain)
	{
		$this->domain  = $domain;
		$this->session = $session;		
		
		$this->ensure();
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add a value to the array stored in the session domain.
	 *
	 * @param mixed The value to add to the session.
	 */
	public function addValue($value)
	{
		$session =& $this->getAccess();
		$session[] = $value;
	}

	/**
	 * Ensure the session is started and the session domain is set or created.
	 */
	public function ensure()
	{
		$this->session->ensure();
      
		// Make currentDomain a reference to $_SESSION so that when we change it
		// we are modifying the session.
		$currentDomain =& $_SESSION;

		foreach($this->domain as $subdomain)
		{
			if (!isset($currentDomain[$subdomain]))
			{
				$currentDomain[$subdomain] = array();
			}
	 
			// Update the currentDomain to reference the session subdomain.
			$currentDomain =& $currentDomain[$subdomain]; 
		}
	}

	/**
	 * Return the value of the key in the session domain.
	 *
	 * @param mixed The index of the value to retrieve.
	 *
	 * @return mixed The value from the session.
	 */
	public function get($key)
	{      
		$session = $this->getAccess();
		return $session[$key];
	}
   
	/**
	 * Get the session domain that we are managing and return a reference to it.
	 *
	 * @return mixed[] A reference to the session data.
	 */
	public function &getAccess()
	{
		// Set currentDomain to reference $_SESSION.
		$currentDomain =& $_SESSION;

		foreach($this->domain as $subdomain)
		{
			// Update the currentDomain to reference the session subdomain.
			$currentDomain =& $currentDomain[$subdomain]; 
		}

		return $currentDomain;
	}

	/**
	 * Return the domain as a flat array.
	 *
	 * @return string[]
	 */
	public function getFlatDomain()
	{
		return $this->domain;
	}
   
	/**
	 * Return the string of the session ID.
	 *
	 * @return string
	 */
	public function getID()
	{
		return $this->session->getID();
	}

	/**
	 * Increment the value in the session by the offset.
	 *
	 * @param mixed The session key to increment.
	 * @param int   The amount to increment the value.
	 */
	public function increment($key, $offset=1)
	{
		$session = $this->getAccess();
		$session[$key] += $offset;
	}
   
	/**
	 * Return whether the session domain is empty or not.
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		$session = $this->getAccess();
		return empty($session);
	}

	/**
	 * Return whether the key is set to the specified value.
	 *
	 * @param mixed The session key to check.
	 * @param mixed The value to check it against.
	 *
	 * @return bool
	 */
	public function isEqual($key, $val)
	{
		$session = $this->getAccess();
		return (isset($session[$key]) && ($session[$key] === $val));
	}
   
	/**
	 * Whether the key has been set in the session domain.
	 *
	 * @param mixed The session key to check.
	 *
	 * @return bool
	 */
	public function issetKey($key)
	{
		$session = $this->getAccess();
		return isset($session[$key]);
	}

	/**
	 * Return the number of keys stored by the session.
	 *
	 * @return int
	 */
	public function keyCount()
	{
		return count($this->getAccess());
	}
   
	/**
	 * Remove the session domain from the session.  This does not remove the
	 * hierarchy above the session domain.
	 */
	public function remove()
	{
		if (empty($this->domain))
		{
			session_unset();
		}
		else
		{
			// Set currentDomain to reference $_SESSION.
			$currentDomain =& $_SESSION;

			$previousSubdomain = $currentDomain;
	 
			foreach($this->domain as $subdomain)
			{
				// Update the currentDomain to reference the session subdomain.
				$previousSubdomain =& $currentDomain;
				$lastSubdomain = $subdomain;
				$currentDomain =& $currentDomain[$subdomain]; 
			}
	 
			unset($previousSubdomain[$lastSubdomain]);
		}
	}

	/**
	 * Remove all of the values in the session domain.
	 */
	public function removeValues()
	{
		$session =& $this->getAccess();
		$session = array();
	}

	/**
	 * Replace the session with the passed value.
	 *
	 * @param mixed The new value(s) for the session.
	 */
	public function replaceWith($newValue)
	{
		$session =& $this->getAccess();
		$session = $newValue;
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
	 * @param mixed The index in the session to set.
	 * @param mixed The value to set.
	 */
	public function set($key, $value)
	{
		$session =& $this->getAccess();
		$session[$key] = $value;
	}

	/**
	 * Unset the key in the session domain.
	 *
	 * @param mixed The index in the session to unset.
	 */
	public function unsetKey($key)
	{
		$session =& $this->getAccess();
		unset($session[$key]);
	}
}
// EOF