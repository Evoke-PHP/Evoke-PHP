<?php
namespace Evoke\Model\Mapper\DB;

use Evoke\Message\TreeIface,
	Evoke\Model\AdminIface,
	Evoke\Persistence\DB\SQLIface,
	Evoke\Persistence\DB\Table\JoinsIface,
	Evoke\Persistence\DB\Table\ListIDIface,
	Evoke\Persistence\SessionManagerIface,
	Exception,
	RuntimeException;

/**
 * JointAdmin
 *
 * JointAdmin provides A CRUD interface to a set of joint tables.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class JointAdmin extends Joint implements AdminIface
{
	/** 
	 * MessageTree of any failures.
	 * @var Evoke\Message\TreeIface
	 */
	protected $failures;

	/**
	 * MessageTree of any notifications.
	 * @var Evoke\Message\TreeIface
	 */
	protected $notifications;

	/**
	 * Session Manager
	 * @var Evoke\Persistence\SessionManagerIface
	 */
	protected $sessionManager;

	/**
	 * Database List ID management
	 * @var Evoke\Persistence\DB\Table\ListIDIface
	 */
	protected $tableListID;
	
	/**
	 * Construct an Administration Model of a joint set of database tables.
	 *
	 * @param Evoke\Persistence\DB\SQLIface
	 *                SQL object.   
	 * @param string  The table name where joins start from.
	 * @param Evoke\Persistence\DB\Table\JoinsIface
	 *                Joins object.
	 * @param Evoke\Persistence\SessionManagerIface SessionManager object.
	 * @param Evoke\Persistence\DB\Table\ListIDIface
	 *                DB List ID Table object.
	 * @param Evoke\Message\TreeIface
	 *                Failure messages object.
	 * @param Evoke\Message\TreeIface
	 *                Notification messages object.
	 * @param mixed[] Select statement settings.
	 * @param bool    Whether to validate the data.
	 */
	public function __construct(SQLIface            $sql,
	                            /* String */        $tableName,
	                            JoinsIface          $joins,
	                            SessionManagerIface $sessionManager,
	                            ListIDIface         $tableListID,
	                            TreeIface           $failures,
	                            TreeIface           $notifications,
	                            Array               $select   = array(),
	                            /* Bool */          $validate = true)
	{
		/// @todo This class is broken, needs fixing.
		throw new RuntimeException('Fix this class, call by ref was altered.');
		
		parent::__construct($sql, $tableName, $joins, $select);

		$this->failures       = $failures;
		$this->notifications  = $notifications;
		$this->sessionManager = $sessionManager;
		$this->tableListID    = $tableListID;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add the joint record to the database.
	 *
	 * @param mixed[] Any new data for the record to be added.
	 */
	public function add(Array $record)
	{
		$this->failures->reset();

		$this->updateCurrentRecord($record);
		$record = $this->sessionManager->get('Current_Record');
		$data = array($record);
      
		if ($this->validate && !$this->validate($data, $this->joins))
		{
			return false;
		}
            
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sql->beginTransaction();

			$this->recurse(
				array('Depth_First_Data'   => array($this, 'addEntries'),
				      'Depth_First_Parent' => array($this, 'feedbackListID')),
				$data,
				$this->joins);
	 
			$this->sql->commit();
			$this->sessionManager->reset();
			$this->notifications->add('Add', 'Successful');
		}
		catch (Exception $e)
		{
			trigger_error($e->getMessage(), E_USER_WARNING);
			$this->failures->add('Add_Failed', 'Sys_Admin_Notified');	 
			$this->sql->rollBack();
		}
	}

	/**
	 * Cancel the editing of the record.
	 */
	public function cancel()
	{
		$this->sessionManager->reset();
	}

	/**
	 * Begin creating a new record.
	 */
	public function createNew()
	{
		$currentRecord = $this->joins->getEmpty();
		$this->sessionManager->set('New_Record', true);
		$this->sessionManager->set('Current_Record', $currentRecord);
		$this->sessionManager->set('Editing_Record', true);
		$this->sessionManager->set('Edited_Record', array());
	}

	/**
	 * Delete a record.
	 *
	 * @param mixed[] The record to delete.
	 */
	public function delete(Array $record)
	{

	}
	
	/**
	 * Cancel the currently requested deletion.
	 */
	public function deleteCancel()
	{
		$this->sessionManager->reset();
	}

	/**
	 * Delete the specified record.
	 *
	 * @param mixed[] The record to delete.
	 */	 
	public function deleteConfirm(Array $record)
	{
		$this->failures->reset();

		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sql->beginTransaction();
			$deleteRecord = $this->sessionManager->get('Delete_Record');
	 
			$this->recurse(
				array('Breadth_First_Data' => array($this, 'deleteEntries')),
				$deleteRecord,
				$this->joins);
	 
			$this->sessionManager->reset();
			$this->sql->commit();
			$this->notifications->add('Delete', 'Successful');
		}
		catch (Exception $e)
		{
			trigger_error($e->message(), E_USER_WARNING);
			$this->failures->add('Delete_Failed', 'Sys_Admin_Notified');
			$this->sql->rollBack();
		}
	}

	/**
	 * Request that a record should be deleted, but only after confirmation from
	 * the user.
	 *
	 * @param mixed[] The record that should be requested to be deleted.
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
      
		$this->sessionManager->set('Delete_Request', true);
		$this->sessionManager->set('Delete_Record', $record);
	}

	/**
	 * Edit a record.
	 *
	 * @param mixed[] The record that should be edited.
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
			trigger_error(
				'Cannot edit multiple records at once.', E_USER_WARNING);	 
			$this->failures->add('Edit_Failed', 'Sys_Admin_Notified');
			return;
		}

		$record = reset($data);
      
		// Set the session to show that the record is being edited.
		$this->sessionManager->set('New_Record', false);
		$this->sessionManager->set('Current_Record', $record);
		$this->sessionManager->set('Editing_Record', true);
		$this->sessionManager->set('Edited_Record', $record);
	}
   
	/**
	 * Get the data for the model.
	 */
	public function getData($selectSetup=array())
	{
		return array('Records'   => parent::getData($selectSetup),
		             'State'     => array_merge(
			             array('Failures'      => $this->failures->get(),
			                   'Notifications' => $this->notifications->get()),
			             $this->sessionManager->getAccess()));
	}

	/**
	 * Modify a record in the database.
	 *
	 * @param mixed[] The record that is to be modified.
	 * @param mixed[] The new values for the record.
	 */
	public function modify(Array $oldRecord, Array $newRecord)
	{
		$this->updateCurrentRecord($updates);
		$addRecord = $this->sessionManager->get('Current_Record');
		$addData = array($addRecord);
      
		if ($this->validate)
		{
			if (!$this->validate($addData, $this->joins))
			{
				return false;
			}
		}

		$deleteData = $this->sessionManager->get('Edited_Record');
      
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sql->beginTransaction();

			/// @todo Implement modify.
			throw new Exception('Modify action not yet implemented.');

			// Recurse and process the modify.

			$this->sql->commit();
			$this->sessionManager->reset();
			$this->notifications->add('Modify', 'Successful');
		}
		catch (Exception $e)
		{
			trigger_error($e->getMessage(), E_USER_WARNING);
			$this->failures->add('Modify_Failed', 'Sys_Admin_Notified');
			$this->sql->rollBack();
		}
	}

	/**
	 * Update the current record in the sesssion.
	 *
	 * @param mixed[] New information to be added to the current record.
	 */
	public function updateCurrentRecord($updateRecord)
	{
		if (!$this->sessionManager->issetKey('Current_Record'))
		{
			throw new RuntimeException(
				__METHOD__ . ' Current_Record is not set for update.');
		}

		$data = $this->joins->arrangeResults(array($updateRecord));
		$updateRecord = reset($data); 

		if (!empty($updateRecord))
		{
			$currentRecord = $this->sessionManager->get('Current_Record');
			$jointKey = $this->joins->getJointKey();
	 
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
						$currentJoint[$key] = array_merge($currentJoint[$key],
						                                  $val);
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
	 
			$this->sessionManager->set('Current_Record', $currentRecord);
		}

		$this->ajaxData = array('Success' => true);
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/
   
	/**
	 * Add the entries for the joint data.
	 *
	 */
	protected function addEntries(&$data, $join)
	{
		if (!$join->isAdminManaged() || empty($data))
		{
			return;
		}

		$tableName = $join->getTableName();
		$childField = $join->getChildField();
      
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
			unset($addRecord[$join->getJointKey()]);
		}

		$this->sql->insert($tableName, array_keys(reset($addData)), $addData);
      
		// If the listID was null then this is a one to one relationship with an
		// ID that we can obtain with lastInsertID.  We set it so that it can be
		// used in any feedback of data to the parent records.
		if (!isset($listID))
		{
			// If it is not a 1 to 1 relationship then we are in trouble.
			if (count($addData) !== 1)
			{
				throw new RuntimeException(
					__METHOD__ . ' child field not set for Child Field: ' .
					var_export($childField, true) . ' in table: ' .
					var_export($tableName, true));
			}
	 
			foreach ($data as &$childRecord)
			{
				$childRecord[$childField] =
					$this->sql->lastInsertID($childField);
			}  
		}
       
		// Return the data which has been altered with any new list ids.
		return $data;
	}
   
	/**
	 * Delete the records for the current table.
	 *
	 *  @param mixed[]                               The records to be deleted.
	 *  @param Evoke\Persistence\DB\Table\JoinsIface The Joins object.
	 */
	protected function deleteEntries($data, $join)
	{
		foreach ($data as $record)
		{
			unset($record[$join->getJointKey()]);
			$this->sql->delete($join->getTableName(), $record);
		}
	}   

	/**
	 * Recurse the data and Joins calling the appropriate callbacks.  This
	 * recursion is used to call the callback functions in varying breadth first
	 * or depth first manner.
	 *
	 * @param mixed[] Callbacks to be called recursively on the data.
	 *
	 * <pre><code>
	 * // Supply callbacks as values in this array.
	 * // The comments show what the callback will receive.
	 * array('Breadth_First_Data'   => NULL, // Data,          Joins.
	 *      'Breadth_First_Record' => NULL, // Record,        Joins.
	 *      'Breadth_First_Parent' => NULL, // Parent Record, Child Join.
	 *      'Depth_First_Data'     => NULL, // Parent Record, Child Join.
	 *      'Depth_First_Record'   => NULL, // Record,        Joins.
	 *      'Depth_First_Parent'   => NULL) // Data,          Joins.
	 * </code></pre>
	 *
	 * For any one bit of data these functions are called in the following
	 * order:
	 *
	 * <ol>
	 *	  <li>Breadth_First_Data</li>
	 *    <li>Breadth_First_Record</li>
	 *    <li>Breadth_First_Parent</li>
	 *    <li>Depth_First_parent</li>
	 *    <li>Depth_First_Record</li>
	 *    <li>Depth_First_Data</li>
	 * </ol>
	 *
	 * Order is important when callback functions alter the data they receive.
	 *
	 * @param mixed[]  The data to traverse.
	 * @param Evoke\Persistence\DB\Table\JoinsIface
	 *                 The joins object to traverse the data with.
	 * @return mixed[] The data after possibly being modified by the callbacks.
	 */
	protected function recurse(Array      $callbacks,
	                           Array      &$data,
	                           JoinsIface $joins)
	{
		$jointKey = $joins->getJointKey();
		$childJoins = $joins->getJoins();
      
		$this->call($callbacks, 'Breadth_First_Data', $data, $joins);
      
		// Loop through the data record by record, recursing downwards through
		// the joint data referenced by the joins.
		foreach ($data as &$record)
		{
			$this->call($callbacks, 'Breadth_First_Record', $record, $joins);
      
			foreach ($childJoins as $join)
			{	    
				$parentField = $join->getParentField();
	    
				if (isset($record[$jointKey][$parentField]))
				{
					$this->call(
						$callbacks, 'Breadth_First_Parent', $record, $join);
	       
					$record[$jointKey][$parentField] = $this->recurse(
						$callbacks, $record[$jointKey][$parentField], $join);
	       
					$this->call(
						$callbacks, 'Depth_First_Parent', $record, $join);
				}
			}
	 
			$this->call($callbacks, 'Depth_First_Record', $record, $joins);
		}
      
		$this->call($callbacks, 'Depth_First_Data', $data, $joins);
      
		return $data;
	}
   
	/**
	 * Feeback the List_ID from the joint child records into the parent record.
	 *
	 * @param mixed[] The parent record.
	 * @param Evoke\Persistence\DB\Table\JoinsIface
	 *                The Join to the child record.
	 */
	protected function feedbackListID(&$parentRecord, $join)
	{
		$jointKey    = $join->getJointKey();
		$childField  = $join->getChildField();
		$parentField = $join->getParentField();

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
   
	/**
	 * Validate all of the data specified with the Joins.
	 *
	 * @param  mixed[] The data to validate.
	 * @param  Evoke\Persistence\DB\Table\JoinsIface
	 *                 The Joins object to validate the data with.
	 * @return bool Whether the data is valid or not.
	 */
	protected function validate($data, JoinsIface $joins)
	{
		$this->recurse(
			array('Depth_First_Data' => array($this, 'validateEntries')),
			$data,
			$joins);
      
		return $this->failures->isEmpty();
	}

	/**
	 * Validate the entries for the table.
	 *
	 * @param mixed[]                               The data for the table.
	 * @param Evoke\Persistence\DB\Table\JoinsIface The join for the data.
	 */
	protected function validateEntries(Array      $data,
	                                   JoinsIface $join)
	{
		if (!$join->isAdminManaged() || empty($data))
		{
			return;
		}

		foreach ($data as $record)
		{
			// We validate, ignoring the joint key field and any child and
			// parent fields which are set automatically.
			$ignoredFields = array($join->getJointKey(),
			                       $join->getChildField());
			$joins = $join->getJoins();
	 
			foreach ($joins as $j)
			{
				$ignoredFields[] = $j->getParentField();
			}

			if (!$join->isValid($record, $ignoredFields))
			{
				$this->failures->append($join->getFailures());
			}
		}
	}

	/*******************/
	/* Private Methods */
	/*******************/

	/**
	 * Helper function to call a callback.
	 *
	 * @param mixed[] The array of callbacks that we are calling from.
	 * @param string  Index for the callback that we want to call.
	 * @param mixed[] The data to pass to the callback by reference.
	 * @param Evoke\Persistence\DB\Table\JoinsIface
	 *                The Joins object to pass to the callback.
	 */
	private function call(Array $callbacks, $cb, Array &$data, $joins)
	{
		if (isset($callbacks[$cb]))
		{
			call_user_func($callbacks[$cb], $data, $joins);
		}
	}
}
// EOF