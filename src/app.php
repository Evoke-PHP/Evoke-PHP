<?php
require_once 'system/db_connection.php';

/** The resources for the application.
 *  This holds all of the important resources that classes could need - one of
 *  them being the inherited container which allows any object to be created.
 */
class App extends Container
{
   private $em;
   private $logger;
   private $session;
   private $sql;
   private $translator;
   private $xwr;
   
   // Get or create the shared resources for the system.
   public function __construct()
   {
      $this->em = $this->getShared('Event_Manager');
      $this->logger = $this->getShared('Logger', array('App' => $this));
      $this->session = $this->getShared('Session');
      $this->sql = $this->getShared(
	 'SQL', array('DB' => $this->getShared(
			 'PDO_Wrapped',
			 array('App'      => $this,
			       'DSN'      => DB_DSN,
			       'Options'  => array(
				  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION),
			       'Password' => DB_PASSWORD,
			       'Username' => DB_USERNAME))));
      $this->translator = $this->getShared(
	 'Translator',
	 array('Session_Manager' => $this->get(
		  'Session_Manager', array('Domain'  => 'Lang',
					   'Session' => $this->session))));
      $this->xwr = $this->getShared('XWR');
   }

   /******************/
   /* Public Methods */
   /******************/

   public function getController(Array $setup=array())
   {
      $setup += array('App'           => $this,
		      'Event_Manager' => $this->em);
      
      return $this->get('Controller', $setup);
   }
   
   /// Return the event manager.
   public function getEventManager()
   {
      return $this->em;
   }

   /// Return the file system object.
   public function getFileSystem()
   {
      return $this->getShared('File_System');
   }
   
   /// Get a model.
   public function getModel($model, Array $setup=array())
   {
      $setup += array('App'           => $this,
		      'Event_Manager' => $this->em);

      return $this->get($model, $setup);
   }

   public function getModelDB($model, Array $setup)
   {
      $setup += array('App'           => $this,
		      'Event_Manager' => $this->em,
		      'SQL'           => $this->sql);

      return $this->get($model, $setup);
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
      $setup += array('Failures'      => $this->get('Message_Array'),
		      'Notifications' => $this->get('Message_Array'),
		      'SQL'           => $this->sql,
		      'Table_Info'    => NULL,
		      'Table_List_ID' => $this->getTableListID());
      
      return $this->getModel('Model_DB_Joint_Admin', $setup);
   }

   // Get an Admin model for a joint table with linked information.
   public function getModelDBJointAdminLinked(Array $setup)
   {
      $setup += array('Failures'      => $this->get('Message_Array'),
		      'Notifications' => $this->get('Message_Array'),
		      'SQL'           => $this->sql,
		      'Table_Info'    => NULL,
		      'Table_List_ID' => $this->getTableListID());
      
      return $this->getModel('Model_DB_Joint_Admin_Linked', $setup);
   }
   
   /// Get an Admin model for a table.
   public function getModelDBTableAdmin(Array $setup)
   {
      $setup += array('App'           => $this,
		      'Event_Manager' => $this->em,
		      'Failures'      => $this->get('Message_Array'),
		      'Notifications' => $this->get('Message_Array'),
		      'SQL'           => $this->sql,
		      'Table_Info'    => NULL,
		      'Table_Name'    => NULL);

      if (!isset($setup['Table_Info']) && isset($setup['Table_Name']))
      {
	 $setup['Table_Info'] = $this->getTableInfo(
	    array('Table_Name' => $setup['Table_Name']));
      }
      
      return $this->getShared('Model_DB_Table_Admin', $setup);
   }
   
   /// Get a Model_Table object.
   public function getModelDBTable(Array $setup)
   {
      return $this->getShared(
	 'Model_DB_Table',
	 array_merge(array('App'           => $this,
			   'Event_Manager' => $this->em,
			   'Failures'      => $this->get('Message_Array'),
			   'Notifications' => $this->get('Message_Array'),
			   'SQL'           => $this->sql),
		     $setup));
   }

