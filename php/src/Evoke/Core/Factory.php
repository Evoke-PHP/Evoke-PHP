<?php
namespace Evoke\Core;
/** The Factory for the core objects that are commonly used.  The Factory can
 *  be used to create and retrieve shared objects in the system.  It provides
 *  helper methods to aid the creation of frequently used objects.
 */
class Factory
{
	/** @property $namespace
	 * \array of namespace settings for the Factory.
	 */
	protected $namespace;

	/** @property $InstanceManager
	 *  InstanceManager \object for creating new and shared objects.
	 */
	protected $InstanceManager;

	/** @property $Settings
	 *  Settings \object for configuring created and retrieved objects.
	 */
	protected $Settings;
   
	public function __construct(Array $setup=array())
	{
		$setup += array('Instance_Manager' => NULL,
		                'Namespace'       => array('Core'  => '\Evoke\Core\\',
		                                           'Data'  => '\Evoke\Data\\',
		                                           'Model' => '\Evoke\Model\\'),
		                'Settings'        => NULL);

		if (!$setup['Instance_Manager'] instanceof
		    \Evoke\Core\Iface\InstanceManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires InstanceManager');
		}
      
		// We are only going to read from the settings, so we only need
		// ArrayAccess.
		if (!$setup['Settings'] instanceof \ArrayAccess)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Settings');
		}

