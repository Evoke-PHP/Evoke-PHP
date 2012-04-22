<?php
namespace Evoke\Model\DB;

use Evoke\Core\Iface;

/// Model_DB_Joint_Admin provides a CRUD interface to a joint set of data.
class JointAdmin extends Joint implements \Evoke\Core\Iface\Model\Admin
{
	/** @property $Failures
	 *  Failure MessageTree \object
	 */
	protected $Failures;

	/** @property $Notifications
	 *  Notification MessageTree \object
	 */
	protected $Notifications;

	/** @property $SessionManager
	 *  Session Manager \object
	 */
	protected $SessionManager;

	/** @property $Table_List_ID
	 *  List_ID Table \object
	 */
	protected $Table_List_ID;
	
	public function __construct(Iface\EventManager    $EventManager,
	                            Iface\SessionManager  $SessionManager,
	                            Iface\DB\Table\ListID $TableListID,
	                            /* Bool */            $validate=true,
	                            Iface\MessageTree     $Failures=NULL,
	                            Iface\MessageTree     $Notifications=NULL)
	{
		parent::__construct($EventManager, );

		$this->Failures       = $Failures;
		$this->Notifications  = $Notifications;
		$this->SessionManager = $SessionManager;
		$this->TableListID    = $TableListID;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/** Add the joint record to the database.
	 *  @param record \array Any new data for the record to be added.
	 */
	public function add($record)
	{
		$this->Failures->reset();

		$this->updateCurrentRecord($record);
		$record = $this->SessionManager->get('Current_Record');
		$data = array($record);
      
		if ($this->validate && !$this->validate($data, $this->Joins))
		{
			return false;
		}
            
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->SQL->beginTransaction();

			$this->recurse(
				array('Depth_First_Data'   => array($this, 'addEntries'),
				      'Depth_First_Parent' => array($this, 'feedbackListID')),
				$data,
				$this->Joins);
	 
			$this->SQL->commit();
			$this->SessionManager->reset();
			$this->Notifications->add('Add', 'Successful');
		}
		catch (\Exception $e)
		{
			$this->EventManager->notify(
				'Log', array('Level'   => LOG_ERR,
				             'Method'  => __METHOD__,
				             'Message' => $e->getMessage()));
			$this->Failures->add('Add_Failed', 'Sys_Admin_Notified');	 
			$this->SQL->rollBack();
		}
	}

	/// Cancel the editing of the record.
	public function cancel()
	{
		$this->SessionManager->reset();
	}

	/// Begin creating a new record.
	public function createNew()
	{
		$currentRecord = $this->Joins->getEmpty();
		$this->SessionManager->set('New_Record', true);
		$this->SessionManager->set('Current_Record', $currentRecord);
		$this->SessionManager->set('Editing_Record', true);
		$this->SessionManager->set('Edited_Record', array());
	}
   
	/// Cancel the currently requested deletion.
	public function deleteCancel()
	{
		$this->SessionManager->reset();
	}