   /// Get a menu model.
   public function getModelMenu(Array $setup=array())
   {
      $setup += array(
	 'App'              => $this,
	 'Data_Prefix'      => array('Header', 'Menu'),
	 'Event_Manager'    => $this->em,
	 'Failures'         => $this->get('Message_Array'),
	 'Notifications'    => $this->get('Message_Array'),
	 'Select_Setup'     => array(
	    'Conditions' => array('Menu.Name' => 'Main_Menu'),
	    'Fields'     => '*',
	    'Order'      => 'Lft ASC',
	    'Limit'      => 0),
	 'SQL'              => $this->sql,
	 'Table_Name'       => 'Menu',
	 'Table_References' => $this->getTableReferences(
	    array('References' => array(
		     'List_ID' => array('Child_Field' => 'Menu_ID',
					'Table_Name'  => 'Menu_List')),
		  'Table_Name' => 'Menu')));

      return $this->get('Model_DB_Joint', $setup);
   }
   
   /// Get processing that takes no action for a page.
   public function getProcessingNone(Array $setup=array())
   {
      return $this->get('Processing_None',
			array('App'           => $this,
			      'Event_Manager' => $this->em));
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
      return $this->get('Session_Manager',
			array('Domain'  => $domain,
			      'Session' => $this->getSession()));
   }
   
   /// Get the sql object.
   public function getSQL()
   {
      return $this->sql;
   }
   
   /// Get a Table_Info object.
   public function getTableInfo(Array $setup)
   {
      return $this->getShared(
	 'Table_Info',
	 array_merge(array('Failures' => $this->get('Message_Array'),
			   'SQL'      => $this->sql),
		     $setup));
   }

   public function getTableListID()
   {
      return $this->getShared('Table_List_ID', array('SQL' => $this->sql));
   }
   
   /** Get a Table_References object (Recursive).
    *  Recursively create the Table_References from the recursive structure
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

      return $this->get(
	 'Table_References',
	 array_merge(
	    $setup,
	    array('References' => $tRefs,
		  'Table_Info' => $this->getTableInfo(
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
      return $this->get(
	 $view,
	 array_merge(array('App'           => $this,
			   'Event_Manager' => $this->em,
			   'Translator'    => $this->translator,
			   'XWR'           => $this->xwr),
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

   /** Make sure the requirements are set or of the correct instance type.
    *  @param needs \array Values that must be set or specific objects.
    */
   public function needs(Array $needs)
   {
      foreach ($needs as $needType => $needVals)
      {
	 switch(strtoupper($needType))
	 {
	 case 'INSTANCE':
	    foreach ($needVals as $key => $val)
	    {
	       $this->needsInstance($val, $key);
	    }
	    break;

	 case 'INSTANCES':
	    foreach ($needVals as $key => $vals)
	    {
	       if (!is_array($vals))
	       {
		  throw new InvalidArgumentException(
		     $this->getCallerDetails(2) . ' makes ' . __METHOD__ .
		     ' fail as Instances are not specified as an array.');
	       }
	       
	       foreach ($vals as $val)
	       {
		  $this->needsInstance($val, $key);
	       }
	    }
	    break;

	 case 'SET':
	    foreach ($needVals as $key => $val)
	    {
	       if (!isset($val))
	       {
		  throw new InvalidArgumentException(
		     $this->getCallerDetails(2) . ' makes ' . __METHOD__ .
		     ' fail due to missing ' . $key);
	       }
	    }
	    break;

	 default:
	    throw new OutOfBoundsException(
	       __METHOD__ . ' unknown needs type: ' . $needType);
	 }
      }
   }
   
   /*******************/
   /* Private Methods */
   /*******************/

   /** Get the caller details from the number of stack levels back.
    *  @param stackLevel \int The number of levels up the stack that we want.
    */
   private function getCallerDetails($stackLevel)
   {
      $trace = debug_backtrace();
      $callerArr = array_slice($trace, $stackLevel, 1);
      $caller = reset($callerArr);
      $callerDetails = '';

      if (isset($caller['class']))
      {
	 $callerDetails .= $caller['class'];
      }
      
      if (isset($caller['function']))
      {
	 $callerDetails .= '::' . $caller['function'];
      }

      if (empty($callerDetails))
      {
	 $callerDetails = 'UNKNOWN_CALLER';
      }

      return $callerDetails;
   }

   /** Need an object of the correct instance type.
    *  @param obj \object The object to check the instanceof.
    *  @param type \string The instance type that is needed.
    */
   private function needsInstance($obj, $type)
   {
      if ($obj instanceof $type)
      {
	 return;
      }

      $message = $this->getCallerDetails(3) . ' makes ' . __METHOD__ .
	 ' fail due to missing ' . $type;
	 
      if (is_object($obj))
      {
	 $message .= ' got instance of ' . get_class($obj) . ' instead.';
      }
	 
      throw new InvalidArgumentException($message);
   }
}

// EOF