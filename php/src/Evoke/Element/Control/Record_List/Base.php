<?php
namespace Evoke\Element;

/// Element to represent a list of records.
class Element_Record_List extends Element
{
	/** @property $contentAttribs
	 *  Attributes \array for the content.
	 */
	protected $contentAttribs;

	/** @property $data
	 *  \array The data for the record list.
	 */
	protected $data;

	/** @property $dataAttribs
	 *  Attributes \array for the data.
	 */
	protected $dataAttribs;

	/** @property $editedRecord
	 *  \array The edited record from the record list.
	 */
	protected $editedRecord;

	/** @property $emptyDataAttribs
	 *  Attributes \array for an empty data element.
	 */
	protected $emptyDataAttribs;

	/** @property $fields
	 *  \array of fields in the record list.
	 */
	protected $fields;

	/** @property $headingSetup
	 *  The setup for the headings.
	 */
	protected $headingSetup;

	/** @property $ignoredFields
	 *  \array Fields to be ignored in the record list.
	 */
	protected $ignoredFields;

	/** @property $labels
	 *  \array Labels.
	 */
	protected $labels;

	/** @property $primaryKeys
	 *  \array The primary keys for the record list.
	 */
	protected $primaryKeys;

	/** @property $rowAttribs
	 *  \array Attributes for the record list rows.
	 */
	protected $rowAttribs;

	/** @property $rowButtons
	 *  \array Buttons for each row.
	 */
	protected $rowButtons;

	/** @property $rowButtonsAsForm
	 *  \bool Whether the row buttons should be in a form.
	 */
	protected $rowButtonsAsForm;

	/** @property $rowButtonsAttribs
	 *  \array Attributes for the row buttons.
	 */
	protected $rowButtonsAttribs;

	/** @property $tableName
	 *  \string The table name for the record data.
	 */
	protected $tableName;

	/** @property $translateLabels
	 *  \bool Whether to translate the labels for the fields.
	 */
	protected $translateLabels;

	/** @property $Translator
	 *  Translator \object
	 */
	protected $Translator;

	public function __construct(Array $setup)
	{
		$setup += array('Content_Attribs'     => array('class' => 'Content'),
		                'Data'                => NULL,
		                'Data_Attribs'        => array('class' => 'Data'),
		                'Default_Attribs'     => array('class' => 'Record_List'),
		                'Edited_Record'       => array(),
		                'Empty_Data_Attribs'  => array('class' => 'Empty Data'),
		                'Fields'              => array(),
		                'Heading_Setup'       => NULL,	    
		                'Ignored_Fields'      => array('Joint_Data'),
		                'Labels'              => array(),
		                'Primary_Keys'        => array(),
		                'Row_Attribs'         => array('class' => 'Row'),
		                'Row_Buttons'         => NULL,
		                'Row_Buttons_As_Form' => true,
		                'Row_Buttons_Attribs' => array('class' => 'Row_Buttons'),
		                'Table_Name'          => NULL,
		                'Translate_Labels'    => true,
		                'Translator'          => NULL);

		if (!isset($setup['Data']))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Data');
		}
      
