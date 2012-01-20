<?php
namespace Evoke\Core;
/** The resources for the application.
 *  This holds all of the important resources that classes could need - one of
 *  them being the inherited container which allows any object to be created.
 */
class App extends Container
{
   private $em;
   private $session;
   private $settings;
   private $sql;
   private $translator;
   private $xwr;
   
   // Get or create the shared resources for the system.
   public function __construct(Array $setup=array())
   {
      $setup += array('DB_Setup' => NULL);
      
      // Create the simple resources that do not rely on system settings.
      $this->em = $this->getShared('\Evoke\Core\EventManager');
      $this->session = $this->getShared('\Evoke\Core\Session');
      $this->settings = $this->getShared('\Evoke\Core\Settings');
      $this->xwr = $this->getShared('\Evoke\Core\XWR');
      
      // Create the database connections and their sql objects
      $this->sql = array();
      $dbConnections = array();
      
      if (isset($setup['DB_Setup']))
      {
	 $dbConnections = $setup['DB_Setup'];
      }
      elseif (isset($this->settings['DB']))
      {
	 $dbConnections = $this->settings['DB'];
      }
      
      foreach ($dbConnections as $name => $dbSettings)
      {
	 echo 'Adding DB: ' . $name . "\n<br>";
	 $this->sql[$name] = $this->getShared(
	    '\Evoke\Core\DB\SQL',
	    array('DB' => $this->getShared('\Evoke\Core\DB\PDO',
					   $dbSettings)));
      }

      // Create the translator.
      $this->translator = $this->getShared(
	 '\Evoke\Core\Translator',
	 array('Default_Language' => $this->settings['Constant'][
		  'Default_Language'],
	       'SessionManager'   => $this->getNew(
		  '\Evoke\Core\SessionManager',
		  array('Domain'  => 'Lang',
			'Session' => $this->session)),
	       'Translation_File' => $this->settings['File']['Translation']));
   }

   /******************/
   /* Public Methods */
   /******************/

   public function getController(Array $setup=array())
   {
      $setup += array('EventManager' => $this->em);
      
      return $this->getNew('\Evoke\Core\Controller', $setup);
   }

   /** Get a data object, specifying any referenced record data.
    */
   public function getData(Array $references=array())
   {
      return $this->getNew('\Evoke\Data\Base', array('References' => $references));
   }
   
   /// Return the event manager.
   public function getEventManager()
   {
      return $this->em;
   }

   /// Return the file system object.
   public function getFilesystem()
   {
      return $this->getShared('\Evoke\Core\Filesystem');
   }
   
   /// Get a model.
   public function getModel($model, Array $setup=array())
   {
      $setup += array('App'          => $this,
		      'EventManager' => $this->em);

      return $this->getNew($model, $setup);
   }

   public function getModelDB($model, Array $setup)
   {
      $setup += array('App'          => $this,
		      'EventManager' => $this->em,
		      'SQL'          => $this->getSQL());

      return $this->getNew($model, $setup);
   }
   
   public function getModelDBAdminRequestKeys()
   {
      return array('',
		   'Add',
		   'Cancel',
		   'Create_New',
		   'Delete_Cancel',
		   'Delete_Confirm',
		   'Delete_Request',
		   'Edit',
		   'Modify',
		   'Update_Current_Record');
   }

   // Get an Admin model for a joint table.
   public function getModelDBJointAdmin(Array $setup)
   {
      $setup += array('Failures'      => $this->getNew('\Evoke\Core\MessageArray'),
		      'Notifications' => $this->getNew('\Evoke\Core\MessageArray'),
		      'SQL'           => $this->getSQL(),
		      'TableInfo'     => NULL,
		      'TableListID'   => $this->getTableListID());
      
      return $this->getModel('\Evoke\Model\DB\JointAdmin', $setup);
   }

   // Get an Admin model for a joint table with linked information.
   public function getModelDBJointAdminLinked(Array $setup)
   {
      $setup += array('Failures'      => $this->getNew('\Evoke\Core\MessageArray'),
		      'Notifications' => $this->getNew('\Evoke\Core\MessageArray'),
		      'SQL'           => $this->getSQL(),
		      'TableInfo'     => NULL,
		      'TableListID'   => $this->getTableListID());
      
      return $this->getModel('\Evoke\Model\DB\JointAdminLinked', $setup);
   }
   
