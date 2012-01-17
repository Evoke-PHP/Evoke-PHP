<?php
namespace Evoke;
/// Session_Manager provide management of a session domain.
class Session_Manager
{
   /** Domain within $_SESSION to manage specified as a 1 dimensional array.
    *  (i.e  array(Lev_1, Lev_2, Lev_3) is $_SESSION[Lev_1][Lev_2][Lev_3])
    */
   private $setup;
   
   public function __construct($setup=array())
   {
      $this->setup = array_merge(
	 array('Domain' => array(),
	       'Session' => NULL),
	 $setup);

      // Ensure the domain is an array for all of the methods that use it.  When
      // a string is passed in we should use that as the domain. 
      if (!(is_array($this->setup['Domain'])))
      {
	 $this->setup['Domain'] = array((string)($this->setup['Domain']));
      }

      if (!$this->setup['Session'] instanceof Session)
      {
	 throw new \InvalidArgumentException(__METHOD__ . ' needs Session');
      }
      
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
      $this->setup['Session']->ensure();
      
      // Make currentDomain a reference to $_SESSION so that when we change it
      // we are modifying the session.
      $currentDomain =& $_SESSION;

      foreach($this->setup['Domain'] as $subdomain)
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

      foreach($this->setup['Domain'] as $subdomain)
      {
	 // Update the currentDomain to reference the session subdomain.
	 $currentDomain =& $currentDomain[$subdomain]; 
      }

      return $currentDomain;
   }

   /// Return the domain as a flat array.
   public function getFlatDomain()
   {
      return $this->setup['Domain'];
   }
   
   /// Return the string of the session ID.
   public function id()
   {
      return $this->setup['Session']->id();
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
      if (empty($this->setup['Domain']))
      {
	 session_unset();
      }
      else
      {
	 // Set currentDomain to reference $_SESSION.
	 $currentDomain =& $_SESSION;

	 $previousSubdomain = $currentDomain;
	 
	 foreach($this->setup['Domain'] as $subdomain)
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