		if (!isset($setup['Row_Buttons']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Row_Buttons');
		}

		if (!isset($setup['Table_Name']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Table_Name');
		}

		if (!$setup['Translator'] instanceof Translator)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Translator');
		}

		$this->data = $setup['Data'];
      
		// Merge the Heading Setup so that information is added to a blank
		// heading setup whereas the default is for only a top heading.
		if (!empty($setup['Heading_Setup']))
		{
			$this->headingSetup = array_merge(
				array('Bottom'          => false,
				      'Buttons'         => array(),
				      'Buttons_Attribs' => array('class' => 'Heading_Buttons'),
				      'Inline'          => false,
				      'Row_Attribs'     => array('class' => 'Heading Row'),
				      'Top'             => false),
				$setup['Heading_Setup']);
		}
      
		parent::__construct(
			array('div',
			      $this->setup['Attribs'],
			      array('Children' => $this->buildRecordListElems())));
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/// Build the elements for the record list.
	protected function buildRecordListElems()
	{
		$fields = $this->getFields();

		// Remove the ignored fields.
		foreach ($this->ignoredFields as $ignored)
		{
			unset($fields[$ignored]);
		}
      
		$headings = $this->getHeadings($fields);
		$headingRow = $this->buildHeadingRow($headings);
		$recordListElems = array();

		if ($this->headingSetup['Top'])
		{
			$recordListElems[] = $headingRow;
		}

		$recordListElems[] = $this->buildContent($fields, $headings);

		if ($this->headingSetup['Bottom'])
		{
			$recordListElems[] = $headingRow;
		}

		return $recordListElems;
	}

	/** Build the content of the record list including any inline headings,
	 *  data and data buttons. (Not the top or bottom headings).
	 *  @param headings \array Array of headings for use in inline headings.
	 *  \return Return the element containing the content of the record list.
	 */
	protected function buildContent($fields, $headings)
	{
		if (empty($this->data))
		{
			$rowElems = array(
				array('div',
				      $this->rowAttribs,
				      array('Children' => array($this->buildEmptyData()))));
		}
		else
		{
			$rowElems = array();
	 
			foreach ($this->data as $row => $rowData)
			{
				$rowElems[] = array(
					'div',
					$this->rowAttribs,
					array('Children' => array(
						      $this->buildRowData($fields, $row, $rowData, $headings),
						      $this->buildRowButtons($row, $rowData))));
			}
		}

		return array('div',
		             $this->contentAttribs,
		             array('Children' => $rowElems));
	}

	/** Build the element for an empty record list.
	 *  @param headingElem \object The heading element that may be used.
	 *  \return \array Array of elements for an empty record list.
	 */
	protected function buildEmptyData()
	{
		return array(
			'div',
			$this->emptyDataAttribs,
			array('Text' => $this->setup['Translator']->get('No_Records_Found')));
	}
   
	/** Build the heading row element.
	 *  @param headings \array Array of heading row elements.
	 *  \return \object The heading row element.
	 */
	protected function buildHeadingRow($headings)
	{
		return array(
			'div',
			$this->headingSetup['Row_Attribs'],
			array('Children' => array(
				      array(
					      'div',
					      $this->dataAttribs,
					      array('Children' => $headings)),
				      array(
					      'div',
					      $this->headingSetup['Buttons_Attribs'],
					      array('Children' => $this->getHeadingButtons())))));
	}

	/** Build the element holding the buttons in a row.
	 *  @param row \mixed The key for the row.
	 *  @param rowData \array The data for the row.
	 *  \return \array Array of elements that make up the buttons.
	 */
	protected function buildRowButtons($row, $rowData)
	{
		if ($this->rowButtonsAsForm)
		{
			return $this->app->getNew(
				'Element_Form_Hidden_Input',
				array('App'            => $this->setup['App'],
				      'Attribs'        => array_merge(
					      array('action' => '',
					            'method' => 'post'),
					      $this->rowButtonsAttribs),
				      'Data'           => $rowData,
				      'Encasing'       => false,
				      'Ignored_Fields' => $this->ignoredFields,
				      'Name_Prefix'    => $this->tableName . '.',
				      'Primary_Keys'   => $this->getPrimaryKeys(),
				      'Submit_Buttons' => $this->rowButtons,
				      'Translator'     => $this->setup['Translator']));
		}
		else
		{
			return array('div',
			             $this->rowButtonsAttribs,
			             array('Children' => $this->getRowButtons($row)));
		}
	}
   
	/** Build the element holding the data in a row.
	 *  @param fields  \array The fields for the row.
	 *  @param row     \mixed The key for the row.
	 *  @param rowData \array The data for the row.
	 *  \return \array Array of elements that make up the data.
	 */
	protected function buildRowData($fields, $row, $rowData, $headings)
	{
		try
		{
			$rowElems = array();

			// Add inline headings and row contents to the row.
			if ($this->headingSetup['Inline'])
			{
				foreach($fields as $field)
				{
					$rowElems[] = array(
						'div',
						array('class' => 'Field_Row'),
						array(
							'Children' => array(
								$headings[$field],
								array(
									'div',
									array('class' => 'Data_Item ' . $field . '_Field'),
									array('Text' => $rowData[$field])))));
				}
			}
			else
			{
				foreach($fields as $field)
				{
					$rowElems[] = array(
						'div',
						array('class' => 'Data_Item ' . $field . '_Field'),
						array('Text' => $rowData[$field]));
				}
			}
	 
			return array(
				'div',
				$this->getDataAttribs($row, $rowData),
				array('Children' => $rowElems));
		}
		catch (Exception $e)
		{
			throw new Exception_Base(
				__METHOD__,
				' Caught exception processing row data: ' .
				var_export($rowData, true),
				$e);
		}
	}

	/** Get the data attributes for the row.
	 *  @param row \mixed The key of the row.
	 *  @param rowData \array The data for the row.
	 */
	protected function getDataAttribs($row, $rowData)
	{
		$dataAttribs = $this->dataAttribs;

		// Counting from 0 the first row should be odd.
		if ($row%2 == 0)
		{
			$dataAttribs['class'] .= ' Odd';
		}
		else
		{
			$dataAttribs['class'] .= ' Even';
		}

		if ($this->isEditedRecord($rowData))
		{
			$dataAttribs['class'] .= ' Edited_Record';
		}

		return $dataAttribs;
	}   

	/// Get the fields for display.
	protected function getFields()
	{
		return $this->setup['Fields'];
	}
   
	/// Get the heading buttons that appear with Top or Bottom headings.
	protected function getHeadingButtons()
	{
		return $this->headingSetup['Buttons'];
	}
   
	/** Build the headings for each field.
	 *  @param fields \array Array of fields.
	 *  \return \array Associative array of the heading elements keyed by field.
	 */
	protected function getHeadings($fields)
	{
		$headings = array();

		foreach ($fields as $field)
		{
			// Use a different label if one has been specified by the label array
			// or a translation is required.
			if (isset($this->setup['Labels'][$field]))
			{
				$headingText = $this->setup['Labels'][$field];
			}
			elseif ($this->translateLabels)
			{
				$headingText = $this->setup['Translator']->get(
					$this->tableName . '_Field_' . $field);
			}
			else
			{
				$headingText = $field;
			}

			$headings[$field] = array(
				'div',
				array('class' => 'Heading_Item ' . $field . '_Field'),
				array('Text' => $headingText));
		}

		return $headings;
	}

	/// Get the primary keys for a row.
	protected function getPrimaryKeys()
	{
		return $this->primaryKeys;
	}

   
	/** Get the buttons for the row.
	 *  @param row \string The key of the row.
	 *  @return An array of button elements for the row.
	 */
	protected function getRowButtons($row)
	{
		$buttons = array();

		foreach ($this->rowButtons as $button)
		{
			$buttonElem = $this->app->getNew('Element', $button);
			$buttonElem->appendAttrib('name', '_' . $row);

			$buttons[] = $buttonElem;
		}

		return $buttons;
	}
   
	/** Determine if the row data is from the currently edited record.
	 *  @param rowData \array The data for the row.
	 *  \return \bool Whether the row is the currently edited record.
	 */
	protected function isEditedRecord($rowData)
	{
		if (empty($this->editedRecord))
		{
			return false;
		}

		// Check that every primary key matches.
		foreach ($this->editedRecord as $pKey => $pValue)
		{
			if (!isset($rowData[$pKey]) || $rowData[$pKey] !== $pValue)
			{
				return false;
			}
		}

		return true;
	}
}
// EOF