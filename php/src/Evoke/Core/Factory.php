<?php
namespace Evoke\Core;
/** The Factory for the core objects that are commonly used.  The Factory can
 *  be used to create and retrieve shared objects in the system.  It provides
 *  helper methods to aid the creation of frequently used objects.
 */
class Factory extends InstanceManager implements Iface\Factory
{
	/** @property $namespace
	 * \array of namespace settings for the Factory.
	 */
	protected $namespace;

	/** @property $Settings
	 *  Settings \object for configuring created and retrieved objects.
	 */
	protected $Settings;

	/** @property $sharedInstances
	 *  \array The instances for shared services that we generally only want to
	 *  create one of.  These objects should not rely on any state (otherwise
	 *  sharing them could cause problems).
	 */
	private $sharedInstances = array();
   
	public function __construct(Array $setup=array())
	{
		$setup += array('Namespace' => array('Core'  => '\Evoke\Core\\',
		                                     'Data'  => '\Evoke\Data\\',
		                                     'Model' => '\Evoke\Model\\'),
		                'Settings'  => NULL);

		// We are only going to read from the settings, so we only need
		// ArrayAccess.
		if (!$setup['Settings'] instanceof \ArrayAccess)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Settings');
		}

		$this->namespace = $setup['Namespace'];
		$this->Settings  = $setup['Settings'];
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/** Build a View.
	 *  @param className \string The class of view to create.
	 *  @param Writer    \object Writer object.
	 *  @param setup     \array  Setup parameters for the view.
	 */
	public function buildView(/* String */ $className,
	                          Iface\Writer $Writer,
	                          Array        $setup=array())
	{		
		return $this->build($className, $Writer, $setup);
	}	

	/** Build a View that is translated.
	 *  @param className  \string The class of view to create.
	 *  @param Writer     \object Writer object.
	 *  @param Translator \object Translator object.
	 *  @param setup      \array  Setup parameters for the view.
	 */
	public function buildViewTranslated(/* String */     $className,
	                                    Iface\Writer     $Writer,
	                                    Iface\Translator $Translator,
	                                    Array            $setup=array())
	{
		return $this->build($className, $Writer, $Translator, $setup);
	}
	
	// Create a controller object.
	public function getController()
	{
		return $this->build($this->namespace['Core'] . 'Controller',
		                    $this->getEventManager());
	}

	/** Get a data object, specifying any joint data.
	 *  @param joins \array Joint data.
	 */
	public function getData(Array $joins=array())
	{
		return $this->build($this->namespace['Data'] . 'Base',
		                    array('Joins' => $joins));
	}
   
	/// Return the event manager.
	public function getEventManager()
	{
		return $this->get($this->namespace['Core'] . 'EventManager');
	}

	/// Return the file system object.
	public function getFilesystem()
	{
		return $this->get($this->namespace['Core'] . 'Filesystem');
	}

	/** Get a Joins object (Recursive).
	 *  Recursively create the Joins from the tree structure passed in.
	 */
	public function getJoins(Array $setup)
	{
		$tableJoins = array();

		if (isset($setup['Multi_Joins']))
		{
			foreach ($setup['Multi_Joins'] as $joins)
			{
				foreach ($joins as $parentField => $join)
				{
					$join['Parent_Field'] = $parentField;
					$tableJoins[] = $this->getJoins($join);
				}
			}
		}

		if (isset($setup['Joins']))
		{
			foreach ($setup['Joins'] as $parentField => $join)
			{
				$join['Parent_Field'] = $parentField;	 
				$tableJoins[] = $this->getJoins($join);
			}
		}

		return $this->build(
			$this->namespace['Core'] . 'DB\Table\Joins',
			array_merge(
				$setup,
				array('Joins' => $tableJoins,
				      'Info'  => $this->getTableInfo(
					      array('Table_Name' => $setup['Table_Name'])))));
	}

	/// Create a message array.
	public function getMessageArray()
	{
		return $this->build($this->namespace['Core'] . 'MessageArray');
	}
   
	/// Get a model.
	public function getModel($model, Array $setup=array())
	{
		$setup += array('Event_Manager' => $this->getEventManager());

		return $this->build($model, $setup);
	}

	/// Get a Database model.
	public function getModelDB($model, Array $setup)
	{
		$setup += array('Event_Manager' => $this->getEventManager(),
		                'SQL'           => $this->getSQL());

		return $this->build($model, $setup);
	}

	/// Get Request keys for a Model DB Admin.
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
		$setup += array('Failures'      => $this->getMessageArray(),
		                'Notifications' => $this->getMessageArray(),
		                'SQL'           => $this->getSQL(),
		                'Table_Info'    => NULL,
		                'Table_List_ID' => $this->getTableListID());
      
