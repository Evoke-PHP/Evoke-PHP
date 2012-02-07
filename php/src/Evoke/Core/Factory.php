<?php
namespace Evoke\Core;
/** The factory for the core objects that are commonly used.  The factory can
 *  be used to create and retrieve shared objects in the system.  It provides
 *  helper methods to aid the creation of frequently used objects.
 */
class Factory
{
	protected $instanceManager;   
	protected $namespace;
	protected $settings;
   
	// Get or create the shared resources for the system.
	public function __construct(Array $setup=array())
	{
		$setup += array('InstanceManager' => NULL,
		                'Namespace'       => array('Core'  => '\Evoke\Core\\',
		                                           'Data'  => '\Evoke\Data\\',
		                                           'Model' => '\Evoke\Model\\'),
		                'Settings'        => NULL);

		if (!$setup['InstanceManager'] instanceof
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

		$this->instanceManager = $setup['InstanceManager'];
		$this->namespace = $setup['Namespace'];
		$this->settings = $setup['Settings'];
	}
   
	/******************/
	/* Public Methods */
	/******************/

	// Create a controller object.
	public function getController(Array $setup=array())
	{
		return $this->instanceManager->create(
			$this->namespace['Core'] . 'Controller',
			array_merge(array('EventManager' => $this->getEventManager()),
			            $setup));
	}

	/** Get a data object, specifying any referenced record data.
	 */
	public function getData(Array $references=array())
	{
		return $this->instanceManager->create(
			$this->namespace['Data'] . 'Base', array('References' => $references));
	}
   
	/// Return the event manager.
	public function getEventManager()
	{
		return $this->instanceManager->get(
			$this->namespace['Core'] . 'EventManager');
	}

	/// Return the file system object.
	public function getFilesystem()
	{
		return $this->instanceManager->get(
			$this->namespace['Core'] . 'Filesystem');
	}

	/// Create a message array.
	public function getMessageArray()
	{
		return $this->instanceManager->create(
			$this->namespace['Core'] . 'MessageArray');
	}
   
	/// Get a model.
	public function getModel($model, Array $setup=array())
	{
		$setup += array('EventManager' => $this->getEventManager());

		return $this->instanceManager->create($model, $setup);
	}

	/// Get a Database model.
	public function getModelDB($model, Array $setup)
	{
		$setup += array('EventManager' => $this->getEventManager(),
		                'SQL'          => $this->getSQL());

		return $this->instanceManager->create($model, $setup);
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
		                'TableInfo'     => NULL,
		                'TableListID'   => $this->getTableListID());
      
		return $this->getModel($this->namespace['Model'] . 'DB\JointAdmin',
		                       $setup);
	}

	// Get an Admin model for a joint table with linked information.
	public function getModelDBJointAdminLinked(Array $setup)
	{
		$setup += array('Failures'      => $this->getMessageArray(),
		                'Notifications' => $this->getMessageArray(),
		                'SQL'           => $this->getSQL(),
		                'TableInfo'     => NULL,
		                'TableListID'   => $this->getTableListID());
      
		return $this->getModel($this->namespace['Model'] . 'DB\JointAdminLinked',
		                       $setup);
	}
   
	/// Get an Admin model for a table.
	public function getModelDBTableAdmin(Array $setup)
	{
		$setup += array('EventManager'  => $this->getEventManager(),
		                'Failures'      => $this->getMessageArray(),
		                'Notifications' => $this->getMessageArray(),
		                'SQL'           => $this->getSQL(),
		                'TableInfo'     => NULL,
		                'Table_Name'    => NULL);

		if (!isset($setup['TableInfo']) && isset($setup['Table_Name']))
		{
			$setup['TableInfo'] = $this->getTableInfo(
				array('Table_Name' => $setup['Table_Name']));
		}
      
		return $this->instanceManager->get(
			$this->namespace['Model'] . 'DB\TableAdmin', $setup);
	}
   