	/// Delete the specified record.
	public function deleteConfirm(Array $record)
	{
		$this->Failures->reset();

		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->SQL->beginTransaction();
			$deleteRecord = $this->SessionManager->get('Delete_Record');
	 
			$this->recurse(
				array('Breadth_First_Data' => array($this, 'deleteEntries')),
				$deleteRecord,
				$this->Joins);
	 
			$this->SessionManager->reset();
			$this->SQL->commit();
			$this->Notifications->add('Delete', 'Successful');
		}
		catch (\Exception $e)
		{
			$this->EventManager->notify(
				'Log', array('Level'   => LOG_ERR,
				             'Method'  => __METHOD__,
				             'Message' => $e->getMessage()));
			$this->Failures->add('Delete_Failed', 'Sys_Admin_Notified');
			$this->SQL->rollBack();
		}
	}

	/** Request that a record should be deleted, but only after confirmation
	 *  from the user.
	 */
	public function deleteRequest($record)
	{
		$conditions = array();
      
		// The fields to select the record are from the record in the parent.
		foreach ($record as $field => $value)
		{
			$conditions[$this->tableName . '.' . $field] = $value;
		}

		$data = $this->getData(array('Conditions' => $conditions));
      
		// Get the offset in the data where the record is found.
		$baseData = $this->getAtPrefix($data, $this->dataPrefix);
		$record = $baseData['Records'];
      
		$this->SessionManager->set('Delete_Request', true);
		$this->SessionManager->set('Delete_Record', $record);
	}

	/** Edit a record.
	 *  @param record The post data.
	 */
	public function edit($record)
	{
		$conditions = array();
      
		// The fields to select the record are from the record in the parent.
		foreach ($record as $field => $value)
		{
			$conditions[$this->tableName . '.' . $field] = $value;
		}
      
		$results = parent::getData(array('Conditions' => $conditions));
		$data = $this->getAtPrefix($results, $this->dataPrefix);
      
		// We edit a single record at a time.
		if (count($data) !== 1)
		{
			$this->EventManager->notify(
				'Log',
				array('Level'   => LOG_ERR,
				      'Method'  => __METHOD__,
				      'Message' => 'multiple records received.'));
	 
			$this->Failures->add('Edit_Failed', 'Sys_Admin_Notified');

			return;
		}

		$record = reset($data);
      
		// Set the session to show that the record is being edited.
		$this->SessionManager->set('New_Record', false);
		$this->SessionManager->set('Current_Record', $record);
		$this->SessionManager->set('Editing_Record', true);
		$this->SessionManager->set('Edited_Record', $record);
	}
   
	/// Get the data for the model.
	public function getData($selectSetup=array())
	{
		return $this->offsetData(
			array('Records'   => parent::getData($selectSetup),
			      'State'     => array_merge(
				      array('Failures'      => $this->Failures->get(),
				            'Notifications' => $this->Notifications->get()),
				      $this->SessionManager->getAccess())));
	}

	/// Modify a record in the database.
	public function modify($updates)
	{
		$this->updateCurrentRecord($updates);
		$addRecord = $this->SessionManager->get('Current_Record');
		$addData = array($addRecord);
      
		if ($this->validate)
		{
			if (!$this->validate($addData, $this->Joins))
			{
				return false;
			}
		}

		$deleteData = $this->SessionManager->get('Edited_Record');
      
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->SQL->beginTransaction();

			/// \todo Implement modify.
			throw new \Exception('Modify action not yet implemented.');

			// Recurse and process the modify.

			$this->SQL->commit();
			$this->SessionManager->reset();
			$this->Notifications->add('Modify', 'Successful');
		}
		catch (\Exception $e)
		{
			$this->EventManager->notify(
				'Log', array('Level'   => LOG_ERR,
				             'Method'  => __METHOD__,
				             'Message' => $e->getMessage()));
			$this->Failures->add('Modify_Failed', 'Sys_Admin_Notified');
			$this->SQL->rollBack();
		}
	}

	/** Update the current record in the sesssion.
	 *  @param record \array New information to be added to the current record.
	 */
	public function updateCurrentRecord($updateRecord)
	{
		if (!$this->SessionManager->issetKey('Current_Record'))
		{
			throw new \RuntimeException(
				__METHOD__ . ' Current_Record is not set for update.');
		}

		$data = $this->Joins->arrangeResults(array($updateRecord));
		$updateRecord = reset($data); 

		if (!empty($updateRecord))
		{
			$currentRecord = $this->SessionManager->get('Current_Record');
			$jointKey = $this->Joins->getJointKey();
	 
			// First merge any joint data.
			if (isset($updateRecord[$jointKey]))
			{
				$updateJoint =& $updateRecord[$jointKey];
	    
				if (!isset($currentRecord[$jointKey]))
				{
					$currentRecord[$jointKey] = array();
				}
	    
				$currentJoint =& $currentRecord[$jointKey];
	    
				foreach ($updateJoint as $key => $val)
				{
					if (isset($currentJoint[$key]))
					{
						$currentJoint[$key] = array_merge($currentJoint[$key], $val);
					}
					else
					{
						$currentJoint[$key] = $val;
					}
				}
			}
	 
			// Now merge any non-joint data.
			unset($updateRecord[$jointKey]);
			$currentRecord = array_merge($currentRecord, $updateRecord);
	 
			$this->SessionManager->set('Current_Record', $currentRecord);
		}

		$this->ajaxData = array('Success' => true);
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/
   
	protected function addEntries(&$data, $Join)
	{
		if (!$Join->isAdminManaged() || empty($data))
		{
			return;
		}

		$tableName = $Join->getTableName();
		$childField = $Join->getChildField();
      
		// Work out whether we need a List_ID to be calculated.
		if (isset($childField))
		{
			$listID = $this->tableListID->getNew(
				$tableName, $childField);
	 
			foreach ($data as &$record)
			{
				$record[$childField] = $listID;
			}
		}

		// We only need to add the records for the current table so we should
		// remove the joint data on a copy of the data.
		$addData = $data;
      
		foreach ($addData as &$addRecord)
		{
			unset($addRecord[$Join->getJointKey()]);
		}

		$this->SQL->insert($tableName, array_keys(reset($addData)), $addData);
      
		// If the listID was null then this is a one to one relationship with an
		// ID that we can obtain with lastInsertID.  We set it so that it can be
		// used in any feedback of data to the parent records.
		if (!isset($listID))
		{
			// If it is not a 1 to 1 relationship then we are in trouble.
			if (count($addData) !== 1)
			{
				throw new \RuntimeException(
					__METHOD__ . ' child field not set for Child Field: ' .
					var_export($childField, true) . ' in table: ' .
					var_export($tableName, true));
			}
	 
			foreach ($data as &$childRecord)
			{
				$childRecord[$childField] = $this->SQL->lastInsertID($childField);
			}  
		}
       
		// Return the data which has been altered with any new list ids.
		return $data;
	}
   
	/** Delete the records for the current table.
	 *  @param data \array The records to be deleted.
	 *  @param Join \obj The Joins object.
	 */
	protected function deleteEntries($data, $Join)
	{
		foreach ($data as $record)
		{
			unset($record[$Join->getJointKey()]);
			$this->SQL->delete($Join->getTableName(), $record);
		}
	}   

	/** Recurse the data and Joins calling the appropriate callbacks.  This
	 *  recursion is used to call the callback functions in varying breadth first
	 *  or depth first manner.
	 *
	 *  @param callbacks \array Callbacks to be called recursively on the data of
	 *  the format:
	 *  \verbatim
	 *  // Supply callbacks as values in this array.
	 *  // The comments show what the callback will receive.
	 *  array('Breadth_First_Data'   => NULL, // Data,          Joins.
	 *       'Breadth_First_Record' => NULL, // Record,        Joins.
	 *       'Breadth_First_Parent' => NULL, // Parent Record, Child Join.
	 *       'Depth_First_Data'     => NULL, // Parent Record, Child Join.
	 *       'Depth_First_Record'   => NULL, // Record,        Joins.
	 *       'Depth_First_Parent'   => NULL) // Data,          Joins.
	 *  \endverbatim
	 *  For any one bit of data these functions are called in the following order:
	 *  \verbatim
	 *  1 Breadth_First_Data
	 *  2 Breadth_First_Record
	 *  3 Breadth_First_Parent
	 *  4 Depth_First_parent
	 *  5 Depth_First_Record
	 *  6 Depth_First_Data
	 *  \endverbatim
	 *  Order is important when callback functions alter the data they receive.
	 *
	 *  @param data \array The data to traverse.
	 *  @param Joins \obj The Joins object to traverse the data with.
	 *  \return The data after possibly being modified by the callbacks.
	 */
	protected function recurse(Array $callbacks, Array &$data, $Joins)
	{
		$jointKey = $Joins->getJointKey();
		$childJoins = $Joins->getJoins();
      
		$this->call($callbacks, 'Breadth_First_Data', &$data, $Joins);
      
		// Loop through the data record by record, recursing downwards through the
		// joint data referenced by the joins.
		foreach ($data as &$record)
		{
			$this->call($callbacks, 'Breadth_First_Record', &$record, $Joins);
      
			foreach ($childJoins as $Join)
			{	    
				$parentField = $Join->getParentField();
	    
				if (isset($record[$jointKey][$parentField]))
				{
					$this->call($callbacks, 'Breadth_First_Parent', &$record, $Join);
	       
					$record[$jointKey][$parentField] = $this->recurse(
						$callbacks, $record[$jointKey][$parentField], $Join);
	       
					$this->call($callbacks, 'Depth_First_Parent', &$record, $Join);
				}
			}
	 
			$this->call($callbacks, 'Depth_First_Record', &$record, $Joins);
		}
      
		$this->call($callbacks, 'Depth_First_Data', &$data, $Joins);
      
		return $data;
	}
   
	/** Feeback the List_ID from the joint child records into the parent record.
	 *  @param parentRecord \array The parent record.
	 *  @param Join \object The Join to the child record. 
	 */
	protected function feedbackListID(&$parentRecord, $Join)
	{
		$jointKey    = $Join->getJointKey();
		$childField  = $Join->getChildField();
		$parentField = $Join->getParentField();

		if (!isset($childField))
		{
			return;
		}
      
		if (isset($parentRecord[$jointKey][$parentField]))
		{
			$firstChildRecord = reset($parentRecord[$jointKey][$parentField]);

			if (is_array($firstChildRecord) &&
			    isset($firstChildRecord[$childField]))
			{
				$parentRecord[$parentField] = $firstChildRecord[$childField];
			}
		}
	}
   
	/// Get the event map used for connecting events.
	protected function getProcessingEventMap()
	{
		return array_merge(
			parent::getProcessingEventMap(),
			array('Add'           	       => 'add',
			      'Cancel'        	       => 'cancel',
			      'Create_New'    	       => 'createNew',
			      'Delete_Cancel' 	       => 'deleteCancel',
			      'Delete_Confirm'	       => 'deleteConfirm',
			      'Delete_Request'	       => 'deleteRequest',
			      'Edit'          	       => 'edit',
			      'Modify'        	       => 'modify',
			      'Update_Current_Record' => 'updateCurrentRecord'));
	}
   
	/** Validate all of the data specified with the Joins.
	 *  @param data \array The data to validate.
	 *  @param Joins \obj The Joins object to validate the data with.
	 *  @return \bool Whether the data is valid or not.
	 */
	protected function validate($data, $Joins)
	{
		$this->recurse(
			array('Depth_First_Data' => array($this, 'validateEntries')),
			$data,
			$Joins);
      
		return $this->Failures->isEmpty();
	}

	/** Validate the data for the table.
	 */
	protected function validateEntries($data, $join)
	{
		if (!$join->isAdminManaged() || empty($data))
		{
			return;
		}

		foreach ($data as $record)
		{
			// We validate, ignoring the joint key field and any child and parent
			// fields which are set automatically.
			$ignoredFields = array($join->getJointKey(), $join->getChildField());
			$joins = $join->getJoins();
	 
			foreach ($joins as $j)
			{
				$ignoredFields[] = $j->getParentField();
			}

			if (!$join->isValid($record, $ignoredFields))
			{
				$this->Failures->append($join->getFailures());
			}
		}
	}

	/*******************/
	/* Private Methods */
	/*******************/

	/** Helper function to call a callback.
	 *  @param callbacks \array The array of callbacks that we are calling from.
	 *  @param cb \string The index for the callback that we want to call.
	 *  @param data \array The data to pass to the callback by reference.
	 *  @param Joins \object The Joins object to pass to the callback.
	 */
	private function call(Array $callbacks, $cb, Array &$data, $Joins)
	{
		if (isset($callbacks[$cb]))
		{
			call_user_func($callbacks[$cb], &$data, $Joins);
		}
	}
}
// EOF