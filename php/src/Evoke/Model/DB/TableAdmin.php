<?php
namespace Evoke\Model\DB;
/// Provide a CRUD interface to a database table.
class TableAdmin extends Table implements \Evoke\Core\Iface\Model\Admin
{
	/** @property $autoFields
	 *  Fields \array for fields that are auto_increment.
	 */
	protected $autoFields;

	/** @property $Failures
	 *  Failure MessageTree \object
	 */
	protected $Failures;

	/** @property $Info
	 *  DB Table Information \object
	 */
	protected $Info;

	/** @property $Notifications
	 *  Notification MessageTree \object
	 */
	protected $Notifications;

	/** @property $SessionManager
	 *  Session Manager \object
	 */
	protected $SessionManager;

	/** @property $validate
	 *  \bool Whether to validate the data.
	 */
	protected $validate;
	
	public function __construct(Iface\DB\Table\Info  $Info,
	                            Iface\SessionManager $SessionManager,
	                            Array                $dataPrefix=array(),
	                            Iface\MessageTree    $Failures=NULL,
	                            Iface\MessageTree    $Notifications=NULL,
	                            /* Bool */           $validate=true,
	                            Array                $autoFields=array('ID'))
	{
		parent::__construct($dataPrefix);
      
		$this->autoFields     = $autoFields;
		$this->Failures       = $Failures;
		$this->Info           = $Info;
		$this->Notifications  = $Notifications;
		$this->SessionManager = $SessionManager;
		$this->validate       = $validate;
	}

	/******************/
	/* Public Methods */
	/******************/

	/// Add a record to the table.
	public function add($record)
	{
		$this->Failures = NULL;

		// Let the database choose the automatic fields.
		foreach ($this->autoFields as $auto)
		{
			unset($record[$auto]);
		}

		if ($this->validate)
		{
			if (!$this->Info->isValid($record))
			{
				$this->Failures = $this->Info->getFailures();
				return false;
			}
		}
      
		try
		{
			$this->SQL->insert(
				$this->tableName, array_keys($record), $record);

			$this->SessionManager->reset();
			$this->Notifications = $this->Notifications->buildNode(
				'Add', 'Successful');
			return true;
		}
		catch (\Exception $E)
		{
			$msg = 'Table: ' . $this->tableName . ' Exception: ' .
				$E->getMessage();
	 
			$this->EventManager->notify('Log', array('Level'   => LOG_ERR,
			                                         'Message' => $msg,
			                                         'Method'  => __METHOD__));
	 
			$this->Failures->add(
				'Record could not be added to table: ' . $this->tableName,
				'System administrator has been notified of error.');

			return false;
		}
	}

	public function cancel()
	{
		$this->SessionManager->reset();
	}

	/// Begin creating a new record.
	public function createNew()
	{
		$this->SessionManager->set('New_Record', true);
		$this->SessionManager->set('Current_Record', array());
		$this->SessionManager->set('Editing_Record', true);
		$this->SessionManager->set('Edited_Record', array());
	}

	/// Cancel the current delete that was requested.
	public function deleteCancel()
	{
		$this->SessionManager->reset();
	}
   
	// Delete a record from the table.
	public function deleteConfirm(Array $record)
	{
		$this->Failures->reset();
      
		try
		{
			$this->SQL->delete($this->tableName, $record);

			$this->SessionManager->reset();
			$this->Notifications->add('Delete', 'Successful');
			return true;
		}
		catch (\Exception $e)
		{
			$msg = 'Table: ' . $this->tableName . ' Exception: ' .
				$e->getMessage();
	 
			$this->EventManager->notify('Log', array('Level'   => LOG_ERR,
			                                         'Message' => $msg,
			                                         'Method'  => __METHOD__));
	 
			$this->Failures->add(
				'Record could not be deleted from table: ' .
				$this->tableName,
				'System administrator has been notified of error.');
	 
			return false;
		}
	}

	public function deleteRequest($conditions)
	{
		$this->SessionManager->set('Delete_Request', true);

		$record = $this->SQL->select(
			$this->tableName, '*', $conditions);
      
		$this->SessionManager->set('Delete_Record', $record);
	}
   
	public function edit($record)
	{
		$result = $this->SQL->select($this->tableName, '*', $record);

		if ($result === false)
		{
			throw new \RuntimeException('Record_Not_Found');
		}
		else
		{
			$this->SessionManager->set('New_Record', false);
			$this->SessionManager->set('Current_Record', $result[0]);
			$this->SessionManager->set('Editing_Record', true);
			$this->SessionManager->set('Edited_Record', $record);
			return true;
		}
	}

	/** Get the data.  This is the administration state plus the underlying
	 *  from the parent class.
	 */
	public function getData()
	{
		return array('Records' => parent::getData(),
		             'State'   => $this->SessionManager->getAccess());
	}
   
	/// Get the currently edited record.
	public function getCurrentRecord()
	{
		return $this->SessionManager->get('Current_Record');      
	}

	/// Whether a record is being edited.
	public function isEditingRecord()
	{
		return $this->SessionManager->is('Editing_Record', true);
	}  

	/// Whether the current record is a new entry.
	public function isNewRecord()
	{
		return $this->SessionManager->is('New_Record', true);
	}

	/** Modify a record in the table.
	 *  \returns \bool Whether the modification was successful.
	 */
	public function modify($record)
	{
		try
		{
			$this->Failures->reset();

			if ($this->validate)
			{
				if (!$this->Info->isValid($record))
				{
					$this->Failures = $this->Info->getFailures();
					return false;
				}
			}

			if ($this->SessionManager->issetKey('Edited_Record'))
			{
				$oldRecord = $this->SessionManager->get('Edited_Record');
				$this->SQL->update($this->tableName, $record, $oldRecord);
			}
			else
			{
				$this->SQL->insert(
					$this->tableName, array_keys($record), $record);
			}

			$this->Notifications->add('Modify', 'Successful');
			$this->SessionManager->reset();
			return true;
		}
		catch (\Exception $E)
		{
			$msg = 'Table: ' . $this->tableName . ' Exception: ' .
				$E->getMessage();
	 
			$this->EventManager->notify('Log', array('Level'   => LOG_ERR,
			                                         'Message' => $msg,
			                                         'Method'  => __METHOD__));
	 
			$this->Failures->add(
				'Record could not be modified in table: ' .
				$this->tableName,
				'System administrator has been notified of error.');

			return false;
		}
	}
}
// EOF