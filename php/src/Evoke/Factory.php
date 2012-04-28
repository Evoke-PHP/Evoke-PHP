<?php
namespace Evoke;
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

	/** @property $settings
	 *  Settings \object for configuring created and retrieved objects.
	 */
	protected $settings;

	/** @property $sharedInstances
	 *  \array The instances for shared services that we generally only want to
	 *  create one of.  These objects should not rely on any state (otherwise
	 *  sharing them could cause problems).
	 */
	private $sharedInstances = array();
   
	public function __construct(Array $setup=array())
	{
		$setup += array('Namespace' => array('Core'  => '\Evoke\\',
		                                     'Data'  => '\Evoke\Data\\',
		                                     'Model' => '\Evoke\Model\\'),
		                'Settings'  => NULL);

		// We are only going to read from the settings, so we only need
		// ArrayAccess.
		if (!$settings instanceof \ArrayAccess)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Settings');
		}

		$this->namespace = $namespace;
		$this->settings  = $settings;
	}
   
	/******************/
	/* Public Methods */
	/******************/
	
	/** Build a Controller.
	 *  @param className \string The class name of the controller.
	 *  @param params    \array Parameters for the response.
	 *  @param Request   \object The Request object (optional).
	 */
	public function buildController(/* String */       $className,
	                                Array              $params,
	                                Iface\HTTP\Request $request=NULL)
	{
		$request = $request ?: $this->getRequest();

		return $this->build($className, $params, $this, $request);
	}
	
	/// Build a MessageTree.
	public function buildMessageTree()
	{
		return $this->build($this->namespace['Core'] . 'MessageTree');
	}
   
	/// Build a model.
	public function buildModel(Array $dataPrefix=array())
	{
		return $this->build($model, $dataPrefix);
	}

	/// Build a Database model.
	public function buildModelDB($model, Array $setup)
	{
		$setup += array('SQL' => $this->getSQL());

		return $this->build($model, $setup);
	}

	/// Get Request keys for a Model DB Admin.
	public function buildModelDBAdminRequestKeys()
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
	public function buildModelDBJointAdmin(Array $setup)
	{
		$setup += array('Failures'      => $this->buildMessageTree(),
		                'Notifications' => $this->buildMessageTree(),
		                'SQL'           => $this->getSQL(),
		                'Table_Info'    => NULL,
		                'Table_List_ID' => $this->getTableListID());
      
		return $this->buildModel($this->namespace['Model'] . 'DB\JointAdmin',
		                       $setup);
	}

	// Get an Admin model for a joint table with linked information.
	public function buildModelDBJointAdminLinked(Array $setup)
	{
		$setup += array('Failures'      => $this->buildMessageTree(),
		                'Notifications' => $this->buildMessageTree(),
		                'SQL'           => $this->getSQL(),
		                'Table_Info'    => NULL,
		                'Table_List_ID' => $this->getTableListID());
      
		return $this->buildModel($this->namespace['Model'] . 'DB\JointAdminLinked',
		                       $setup);
	}
   
	/// Get an Admin model for a table.
	public function buildModelDBTableAdmin(Array $setup)
	{
		$setup += array('Event_Manager' => $this->getEventManager(),
		                'Failures'      => $this->buildMessageTree(),
		                'Info'          => NULL,
		                'Notifications' => $this->buildMessageTree(),
		                'SQL'           => $this->getSQL(),
		                'Table_Name'    => NULL);

		if (!isset($info) && isset($tableName))
		{
			$info = $this->getTableInfo(
				array('Table_Name' => $tableName));
		}
      
		return $this->get($this->namespace['Model'] . 'DB\TableAdmin', $setup);
	}
   
	/// Get a Model_Table object.
	public function buildModelDBTable(Array $setup)
	{
		return $this->get(
			$this->namespace['Model'] . 'DB\Table',
			array_merge(array('Event_Manager' => $this->getEventManager(),
			                  'Failures'      => $this->buildMessageTree(),
			                  'Notifications' => $this->buildMessageTree(),
			                  'SQL'           => $this->getSQL()),
			            $setup));
	}

	/// Get a menu model for the named menu.
	public function buildModelMenu($menuName)
	{
		$provider = $this->build($this->namespace['Core'] . 'Provider');
		$provider->define($this->namespace['Model'] . 'DB\Joint',
		                  array('tableName' => 'Menu',
		                        'Joins'     => 'Evoke\DB\Table\Joins'));
			
		return $provider->make($this->namespace['Model'] . 'DB\Joint');

		/*
		return $this->build(
			$this->namespace['Model'] . 'DB\Joint',
			
		
		$setup = array(
			'Event_Manager'  => $this->getEventManager(),
			'Failures'      => $this->buildMessageTree(),
			'Joins'         => $this->getJoins(
				array('Joins' => array(
					      'List_ID' => array('Child_Field' => 'Menu_ID',
					                         'Table_Name'  => 'Menu_List')),
				      'Table_Name' => 'Menu')),
			'Notifications' => $this->buildMessageTree(),
			'Select'        => array(
				'Conditions' => array('Menu.Name' => $menuName),
				'Fields'     => '*',
				'Order'      => 'Lft ASC',
				'Limit'      => 0),
			'SQL'           => $this->getSQL(),
			'Table_Name'    => 'Menu');			

		return $this->build($this->namespace['Model'] . 'DB\Joint', $setup);
		*/
	}
	
	/** Build a View.
	 *  @param className \string The class of view to create.
	 *  @param Writer    \object Writer object.
	 *  @param setup     \array  Setup parameters for the view.
	 */
	public function buildView(/* String */ $className,
	                          Iface\Writer $writer,
	                          Array        $setup=array())
	{		
		return $this->build($className, $writer, $setup);
	}	

	/** Build a View that is translated.
	 *  @param className  \string The class of view to create.
	 *  @param Writer     \object Writer object.
	 *  @param Translator \object Translator object.
	 *  @param setup      \array  Setup parameters for the view.
	 */
	public function buildViewTranslated(/* String */     $className,
	                                    Iface\Writer     $writer,
	                                    Iface\Translator $translator,
	                                    Array            $setup=array())
	{
		return $this->build($className, $writer, $translator, $setup);
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

		if (isset($multiJoins))
		{
			foreach ($multiJoins as $joins)
			{
				foreach ($joins as $parentField => $join)
				{
					$join['Parent_Field'] = $parentField;
					$tableJoins[] = $this->getJoins($join);
				}
			}
		}

		if (isset($joins))
		{
			foreach ($joins as $parentField => $join)
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
					      array('Table_Name' => $tableName)))));
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

	public function getResponse()
	{
		return $this->get($this->namespace['Core'] . 'HTTP\Response');
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
			if (empty($this->settings['DB']) || !is_array($this->settings['DB']))
			{    
				throw new \UnexpectedValueException(
					__METHOD__ . ' DB Settings are needed to create an SQL object.');
			}

			// We cannot call reset on Settings (as it causes an
			// 'Indirect modification of overloaded element' notice.
			$allSettings = $this->settings['DB'];
			$dbSettings = reset($allSettings);
		}
		else
		{
			if (!isset($this->settings['DB'][$name]))
			{
				throw new \OutOfBoundsException(
					__METHOD__ . ' no Settings for DB: ' . $name . ' are defined.');
			}

			$dbSettings = $this->settings['DB'][$name];
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
				                  $this->namespace['Core'] . 'MessageTree'),
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
			array('Default_Language'      => $this->settings['Constant'][
				      'Default_Language'],
			      'Request'               => $this->getRequest(),
			      'Session_Manager'       => $this->getSessionManager('Lang'),
			      'Translations_Filename' => $this->settings['File']['Translation']));
	}

	// Get the Triad object.
	public function getTriad()
	{
		return $this->get(
			$this->namespace['Core'] . 'Triad', $this->getEventManager());
	}
}
// EOF