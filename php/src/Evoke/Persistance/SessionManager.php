<?php
namespace Evoke;

/// Session_Manager provide management of a session domain.
class SessionManager implements Iface\SessionManager
{
	/** @property $domain
	 *  @array The domain within the session that we are managing.  This is an
	 *  ordered list of the keys required to reach the domain:
	 *  `array('L1', 'L2', 'L3') == $_SESSION['L1']['L2']['L3']`
	 */
	protected $domain;

	/** @property $session
	 *  @object Session
	 */
	protected $session;
   
	public function __construct(Iface\Session $session,
	                            Array         $domain)
	{
		$this->domain  = $domain;
		$this->session = $session;		
		
		$this->ensure();
	}

	/******************/
	/* Public Methods */
	/******************/

	/// Add a value to the array stored in the session domain.
	public function addValue($value)
	{
		$session =& $this->getAccess();
		$session[] = $value;
	}

	/// Ensure the session is started and the session domain is set or created.
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

	/// Return the value of the key in the session domain.
	public function get($key)
	{      
		$session = $this->getAccess();
		return $session[$key];
	}
   
	/// Get the session domain that we are managing and return a reference to it.
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

	/// Return the domain as a flat array.
	public function getFlatDomain()
	{
		return $this->domain;
	}
   
	/// Return the string of the session ID.
	public function getID()
	{
		return $this->session->getID();
	}

	/** Increment the value in the session by the offset.
	 *  @param key \string The session key to increment.
	 *  @param offset \int The amount to increment the value.
	 */
	public function increment($key, $offset=1)
	{
		$session = $this->getAccess();
		$session[$key] += $offset;
	}
   
	/// Return whether the key is set to the specified value.
	public function is($key, $val)
	{
		$session = $this->getAccess();
		return (isset($session[$key]) && ($session[$key] === $val));
	}

	/// Return whether the session domain is empty or not.
	public function isEmpty()
	{
		$session = $this->getAccess();
		return empty($session);
	}

   
	/// Whether the key has been set in the session domain.
	public function issetKey($key)
	{
		$session = $this->getAccess();
		return isset($session[$key]);
	}

	/// Return the number of keys stored by the session.
	public function keyCount()
	{
		return count($this->getAccess());
	}
   
	/** Remove the session domain from the session.
	 *  This does not remove the hierarchy above the session domain.
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

	/// Remove all of the values in the session domain.
	public function removeValues()
	{
		$session =& $this->getAccess();
		$session = array();
	}

	/** Replace the session with the passed value.
	 *  @param newValue \mixed The new value(s) for the session.
	 */
	public function replaceWith($newValue)
	{
		$session =& $this->getAccess();
		$session = $newValue;
	}
   
	/// Reset the session to a blank start.
	public function reset()
	{
		$this->remove();
		$this->ensure();
	}
   
	/// Set the value of the key in the session domain.
	public function set($key, $value)
	{
		$session =& $this->getAccess();
		$session[$key] = $value;
	}

	/// Unset the key in the session domain.
	public function unsetKey($key)
	{
		$session =& $this->getAccess();
		unset($session[$key]);
	}
}
// EOF