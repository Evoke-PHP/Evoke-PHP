<?php
namespace Evoke\Model\DB;

use Evoke\Iface\Core as ICore;

/// Provide a CRUD interface to a database table.
class TableAdmin extends Table implements \Evoke\Iface\Model\Admin
{
	/** @property $autoFields
	 *  Fields @array for fields that are auto_increment.
	 */
	protected $autoFields;

	/** @property $failures
	 *  Failure MessageTree @object
	 */
	protected $failures;

	/** @property $info
	 *  DB Table Information @object
	 */
	protected $info;

	/** @property $notifications
	 *  Notification MessageTree @object
	 */
	protected $notifications;

	/** @property $sessionManager
	 *  Session Manager @object
	 */
	protected $sessionManager;

	/** @property $validate
	 *  @bool Whether to validate the data.
	 */
	protected $validate;

	/** Construct the Table Administration Model.
	 *  @param sql            @object SQL object.
	 *  @param info           @object Table Info object.
	 *  @param sessionManager @object Session Manager object.
	 *  @param dataPrefix     @array  Data prefix for the offset of the model
	 *                                data.
	 *  @param failures       @object Failures object.
	 *  @param notifications  @object Notifications object.
	 *  @param validate       @bool   Whether to validate the data.
	 *  @param autoFields     @array  Which fields should be left to get an
	 *                                automatic value from the database.
	 */
	public function __construct(ICore\DB\SQL         $sql,
	                            ICore\DB\Table\Info  $info,
	                            ICore\SessionManager $sessionManager,
	                            ICore\MessageTree    $failures,
	                            ICore\MessageTree    $notifications,
	                            Array                $dataPrefix = array(),
	                            /* Bool */           $validate   = true,
	                            Array                $autoFields = array('ID'))
	{
		parent::__construct($sql, $dataPrefix);
      
		$this->autoFields     = $autoFields;
		$this->failures       = $failures;
		$this->info           = $info;
		$this->notifications  = $notifications;
		$this->sessionManager = $sessionManager;
		$this->validate       = $validate;
	}

	/******************/
	/* Public Methods */
	/******************/

	/// Add a record to the table.
	public function add($record)
	{
		$this->failures = NULL;

		// Let the database choose the automatic fields.
		foreach ($this->autoFields as $auto)
		{
			unset($record[$auto]);
		}

		if ($this->validate)
		{
			if (!$this->info->isValid($record))
			{
				$this->failures = $this->info->getFailures();
				return false;
			}
		}
      
		try
		{
			$this->sql->insert(
				$this->tableName, array_keys($record), $record);

			$this->sessionManager->reset();
			$this->notifications = $this->notifications->buildNode(
				'Add', 'Successful');
			return true;
		}
		catch (\Exception $e)
		{
			$msg = 'Table: ' . $this->tableName . ' Exception: ' .
				$e->getMessage();
	 
			$this->eventManager->notify('Log', array('Level'   => LOG_ERR,
			                                         'Message' => $msg,
			                                         'Method'  => __METHOD__));
	 
			$this->failures->add(
				'Record could not be added to table: ' . $this->tableName,
				'System administrator has been notified of error.');

			return false;
		}
	}

	public function cancel()
	{
		$this->sessionManager->reset();
	}

	/// Begin creating a new record.
	public function createNew()
	{
		$this->sessionManager->set('New_Record', true);
		$this->sessionManager->set('Current_Record', array());
		$this->sessionManager->set('Editing_Record', true);
		$this->sessionManager->set('Edited_Record', array());
	}

	/// Cancel the current delete that was requested.
	public function deleteCancel()
	{
		$this->sessionManager->reset();
	}
   
	// Delete a record from the table.
	public function deleteConfirm(Array $record)
	{
		$this->failures->reset();
      
		try
		{
			$this->sql->delete($this->tableName, $record);

			$this->sessionManager->reset();
			$this->notifications->add('Delete', 'Successful');
			return true;
		}
		catch (\Exception $e)
		{
			$msg = 'Table: ' . $this->tableName . ' Exception: ' .
				$e->getMessage();
	 
			$this->eventManager->notify('Log', array('Level'   => LOG_ERR,
			                                         'Message' => $msg,
			                                         'Method'  => __METHOD__));
	 
			$this->failures->add(
				'Record could not be deleted from table: ' .
				$this->tableName,
				'System administrator has been notified of error.');
	 
			return false;
		}
	}

	public function deleteRequest($conditions)
	{
		$this->sessionManager->set('Delete_Request', true);

		$record = $this->sql->select(
			$this->tableName, '*', $conditions);
      
		$this->sessionManager->set('Delete_Record', $record);
	}
   
	public function edit($record)
	{
		$result = $this->sql->select($this->tableName, '*', $record);

		if ($result === false)
		{
			throw new \RuntimeException('Record_Not_Found');
		}
		else
		{
			$this->sessionManager->set('New_Record', false);
			$this->sessionManager->set('Current_Record', $result[0]);
			$this->sessionManager->set('Editing_Record', true);
			$this->sessionManager->set('Edited_Record', $record);
			return true;
		}
	}

	/** Get the data.  This is the administration state plus the underlying
	 *  from the parent class.
	 */
	public function getData()
	{
		return array('Records' => parent::getData(),
		             'State'   => $this->sessionManager->getAccess());
	}
   
	/// Get the currently edited record.
	public function getCurrentRecord()
	{
		return $this->sessionManager->get('Current_Record');      
	}

	/// Whether a record is being edited.
	public function isEditingRecord()
	{
		return $this->sessionManager->is('Editing_Record', true);
	}  

	/// Whether the current record is a new entry.
	public function isNewRecord()
	{
		return $this->sessionManager->is('New_Record', true);
	}

	/** Modify a record in the table.
	 *  \returns \bool Whether the modification was successful.
	 */
	public function modify($record)
	{
		try
		{
			$this->failures->reset();

			if ($this->validate)
			{
				if (!$this->info->isValid($record))
				{
					$this->failures = $this->info->getFailures();
					return false;
				}
			}

			if ($this->sessionManager->issetKey('Edited_Record'))
			{
				$oldRecord = $this->sessionManager->get('Edited_Record');
				$this->sql->update($this->tableName, $record, $oldRecord);
			}
			else
			{
				$this->sql->insert(
					$this->tableName, array_keys($record), $record);
			}

			$this->notifications->add('Modify', 'Successful');
			$this->sessionManager->reset();
			return true;
		}
		catch (\Exception $e)
		{
			$msg = 'Table: ' . $this->tableName . ' Exception: ' .
				$e->getMessage();
	 
			$this->eventManager->notify('Log', array('Level'   => LOG_ERR,
			                                         'Message' => $msg,
			                                         'Method'  => __METHOD__));
	 
			$this->failures->add(
				'Record could not be modified in table: ' .
				$this->tableName,
				'System administrator has been notified of error.');

			return false;
		}
	}
}
// EOF