   /// Get an Admin model for a table.
   public function getModelDBTableAdmin(Array $setup)
   {
      $setup += array('App'           => $this,
		      'EventManager'  => $this->em,
		      'Failures'      => $this->getNew('\Evoke\Core\MessageArray'),
		      'Notifications' => $this->getNew('\Evoke\Core\MessageArray'),
		      'SQL'           => $this->getSQL(),
		      'TableInfo'    => NULL,
		      'Table_Name'    => NULL);

      if (!isset($setup['TableInfo']) && isset($setup['Table_Name']))
      {
	 $setup['TableInfo'] = $this->getTableInfo(
	    array('Table_Name' => $setup['Table_Name']));
      }
      
      return $this->getShared('\Evoke\Model\DB\TableAdmin', $setup);
   }
   
   /// Get a Model_Table object.
   public function getModelDBTable(Array $setup)
   {
      return $this->getShared(
	 '\Evoke\Model\DB\Table',
	 array_merge(array('App'           => $this,
			   'EventManager'  => $this->em,
			   'Failures'      => $this->getNew('\Evoke\Core\MessageArray'),
			   'Notifications' => $this->getNew('\Evoke\Core\MessageArray'),
			   'SQL'           => $this->getSQL()),
		     $setup));
   }

   /// Get a menu model for the named menu.
   public function getModelMenu($menuName)
   {
      $setup = array(
	 'App'             => $this,
	 'Data_Prefix'     => array('Header', 'Menu'),
	 'EventManager'    => $this->em,
	 'Failures'        => $this->getNew('\Evoke\Core\MessageArray'),
	 'Notifications'   => $this->getNew('\Evoke\Core\MessageArray'),
	 'Select_Setup'    => array(
	    'Conditions' => array('Menu.Name' => $menuName),
	    'Fields'     => '*',
	    'Order'      => 'Lft ASC',
	    'Limit'      => 0),
	 'SQL'             => $this->getSQL(),
	 'Table_Name'      => 'Menu',
	 'TableReferences' => $this->getTableReferences(
	    array('References' => array(
		     'List_ID' => array('Child_Field' => 'Menu_ID',
					'Table_Name'  => 'Menu_List')),
		  'Table_Name' => 'Menu')));

      return $this->getNew('\Evoke\Model\DB\Joint', $setup);
   }
   
   /// Get processing that takes no action for a page.
   public function getProcessingNone(Array $setup=array())
   {
      return $this->getNew('\Evoke\Core\Processing\None',
			   array('App'          => $this,
				 'EventManager' => $this->em));
   }
   
   /// Get the session object.
   public function getSession()
   {
      return $this->session;
   }

   /** Get a session manager object using the default session.
    *  @param domain The domain to get the session manager for.
    *  \return The session manager.
    */
   public function getSessionManager($domain)
   {
      return $this->getNew('\Evoke\Core\SessionManager',
			   array('Domain'  => $domain,
				 'Session' => $this->getSession()));
   }

   public function getSettings()
   {
      return $this->settings;
   }
   
   /// Get the sql object.
   public function getSQL($name=NULL)
   {
      if (!isset($name) && !empty($this->sql))
      {
	 return reset($this->sql);
      }

      if (isset($this->sql[$name]))
      {
	 return $this->sql[$name];
      }

      throw new \RuntimeException(
	 __METHOD__ . ' No DB connection exists for name: ' .
	 var_export($name, true));
   }
   
   /// Get a TableInfo object.
   public function getTableInfo(Array $setup)
   {
      return $this->getShared(
	 '\Evoke\Core\DB\Table\Info',
	 array_merge(array('Failures' => $this->getNew('\Evoke\Core\MessageArray'),
			   'SQL'      => $this->getSQL()),
		     $setup));
   }

   public function getTableListID()
   {
      return $this->getShared('\Evoke\Core\DB\Table\ListID',
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

      return $this->getNew(
	 '\Evoke\Core\DB\Table\References',
	 array_merge(
	    $setup,
	    array('References' => $tRefs,
		  'TableInfo' => $this->getTableInfo(
		     array('Table_Name' => $setup['Table_Name'])))));
   }

   // Get the Translator.
   public function getTranslator()
   {
      return $this->translator;
   }
   
   /// Get a view object.
   public function getView($view, Array $setup=array())
   {
      return $this->getNew(
	 $view,
	 array_merge(array('App'          => $this,
			   'EventManager' => $this->em,
			   'Translator'   => $this->translator,
			   'XWR'          => $this->xwr),
		     $setup));
   }
   
   /// Get the XWR (XHTML Writing Resource).
   public function getXWR()
   {
      return $this->xwr;
   }

   /// Whether the current request is via ajax or not.
   public function isAjaxRequest()
   {
      return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
	      ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
   }
}
// EOF