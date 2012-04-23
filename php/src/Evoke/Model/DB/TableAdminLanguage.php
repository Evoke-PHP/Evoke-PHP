<?php
namespace Evoke\Model\DB;
/** \todo PDO transactions get auto-committed by MYSQL and other DB's when
 *  structure changes are made (such as DROP COLUMN, ADD COLUMN etc.).  We
 *  need to take this into account for the transactions.
 */
class TableAdminLanguage extends TableAdmin
{
	/******************/
	/* Public Methods */
	/******************/

	/** Add a language to the database, updating all tables that have a language
	 *  field with the newly defined language.  Updating a table definition
	 */
	public function add($record)
	{
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sQL->beginTransaction();
	 
			if (parent::add($record))
			{
				$langFields = $this->sQL->select('Language_Fields', '*');

				foreach ($langFields as $langField)
				{
					if (empty($langField['Field_Name']))
					{
						$newField = $record['Language'];
					}
					else
					{
						$newField =
							$langField['Field_Name'] . '_' . $record['Language'];
					}
	       
					$this->sQL->addColumn($langField['Table_Name'],
					                      $newField,
					                      $langField['Field_Type']);
				}

				$this->sQL->commit();
			}
			else
			{
				$this->sQL->rollBack();
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Failure adding language to database due to exception:  ' .
				$e->getMessage();

			$this->eventManager->notify('Log', array('Level'   => LOG_ERR,
			                                         'Message' => $msg,
			                                         'Method'  => __METHOD__));

			$this->failures->add(
				'Failure Adding Language',
				'System Administrator has been notified.');

			$this->sQL->rollBack();
		}
	}

	/// Execute a delete that has been confirmed.
	public function deleteConfirm(Array $record)
	{
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sQL->beginTransaction();

			// Get the name of the language before we delete it.
			$result = $this->sQL->select('Language', 'Language', $record);
			$dropLang = $result[0]['Language'];
	 
			if (parent::deleteConfirm($record))
			{
				$langFields = $this->sQL->select('Language_Fields', '*');

				foreach ($langFields as $langField)
				{
					if (empty($langField['Field_Name']))
					{
						$dropField = $dropLang;
					}
					else
					{
						$dropField = $langField['Field_Name'] . '_' . $dropLang;
					}
	       
					$this->sQL->dropColumn($langField['Table_Name'], $dropField);
				}

				$this->sQL->commit();
			}
			else
			{
				$this->sQL->rollBack();
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Failure deleting language from database due to exception:  ' .
				$e->getMessage();

			$this->eventManager->notify('Log', array('Level'   => LOG_ERR,
			                                         'Message' => $msg,
			                                         'Method'  => __METHOD__));

			$this->failures->add(
				'Failure Deleting Language',
				'System Administrator has been notified.');

			$this->sQL->rollBack();
		}
	}

	public function modify($record)
	{
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sQL->beginTransaction();

			// Get the name of the language before we modify it.
			$result = $this->sQL->select(
				'Language',
				'Language',
				$this->sessionManager->get('Edited_Record'));
	 
			$oldLang = $result[0]['Language'];
	 
			if (parent::modify($record))
			{
				// If the language has changed.
				if ($oldLang !== $record['Language'])
				{
					$langFields =
						$this->sQL->select('Language_Fields', '*');

					foreach ($langFields as $langField)
					{
						if (empty($langField['Field_Name']))
						{
							$oldField = $oldLang;
							$newField = $record['Language'];
						}
						else
						{
							$oldField = $langField['Field_Name'] . '_' . $oldLang;
							$newField =
								$langField['Field_Name'] . '_' . $record['Language'];
						}
	       
						$this->sQL->changeColumn($langField['Table_Name'],
						                         $oldField,
						                         $newField,
						                         $langField['Field_Type']);
					}
				}
	    
				$this->sQL->commit();
			}
			else
			{
				$this->sQL->rollBack();
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Failure modifying language in database due to exception:  ' .
				$e->getMessage();
	 
			$this->eventManager->notify('Log', array('Level'   => LOG_ERR,
			                                         'Message' => $msg,
			                                         'Method'  => __METHOD__));

			$this->failures->add(
				'Failure Modifying Language',
				'System Administrator has been notified.');

			$this->sQL->rollBack();
		}
	}
}
// EOF