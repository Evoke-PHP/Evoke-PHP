<?php
namespace Evoke\View\XHTML\Control\RecordList;

use Evoke\View\Text\TranslatorIface,
	Evoke\View\ViewIface;

/**
 * RecordList
 *
 * View to represent a list of records.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class RecordList implements ViewIface
{
	/**
	 * Attributes for the content.
	 * @var mixed[]
	 */
	protected $contentAttribs;

	/**
	 * The data for the record list.
	 * @var mixed[]
	 */
	protected $data;

	/**
	 * Attributes for the data.
	 * @var mixed[]
	 */
	protected $dataAttribs;

	/**
	 * The edited record from the record list.
	 * @var mixed[]
	 */
	protected $editedRecord;

	/**
	 * Attributes for an empty record list.
	 * @var mixed[]
	 */
	protected $emptyDataAttribs;

	/**
	 * Fields in the record list.
	 * @var mixed[]
	 */
	protected $fields;

	/**
	 * The setup for the headings.
	 * @var mixed[]
	 */
	protected $headingSetup;

	/**
	 * Fields to be ignored in the record list.
	 * @var mixed[]
	 */
	protected $ignoredFields;

	/**
	 * Labels.
	 * @var string[]
	 */
	protected $labels;

	/**
	 * The primary keys for the record list.
	 * @var string[]
	 */
	protected $primaryKeys;

	/**
	 * Attributes for the record list rows.
	 * @var mixed[]
	 */
	protected $rowAttribs;

	/**
	 * Buttons for each row.
	 * @var mixed[]
	 */
	protected $rowButtons;

	/**
	 * Whether the row buttons should be in a form.
	 * @var bool
	 */
	protected $rowButtonsAsForm;

	/**
	 * Attributes for the row buttons.
	 * @var mixed[]
	 */
	protected $rowButtonsAttribs;

	/**
	 * The table name for the record data.
	 * @var string
	 */
	protected $tableName;

	/**
	 * Whether to translate the labels for the fields.
	 * @var bool
	 */
	protected $translateLabels;

	/**
	 * Translator object.
	 * @var Evoke\View\Text\TranslatorIface
	 */
	protected $translator;

	/**
	 * @todo Fix to new interface,
	 */
	public function __construct(Array $setup)
	{
		/// @todo Fix to new View interface.
		throw new \RuntimeException('Fix to new view interface.');

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

		if (!isset($data))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Data');
		}
      
		if (!isset($rowButtons))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Row_Buttons');
		}

		if (!isset($tableName))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Table_Name');
		}

		if (!$translator instanceof Translator)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Translator');
		}

		$this->data = $data;
      
		// Merge the Heading Setup so that information is added to a blank
		// heading setup whereas the default is for only a top heading.
		if (!empty($headingSetup))
		{
			$this->headingSetup = array_merge(
				array('Bottom'          => false,
				      'Buttons'         => array(),
				      'Buttons_Attribs' => array('class' => 'Heading_Buttons'),
				      'Inline'          => false,
				      'Row_Attribs'     => array('class' => 'Heading Row'),
				      'Top'             => false),
				$headingSetup);
		}
      
		parent::__construct(array('div',
		                          $this->attribs,
		                          $this->buildRecordListElems()));
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Get the view.
	 *
	 * @param mixed[] Parameters to the view.
	 */
	public function get(Array $params = array())
	{
		/** @todo Implement get.
		 */
		throw new \RuntimeException(__METHOD__ .  ' not yet implemented.');
	}
	
	/**
	 * Build the elements for the record list.
	 *
	 * @return mixed[] The elements in the record list.
	 */
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

	/**
	 * Build the content of the record list including any inline headings,
	 * data and data buttons. (Not the top or bottom headings).
	 *
	 * @param mixed[] Array of headings for use in inline headings.
	 *
	 * @return mixed[] Return the element containing the content of the record
	 *                 list.
	 */
	protected function buildContent($fields, $headings)
	{
		if (empty($this->data))
		{
			$rowElems = array(array('div',
			                        $this->rowAttribs,
			                        $this->buildEmptyData()));
		}
		else
		{
			$rowElems = array();
	 
			foreach ($this->data as $row => $rowData)
			{
				$rowElems[] = array(
					'div',
					$this->rowAttribs,
					array($this->buildRowData(
						      $fields, $row, $rowData, $headings),
					      $this->buildRowButtons($row, $rowData)));
			}
		}

		return array('div', array('class' => 'Content'), $rowElems);
	}

	/**
	 * Build the element for an empty record list.
	 *
	 * @param mixed[] The heading element that may be used.
	 *
	 * @return mixed[] Array of elements for an empty record list.
	 */
	protected function buildEmptyData()
	{
		return array('div',
		             $this->emptyDataAttribs,
		             $this->translator->get('No_Records_Found'));
	}
   
	/**
	 * Build the heading row element.
	 *
	 * @param mixed[] Array of heading row elements.
	 *
	 * @return mixed[] The heading row element.
	 */
	protected function buildHeadingRow($headings)
	{
		return array('div',
		             array('class' => 'Row'),
		             array(array('div',
		                         $this->dataAttribs,
		                         $headings),
		                   array('div',
		                         $this->headingSetup['Buttons_Attribs'],
		                         $this->getHeadingButtons())));
	}

	/**
	 * Build the element holding the buttons in a row.
	 *
	 * @param mixed   The key for the row.
	 * @param mixed[] The data for the row.
	 *
	 * @return mixed[] Array of elements that make up the buttons.
	 */
	protected function buildRowButtons($row, $rowData)
	{
		if ($this->rowButtonsAsForm)
		{
			return $this->app->getNew(
				'Element_Form_Hidden_Input',
				array('App'            => $this->app,
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
				      'Translator'     => $this->translator));
		}
		else
		{
			return array('div',
			             $this->rowButtonsAttribs,
			             $this->getRowButtons($row));
		}
	}
   
	/**
	 * Build the element holding the data in a row.
	 *
	 * @param string[] The fields for the row.
	 * @param mixed    The key for the row.
	 * @param mixed[]  The data for the row.
	 *
	 * @return mixed[] Array of elements that make up the data.
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
						array($headings[$field],
						      array('div',
						            array('class' => 'Data_Item ' . $field .
						                  '_Field'),
						            $rowData[$field])));
				}
			}
			else
			{
				foreach($fields as $field)
				{
					$rowElems[] = array(
						'div',
						array('class' => 'Data_Item ' . $field . '_Field'),
						$rowData[$field]);
				}
			}
	 
			return array('div',
			             $this->getDataAttribs($row, $rowData),
			             $rowElems);
		}
		catch (Exception $e)
		{
			throw new \Evoke\Exception(
				__METHOD__,
				' Caught exception processing row data: ' .
				var_export($rowData, true),
				$e);
		}
	}

	/**
	 * Get the data attributes for the row.
	 *
	 * @param mixed   The key of the row.
	 * @param mixed[] The data for the row.
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

	/**
	 * Get the fields for display.
	 *
	 * @return string[] The fields to be displayed.
	 */
	protected function getFields()
	{
		return $this->fields;
	}
   
	/**
	 * Get the heading buttons that appear with Top or Bottom headings.
	 *
	 * @return mixed[] The heading buttons.
	 */
	protected function getHeadingButtons()
	{
		return $this->headingSetup['Buttons'];
	}
   
	/**
	 * Build the headings for each field.
	 *
	 *  @param string[] The fields.
	 *
	 *  @return string[] Array of the heading elements keyed by field.
	 */
	protected function getHeadings($fields)
	{
		$headings = array();

		foreach ($fields as $field)
		{
			// Use a different label if one has been specified by the label array
			// or a translation is required.
			if (isset($this->labels[$field]))
			{
				$headingText = $this->labels[$field];
			}
			elseif ($this->translateLabels)
			{
				$headingText = $this->translator->get(
					$this->tableName . '_Field_' . $field);
			}
			else
			{
				$headingText = $field;
			}

			$headings[$field] = array(
				'div',
				array('class' => 'Heading ' . $field . '_Field'),
				$headingText);
		}

		return $headings;
	}

	/**
	 * Get the primary keys for a row.
	 *
	 * @return string[] The primary keys for the row.
	 */
	protected function getPrimaryKeys()
	{
		return $this->primaryKeys;
	}

   
	/**
	 * Get the buttons for the row.
	 *
	 * @param string The key of the row.
	 *
	 * @return mixed[] Button elements for the row.
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
   
	/**
	 * Determine if the row data is from the currently edited record.
	 * 
	 * @param mixed[] The data for the row.
	 * 
	 * @return bool Whether the row is the currently edited record.
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