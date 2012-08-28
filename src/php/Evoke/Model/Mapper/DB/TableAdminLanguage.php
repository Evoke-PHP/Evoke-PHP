<?php
/**
 * Language Table Mapper
 *
 * @package Model
 */
namespace Evoke\Model\Mapper\DB;

use Exception;

/**
 * @todo PDO transactions get auto-committed by MYSQL and other DB's when
 * structure changes are made (such as DROP COLUMN, ADD COLUMN etc.).  We
 * need to take this into account for the transactions.
 */

/**
 * Language Table Mapper
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class TableLanguage extends Table
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add a language to the database, updating all tables that have a language
	 * field with the newly defined language.  Updating a table definition
	 *
	 * @var mixed[] The record to add.
	 */
	public function create(Array $data = array())
	{
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sql->beginTransaction();
			
			/// @todo Fix this. The parent create is a single transaction, we
			/// need to implement the whole thing here, based on the surrounding
			/// logic that is in place at the moment.
			if (parent::create($data))
			{
				$langFields = $this->sql->select('Language_Fields', '*');

				foreach ($langFields as $langField)
				{
					if (empty($langField['Field_Name']))
					{
						$newField = $data['Language'];
					}
					else
					{
						$newField =
							$langField['Field_Name'] . '_' .
							$data['Language'];
					}
	       
					$this->sql->addColumn($langField['Table_Name'],
					                      $newField,
					                      $langField['Field_Type']);
				}

				$this->sql->commit();
			}
		}
		catch (Exception $e)
		{
			$this->sql->rollBack();
			throw $e;
		}
	}

	/**
	 * Delete a language record.
	 *
	 * @param mixed[] The record to delete.
	 */
	public function delete(Array $params = array())
	{
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sql->beginTransaction();

			// Get the name of the language before we delete it.
			$result = $this->sql->select('Language', 'Language', $params);
			$dropLang = $result[0]['Language'];
	 
			/// @todo Fix this. The parent delete is a single transaction, we
			/// need to implement the whole thing here, based on the surrounding
			/// logic that is in place at the moment.
			if (parent::delete($params))
			{
				$langFields = $this->sql->select('Language_Fields', '*');

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
	       
					$this->sql->dropColumn($langField['Table_Name'],
					                       $dropField);
				}

				$this->sql->commit();
			}
		}
		catch (Exception $e)
		{
			$this->sql->rollBack();
			throw $e;
		}
	}

	/**
	 * Update a language record.
	 *
	 * @param mixed[] The record to modify.
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

			// Get the name of the language before we modify it.
			$result = $this->sql->select(
				'Language',
				'Language',
				$this->sessionManager->get('Edited_Record'));
	 
			$oldLang = $result[0]['Language'];

			
			/// @todo Fix this. The parent update is a single transaction, we
			/// need to implement the whole thing here, based on the surrounding
			/// logic that is in place at the moment.
			if (parent::update($record))
			{
				// If the language has changed.
				if ($oldLang !== $record['Language'])
				{
					$langFields =
						$this->sql->select('Language_Fields', '*');

					foreach ($langFields as $langField)
					{
						if (empty($langField['Field_Name']))
						{
							$oldField = $oldLang;
							$newField = $record['Language'];
						}
						else
						{
							$oldField = $langField['Field_Name'] . '_' .
								$oldLang;
							$newField =
								$langField['Field_Name'] . '_' .
								$record['Language'];
						}
	       
						$this->sql->changeColumn($langField['Table_Name'],
						                         $oldField,
						                         $newField,
						                         $langField['Field_Type']);
					}
				}
	    
				$this->sql->commit();
			}
		}
		catch (Exception $e)
		{
			$this->sql->rollBack();
			throw $e;
		}
	}
}
// EOF