		$this->namespace = $setup['Namespace'];
		$this->InstanceManager = $setup['Instance_Manager'];
		$this->Settings        = $setup['Settings'];
	}
   
	/******************/
	/* Public Methods */
	/******************/

	// Create a controller object.
	public function getController(Array $setup=array())
	{
		return $this->InstanceManager->create(
			$this->namespace['Core'] . 'Controller',
			array_merge(array('Event_Manager' => $this->getEventManager()),
			            $setup));
	}

	/** Get a data object, specifying any joint data.
	 *  @param joins \array Joint data.
	 */
	public function getData(Array $joins=array())
	{
		return $this->InstanceManager->create(
			$this->namespace['Data'] . 'Base', array('Joins' => $joins));
	}
   
	/// Return the event manager.
	public function getEventManager()
	{
		return $this->InstanceManager->get(
			$this->namespace['Core'] . 'EventManager');
	}

	/// Return the file system object.
	public function getFilesystem()
	{
		return $this->InstanceManager->get(
			$this->namespace['Core'] . 'Filesystem');
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

		return $this->InstanceManager->create(
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
		return $this->InstanceManager->create(
			$this->namespace['Core'] . 'MessageArray');
	}
   
	/// Get a model.
	public function getModel($model, Array $setup=array())
	{
		$setup += array('Event_Manager' => $this->getEventManager());

		return $this->InstanceManager->create($model, $setup);
	}

	/// Get a Database model.
	public function getModelDB($model, Array $setup)
	{
		$setup += array('Event_Manager' => $this->getEventManager(),
		                'SQL'          => $this->getSQL());

		return $this->InstanceManager->create($model, $setup);
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
		                'Table_Info'     => NULL,
		                'Table_List_I_D'   => $this->getTableListID());
      
		return $this->getModel($this->namespace['Model'] . 'DB\JointAdmin',
		                       $setup);
	}

	// Get an Admin model for a joint table with linked information.
	public function getModelDBJointAdminLinked(Array $setup)
	{
		$setup += array('Failures'      => $this->getMessageArray(),
		                'Notifications' => $this->getMessageArray(),
		                'SQL'           => $this->getSQL(),
		                'Table_Info'     => NULL,
		                'Table_List_I_D'   => $this->getTableListID());
      
		return $this->getModel($this->namespace['Model'] . 'DB\JointAdminLinked',
		                       $setup);
	}
   
	/// Get an Admin model for a table.
	public function getModelDBTableAdmin(Array $setup)
	{
		$setup += array('Event_Manager'  => $this->getEventManager(),
		                'Failures'      => $this->getMessageArray(),
		                'Notifications' => $this->getMessageArray(),
		                'SQL'           => $this->getSQL(),
		                'Table_Info'     => NULL,
		                'Table_Name'    => NULL);

		if (!isset($setup['Table_Info']) && isset($setup['Table_Name']))
		{
			$setup['Table_Info'] = $this->getTableInfo(
				array('Table_Name' => $setup['Table_Name']));
		}
      
		return $this->InstanceManager->get(
			$this->namespace['Model'] . 'DB\TableAdmin', $setup);
	}
   
	/// Get a Model_Table object.
	public function getModelDBTable(Array $setup)
	{
		return $this->InstanceManager->get(
			$this->namespace['Model'] . 'DB\Table',
			array_merge(array('Event_Manager'  => $this->getEventManager(),
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

		return $this->InstanceManager->create(
			$this->namespace['Model'] . 'DB\Joint', $setup);
	}

	/// Get an XML Page.
	public function getPageXML($page, Array $setup=array())
	{
		$setup += array('Factory'         => $this,
		                'Instance_Manager' => $this->InstanceManager,
		                'Translator'      => $this->getTranslator(),
		                'XWR'             => $this->getXWR());

		return $this->InstanceManager->create($page, $setup);
	}
   
	/** Get a processing object.
	 *  @param processing \string The class of processing.
	 *  @param setup \array Setup for the processing class.
	 */
	public function getProcessing($processing, Array $setup=array())
	{
		return $this->InstanceManager->create(
			$processing,
			array_merge($setup,
			            array('Event_Manager' => $this->getEventManager())));
	}
   
	/// Get the session object.
	public function getSession()
	{
		return $this->InstanceManager->get($this->namespace['Core'] . 'Session');
	}

	/** Get a session manager object using the default session.
	 *  @param domain The domain to get the session manager for.
	 *  \return The session manager.
	 */
	public function getSessionManager($domain)
	{
		return $this->InstanceManager->create(
			$this->namespace['Core'] . 'SessionManager',
			array('Domain'  => $domain,
			      'Session' => $this->getSession()));
	}

	public function getSettings()
	{
		return $this->InstanceManager->get($this->namespace['Core'] . 'Settings');
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

		return $this->InstanceManager->get(
			$this->namespace['Core'] . 'DB\SQL',
			array('DB' => $this->InstanceManager->get(
				      $this->namespace['Core'] . 'DB\PDO', $dbSettings)));
	}

	/// Get a TableInfo object.
	public function getTableInfo(Array $setup)
	{
		return $this->InstanceManager->get(
			$this->namespace['Core'] . 'DB\Table\Info',
			array_merge(array('Failures' => $this->InstanceManager->create(
				                  $this->namespace['Core'] . 'MessageArray'),
			                  'SQL'      => $this->getSQL()),
			            $setup));
	}

	public function getTableListID()
	{
		return $this->InstanceManager->get(
			$this->namespace['Core'] . 'DB\Table\ListID',
			array('SQL' => $this->getSQL()));
	}
   
	// Get the Translator.
	public function getTranslator()
	{
		return $this->InstanceManager->get(
			$this->namespace['Core'] . 'Translator',
			array('Default_Language' => $this->Settings['Constant'][
				      'Default_Language'],
			      'Filename'         => $this->Settings['File']['Translation'],
			      'Session_Manager'  => $this->getSessionManager('Lang')));
	}

	/// Get a view object.
	public function getView($view, Array $setup=array())
	{
		return $this->InstanceManager->create(
			$view,
			array_merge(array('Event_Manager'    => $this->getEventManager(),
			                  'Instance_Manager' => $this->InstanceManager,
			                  'Translator'       => $this->getTranslator(),
			                  'XWR'              => $this->getXWR()),
			            $setup));
	}
   
	/// Get the XWR (XML Writing Resource).
	public function getXWR()
	{
		return $this->InstanceManager->get($this->namespace['Core'] . 'XWR');
	}
}
// EOF