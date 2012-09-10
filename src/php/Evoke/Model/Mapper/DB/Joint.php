<?php
/**
 * Mapper for Joint database tables.
 *
 * @package Model
 */
namespace Evoke\Model\Mapper\DB;

use Evoke\Message\TreeIface,
	Evoke\Model\Mapper\MapperIface,
	Evoke\Persistence\DB\SQLIface,
	Evoke\Persistence\DB\Table\JoinsIface,
	Evoke\Persistence\DB\Table\ListIDIface,
	Exception,
	RuntimeException;

/**
 * Mapper for Joint database tables.
 *
 * Joint provides A CRUD interface to a set of joint tables.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Joint extends JointRead implements MapperIface
{
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
	 * @param Evoke\Persistence\DB\Table\ListIDIface
	 *                DB List ID Table object.
	 * @param mixed[] Select statement settings.
	 */
	public function __construct(SQLIface            $sql,
	                            /* String */        $tableName,
	                            JoinsIface          $joins,
	                            ListIDIface         $tableListID,
	                            Array               $select   = array())
	{
		parent::__construct($sql, $tableName, $joins, $select);

		$this->tableListID    = $tableListID;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Create the joint data record in the database.
	 *
	 * @param mixed[] The data to create.
	 */
	public function create(Array $data = array())
	{
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
		}
		catch (Exception $e)
		{
			$this->sql->rollBack();
			throw $e;
		}
	}

	/**
	 * Delete the data from the joint tables.
	 *
	 * @param mixed[] The conditions to match for deletion.
	 */	 
	public function delete(Array $params = array())
	{
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sql->beginTransaction();
	 
			$this->recurse(
				array('Breadth_First_Data' => array($this, 'deleteEntries')),
				$params,
				$this->joins);
	 
			$this->sql->commit();
		}
		catch (Exception $e)
		{
			$this->sql->rollBack();
			throw $e;
		}
	}

	/**
	 * Modify a record in the database.
	 *
	 * @param mixed[] The record that is to be modified.
	 * @param mixed[] The new values for the record.
	 */
	public function update(Array $old = array(), Array $new = array())
	{
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
		}
		catch (Exception $e)
		{
			$this->sql->rollBack();
			throw $e;
		}
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