	/// Get a Model_Table object.
	public function getModelDBTable(Array $setup)
	{
		return $this->instanceManager->get(
			$this->namespace['Model'] . 'DB\Table',
			array_merge(array('EventManager'  => $this->getEventManager(),
			                  'Failures'      => $this->getMessageArray(),
			                  'Notifications' => $this->getMessageArray(),
			                  'SQL'           => $this->getSQL()),
			            $setup));
	}

	/// Get a menu model for the named menu.
	public function getModelMenu($menuName)
	{
		$setup = array(
			'EventManager'    => $this->getEventManager(),
			'Failures'        => $this->getMessageArray(),
			'Notifications'   => $this->getMessageArray(),
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

		return $this->instanceManager->create(
			$this->namespace['Model'] . 'DB\Joint', $setup);
	}

	/// Get an XML Page.
	public function getPageXML($page, Array $setup=array())
	{
		$setup += array('Factory'         => $this,
		                'InstanceManager' => $this->instanceManager,
		                'Translator'      => $this->getTranslator(),
		                'XWR'             => $this->getXWR());

		return $this->instanceManager->create($page, $setup);
	}
   
	/** Get a processing object.
	 *  @param processing \string The class of processing.
	 *  @param setup \array Setup for the processing class.
	 */
	public function getProcessing($processing, Array $setup=array())
	{
		return $this->instanceManager->create(
			$processing,
			array_merge($setup,
			            array('EventManager' => $this->getEventManager())));
	}
   
	/// Get the session object.
	public function getSession()
	{
		return $this->instanceManager->get($this->namespace['Core'] . 'Session');
	}

	/** Get a session manager object using the default session.
	 *  @param domain The domain to get the session manager for.
	 *  \return The session manager.
	 */
	public function getSessionManager($domain)
	{
		return $this->instanceManager->create(
			$this->namespace['Core'] . 'SessionManager',
			array('Domain'  => $domain,
			      'Session' => $this->getSession()));
	}

	public function getSettings()
	{
		return $this->instanceManager->get($this->namespace['Core'] . 'Settings');
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

			// We cannot call reset on settings (as it causes an
			// 'Indirect modification of overloaded element' notice.
			$allSettings = $this->settings['DB'];
			$dbSettings = reset($allSettings);
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

		return $this->instanceManager->get(
			$this->namespace['Core'] . 'DB\SQL',
			array('DB' => $this->instanceManager->get(
				      $this->namespace['Core'] . 'DB\PDO', $dbSettings)));
	}

	/// Get a TableInfo object.
	public function getTableInfo(Array $setup)
	{
		return $this->instanceManager->get(
			$this->namespace['Core'] . 'DB\Table\Info',
			array_merge(array('Failures' => $this->instanceManager->create(
				                  $this->namespace['Core'] . 'MessageArray'),
			                  'SQL'      => $this->getSQL()),
			            $setup));
	}

	public function getTableListID()
	{
		return $this->instanceManager->get(
			$this->namespace['Core'] . 'DB\Table\ListID',
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

		return $this->instanceManager->create(
			$this->namespace['Core'] . 'DB\Table\References',
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
		return $this->instanceManager->get(
			$this->namespace['Core'] . 'Translator',
			array('Default_Language' => $this->settings['Constant'][
				      'Default_Language'],
			      'SessionManager'   => $this->getSessionManager('Lang'),
			      'Translation_File' => $this->settings['File']['Translation']));
	}

	/// Get a view object.
	public function getView($view, Array $setup=array())
	{
		return $this->instanceManager->create(
			$view,
			array_merge(array('EventManager'    => $this->getEventManager(),
			                  'InstanceManager' => $this->instanceManager,
			                  'Translator'      => $this->getTranslator(),
			                  'XWR'             => $this->getXWR()),
			            $setup));
	}
   
	/// Get the XWR (XHTML Writing Resource).
	public function getXWR()
	{
		return $this->instanceManager->get($this->namespace['Core'] . 'XWR');
	}
}
// EOF