<?php
/**
 * Language Mapper
 *
 * @package Model
 */
namespace Evoke\Model\Mapper\DB;

use Evoke\Model\Mapper\MapperIface,
	Evoke\Persistence\DB\SQLIface;

/**
 * Language Mapper
 *
 * Provide the CRUD that manages the language entries in a database.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Language implements MapperIface
{
	/**
	 * The language field setup.  This is the setup for the metadata that holds
	 * the information on which database tables contain language fields.
	 * @var string[]
	 */
	protected $fieldSetup;
	
	/**
	 * The language setup.  This is the setup for the table which holds the
	 * language entries.
	 * @var string[]
	 */
	protected $setup;
		
	/**
	 * Construct a TableLanguage object.
	 *
	 * @param SQLIface SQL object.
	 * @param string[] Language setup.
	 * @param string[] Language Field setup.
	 */
	public function __construct(SQLIface $sql,
	                            Array    $setup      = array(
		                            'Language_Field' => 'Language',
		                            'Table_Name'     => 'Language'),
	                            Array    $fieldSetup = array(
		                            'Field_Field'      => 'Field',
		                            'Field_Type_Field' => 'Field_Type',
		                            'Table_Field'      => 'Table',
		                            'Table_Name'       => 'Language_Field'))
	{
		parent::__construct($sql, $setup['Table_Name']);

		$this->setup      = $setup;
		$this->fieldSetup = $fieldSetup;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add a language to the database, updating all tables that have a language
	 * field with the newly defined language.  Updating the table definitions
	 * cannot be done within a transaction because the table update causes an
	 * immediate commit.
	 *
	 * @var mixed[] The language to add.
	 */
	public function create(Array $data = array())
	{
		$newLanguage = $data[$this->setup['Language_Field']];

		// Create the entry in the Language table.
		$this->sql->insert(
			$this->setup['Table_Name'], array_keys($data), $data);
		
		// Get the language field entries.
		$langFields = $this->sql->select($this->fieldSetup['Table_Name'], '*');
		
		foreach ($langFields as $langField)
		{
			$this->sql->addColumn(
				$langField[$this->fieldSetup['Table_Name']],
				$this->getFieldName(
					$langField[$this->fieldSetup['Field_Field']], $newLanguage),
				$langField['Field_Type']);
		}
	}

	/**
	 * Delete a language record.
	 *
	 * @param mixed[] The record(s) to match for deletion.
	 */
	public function delete(Array $params = array())
	{
		// Get the name of the language before we delete it.
		$recordsToDelete = $this->read($params);
		$langFields = $this->sql->select($this->fieldSetup['Table_Name'], '*');
		
		foreach ($recordsToDelete as $record)
		{
			$removedLanguage = $record[$this->setup['Language_Field']];

			foreach ($langFields as $langField)
			{
				$this->sql->dropColumn(
					$langField[$this->fieldSetup['Table_Name']],
					$this->getFieldName(
						$langField[$this->fieldSetup['Field_Field']],
						$removedLanguage));
			}
		}
	}

	/**
	 * Read the languages supported by the database.
	 *
	 * @param string[] Select options.
	 *
	 * @return mixed[] The languages in the database.
	 */
	public function read(Array $params = array())
	{
		return $this->sql->select($this->setup['Table_Name'], '*', $params);
	}
	
	/**
	 * Update a language record.
	 *
	 * @param mixed[] The record to modify.
	 * @param mixed[] The new values for the record.
	 */
	public function update(Array $old = array(), Array $new = array())
	{
		$oldLanguage = $old[$this->setup['Language_Field']];
		$newLanguage = $new[$this->setup['Language_Field']];
		$langFields = $this->sql->select($this->fieldSetup['Table_Name'], '*');
		
		// Update at most 1 language record.
		$this->sql->update($this->setup['Table_Name'], $new, $old, 1);

		foreach ($langFields as $langField)
		{
			$this->sql->changeColumn(
				$langField[$this->fieldSetup['Table_Name']],
				$this->getFieldName(
					$langField[$this->fieldSetup['Field_Field']],
					$oldLanguage),
				$this->getFieldName(
					$langField[$this->fieldSetup['Field_Field']],
					$newLanguage),
				$langField[$this->fieldSetup['Field_Type_Field']]);
		}
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Get the field name for the specified field and language.  This method can
	 * be overridden if another format is required for the language fields.
	 *
	 * @param string Field name.
	 * @param string Language.
	 *
	 * @return string The field name (e.g Fieldname_Language).  By default if
	 *                the field name is empty then just the language is
	 *                returned.
	 */
	protected function getFieldName($field, $language)
	{
		return empty($field) ? $language : $field . '_' . $language;
	}
}
// EOF