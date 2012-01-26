<?php
namespace Evoke\Core;
/// The Factory for the core objects.
class Factory
{
   protected $objectHandler;
   private $settings;
   
   // Get or create the shared resources for the system.
   public function __construct(Array $setup=array())
   {
      $setup += array('ObjectHandler' => NULL,
		      'Settings'      => NULL);

      if (!$setup['ObjectHandler'] instanceof Iface\ObjectHandler)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires ObjectHandler');
      }

      // We are only going to read from the settings, so we only need
      // ArrayAccess.
      if (!$this->setup['Settings'] instanceof ArrayAccess)
      {
	 throw new \InvalidArgumentException(__METHOD__ . ' requires Settings');
      }
      
      $this->objectHandler = $setup['ObjectHandler'];
      $this->settings = $setup['Settings'];
   }
   
   /******************/
   /* Public Methods */
   /******************/

   public function getController(Array $setup=array())
   {
      return $this->objectHandler->getNew(
	 __NAMESPACE__ . '\Controller',
	 array_merge(array('Event_Manager' => $this->getEventManager()),
		     $setup));
   }
   
   /// Return the event manager.
   public function getEventManager()
   {
      return $this->objectHandler->getShared(__NAMESPACE__ . '\EventManager');
   }

   /// Return the file system object.
   public function getFilesystem()
   {
      return $this->objectHandler->getShared(__NAMESPACE__ . '\Filesystem');
   }

   /// Get processing that takes no action for a page.
   public function getProcessingNone(Array $setup=array())
   {
      return $this->objectHandler->getNew(__NAMESPACE__ . '\Processing\None',
					  array('EventManager' => $this->em));
   }
   
   /// Get the session object.
   public function getSession()
   {
      return $this->objectHandler->getShared(__NAMESPACE__ . '\Session');
   }

   /** Get a session manager object using the default session.
    *  @param domain The domain to get the session manager for.
    *  \return The session manager.
    */
   public function getSessionManager($domain)
   {
      return $this->objectHandler->getNew(
	 __NAMESPACE__ . '\SessionManager',
	 array('Domain'  => $domain,
	       'Session' => $this->getSession()));
   }

   public function getSettings()
   {
      return $this->objectHandler->getShared(__NAMESPACE__ . '\Settings');
   }
   
   /// Get the sql object.
   public function getSQL($name=NULL)
   {
      if ($name === NULL)
      {
	 if (empty($this->settings['DB']) || !is_array($this->settings['DB']))
	 {    
	    throw new \UnexpectedValueException(
	       __METHOD__ . ' DB Settings are needed to create an SQL object.');
	 }

	 $dbSettings = reset($this->settings['DB']);
      }
      else
      {
	 if (!isset($this->settings['DB'][$name]))
	 {
	    throw new \OutOfBoundsException(
	       __METHOD__ . ' no settings for DB: ' . $name . ' are defined.');
	 }

	 $dbSettings = $this->settings['DB'][$name];
      }

      return $this->objectHandler->getShared(
	 __NAMESPACE__ . '\DB\SQL',
	 array('DB' => $this->objectHandler->getShared(
		  __NAMESPACE__ . '\DB\PDO',
		  $dbSettings)));
   }

   /// Get a TableInfo object.
   public function getTableInfo(Array $setup)
   {
      return $this->objectHandler->getShared(
	 __NAMESPACE__ . '\DB\Table\Info',
	 array_merge(array('Failures' => $this->objectHandler->getNew(
			      __NAMESPACE__ . '\MessageArray'),
			   'SQL'      => $this->getSQL()),
		     $setup));
   }

   public function getTableListID()
   {
      return $this->objectHandler->getShared(__NAMESPACE__ . '\DB\Table\ListID',
					     array('SQL' => $this->getSQL()));
   }
   
   /** Get a TableReferences object (Recursive).
    *  Recursively create the TableReferences from the recursive structure
    *  passed in.
    */
   public function getTableReferences(Array $setup)
   {
      $tRefs = array();

      if (isset($setup['Multi_References']))
      {
	 foreach ($setup['Multi_References'] as $references)
	 {
	    foreach ($references as $parentField => $ref)
	    {
	       $ref['Parent_Field'] = $parentField;
	       $tRefs[] = $this->getTableReferences($ref);
	    }
	 }
      }

      if (isset($setup['References']))
      {
	 foreach ($setup['References'] as $parentField => $ref)
	 {
	    $ref['Parent_Field'] = $parentField;	 
	    $tRefs[] = $this->getTableReferences($ref);
	 }
      }

      return $this->objectHandler->getNew(
	 __NAMESPACE__ . '\DB\Table\References',
	 array_merge(
	    $setup,
	    array('References' => $tRefs,
		  'TableInfo' => $this->getTableInfo(
		     array('Table_Name' => $setup['Table_Name'])))));
   }

   // Get the Translator.
   public function getTranslator()
   {
      // Create the translator.
     return $this->objectHandler->getShared(
	__NAMESPACE__ . '\Translator',
	array('Default_Language' => $this->settings['Constant'][
		 'Default_Language'],
	      'SessionManager'   => $this->getSessionManager('Lang'),
	      'Translation_File' => $this->settings['File']['Translation']));
   }

   
   /// Get the XWR (XHTML Writing Resource).
   public function getXWR()
   {
      return $this->objectHandler->getShared(__NAMESPACE__ . '\XWR');
   }
}
// EOF