		return $this->getModel($this->namespace['Model'] . 'DB\JointAdmin',
		                       $setup);
	}

	// Get an Admin model for a joint table with linked information.
	public function getModelDBJointAdminLinked(Array $setup)
	{
		$setup += array('Failures'      => $this->getMessageArray(),
		                'Notifications' => $this->getMessageArray(),
		                'SQL'           => $this->getSQL(),
		                'Table_Info'    => NULL,
		                'Table_List_ID' => $this->getTableListID());
      
		return $this->getModel($this->namespace['Model'] . 'DB\JointAdminLinked',
		                       $setup);
	}
   
	/// Get an Admin model for a table.
	public function getModelDBTableAdmin(Array $setup)
	{
		$setup += array('Event_Manager' => $this->getEventManager(),
		                'Failures'      => $this->getMessageArray(),
		                'Info'          => NULL,
		                'Notifications' => $this->getMessageArray(),
		                'SQL'           => $this->getSQL(),
		                'Table_Name'    => NULL);

		if (!isset($setup['Info']) && isset($setup['Table_Name']))
		{
			$setup['Info'] = $this->getTableInfo(
				array('Table_Name' => $setup['Table_Name']));
		}
      
		return $this->get($this->namespace['Model'] . 'DB\TableAdmin', $setup);
	}
   
	/// Get a Model_Table object.
	public function getModelDBTable(Array $setup)
	{
		return $this->get(
			$this->namespace['Model'] . 'DB\Table',
			array_merge(array('Event_Manager' => $this->getEventManager(),
			                  'Failures'      => $this->getMessageArray(),
			                  'Notifications' => $this->getMessageArray(),
			                  'SQL'           => $this->getSQL()),
			            $setup));
	}

	/// Get a menu model for the named menu.
	public function getModelMenu($menuName)
	{
		$setup = array(
			'Event_Manager'  => $this->getEventManager(),
			'Failures'      => $this->getMessageArray(),
			'Joins'         => $this->getJoins(
				array('Joins' => array(
					      'List_ID' => array('Child_Field' => 'Menu_ID',
					                         'Table_Name'  => 'Menu_List')),
				      'Table_Name' => 'Menu')),
			'Notifications' => $this->getMessageArray(),
			'Select'        => array(
				'Conditions' => array('Menu.Name' => $menuName),
				'Fields'     => '*',
				'Order'      => 'Lft ASC',
				'Limit'      => 0),
			'SQL'           => $this->getSQL(),
			'Table_Name'    => 'Menu');			

		return $this->build($this->namespace['Model'] . 'DB\Joint', $setup);
	}

	/// Get an XML Page.
	public function getPageXML($page, Array $setup=array())
	{
		$setup += array('Factory'         => $this,
		                'Translator'      => $this->getTranslator(),
		                'Writer'          => $this->buildWriterXWR());

		return $this->build($page, $setup);
	}
   
	/** Get a processing object.
	 *  @param processing \string The class of processing.
	 *  @param setup \array Setup for the processing class.
	 */
	public function getProcessing($processing, Array $setup=array())
	{
		$setup += array('Event_Manager' => $this->getEventManager());
		
		return $this->build($processing, $setup);
	}

	/// Get the Request object.
	public function getRequest()
	{
		return $this->get($this->namespace['Core'] . 'HTTP\Request');
	}

	/** Get a Response object.
	 *  @param response \string The class of the response (as a string).
	 *  @param Request  \object The Request object (optional).
	 *  @param params   \array Parameters for the response.
	 */
	public function getResponse($response,
	                            Array $params,
	                            Iface\Factory $Factory=NULL,
	                            Iface\HTTP\Request $Request=NULL)
	{
		$Factory = $Factory ?: $this;
		$Request = $Request ?: $this->getRequest();

		return $this->build($response, $params, $Factory, $Request);
	}
	
	/// Get the Session object.
	public function getSession()
	{
		return $this->get($this->namespace['Core'] . 'Session');
	}

	/** Get a session manager object using the default session.
	 *  @param domain The domain to get the session manager for.
	 *  \return The session manager.
	 */
	public function getSessionManager($domain)
	{
		return $this->build($this->namespace['Core'] . 'SessionManager',
		                    array('Domain'  => $domain,
		                          'Session' => $this->getSession()));
	}

	public function getSettings()
	{
		return $this->get($this->namespace['Core'] . 'Settings');
	}
   
	/// Get the SQL object.
	public function getSQL($name=NULL)
	{
		if ($name === NULL)
		{
			if (empty($this->Settings['DB']) || !is_array($this->Settings['DB']))
			{    
				throw new \UnexpectedValueException(
					__METHOD__ . ' DB Settings are needed to create an SQL object.');
			}

			// We cannot call reset on Settings (as it causes an
			// 'Indirect modification of overloaded element' notice.
			$allSettings = $this->Settings['DB'];
			$dbSettings = reset($allSettings);
		}
		else
		{
			if (!isset($this->Settings['DB'][$name]))
			{
				throw new \OutOfBoundsException(
					__METHOD__ . ' no Settings for DB: ' . $name . ' are defined.');
			}

			$dbSettings = $this->Settings['DB'][$name];
		}

		return $this->get(
			$this->namespace['Core'] . 'DB\SQL',
			array('DB' => $this->get(
				      $this->namespace['Core'] . 'DB\PDO', $dbSettings)));
	}

	/// Get a TableInfo object.
	public function getTableInfo(Array $setup)
	{
		return $this->get(
			$this->namespace['Core'] . 'DB\Table\Info',
			array_merge(array('Failures' => $this->build(
				                  $this->namespace['Core'] . 'MessageArray'),
			                  'SQL'      => $this->getSQL()),
			            $setup));
	}

	public function getTableListID()
	{
		return $this->get(
			$this->namespace['Core'] . 'DB\Table\ListID',
			array('SQL' => $this->getSQL()));
	}
   
	// Get the Translator.
	public function getTranslator()
	{
		return $this->get(
			$this->namespace['Core'] . 'Translator',
			array('Default_Language'      => $this->Settings['Constant'][
				      'Default_Language'],
			      'Request'               => $this->getRequest(),
			      'Session_Manager'       => $this->getSessionManager('Lang'),
			      'Translations_Filename' => $this->Settings['File']['Translation']));
	}
}
// EOF