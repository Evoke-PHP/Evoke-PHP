<?php
namespace Evoke\View\Form;
/** Entry form for a databse record.
 *  Provide a form to show and allow modification to database tables.
 */
class Entry extends \Evoke\View\Form
{
	/// Construct the entry form with the table information.
	public function __construct(Array $setup)
	{
		$setup += array('App'                 => NULL,
		                'Field_Attribs'       => array(),
		                'Field_Encasing'      => false,
		                'Field_Order'         => array(),
		                'Field_Prefix'        => '',
		                'Field_Prefix_Table'  => false,
		                'Field_Values'        => array(),
		                'Group_Headings'      => array(),
		                'Group_Heading_Class' => 'Group_Heading',
		                'Hidden'              => array(),
		                'Highlighted'         => array(),
		                'Ignored_Fields'      => array(),
		                'Labels'              => array(),
		                'Required_Indication' => array(),
		                'Table_Info'          => NULL,
		                'Table_Name'          => NULL,
		                'Table_Separator'     => '_T_',
		                'Translate_Labels'    => array(),
		                'Translator'          => NULL);

		/// \todo Remove dependency on App.
		throw new Exception(__METHOD__ .' Remove dependency on App');
		$app->needs(
			array('Instance' => array('Table_Info'    => $tableInfo,
			                          'Translator'    => $translator),
			      'Set'      => array('Table_Name' => $tableName)));
      
		if (!isset($translatePrefix))
		{
			$translatePrefix = $tableName . '_Field_';
		}

		if ($fieldPrefixTable === true)
		{
			$fieldPrefix =
				$tableName . $tableSeparator;
		}

		parent::__construct($setup);
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/// Build the elements for the entry form.
	protected function buildFormElements()
	{
		$description = $this->tableInfo->getDescription();

		// A keyed version of the description that is much more useful.
		$tableDescription = array();
      
		// Run through the description to create a useful description.
		foreach ($description as $rowInfo)
		{
			$tableDescription[$rowInfo['Field']] = $rowInfo;
		}

		// The elements that should be built in their correct order.
		$fieldOrder = array();

		// The field order is supplied by the caller or is defaulted to every
		// field in the table.
		if (!empty($this->fieldOrder))
		{
			$fieldOrder = $this->fieldOrder;
		}
		else
		{
			$fieldOrder = array_keys($tableDescription);
		}

		// Build the entry elements in the correct order.
		foreach ($fieldOrder as $groupName => $fieldDescription)
		{
			if (is_array($fieldDescription))
			{
				$groupElements = array();

				if (isset($this->groupHeadings[$groupName]))
				{
					$groupElements = array(
						array('h2',
						      array('class' => $this->groupHeadingClass),
						      $this->groupHeadings[$groupName]));
				}
	    
				foreach($fieldDescription as $field)
				{
					if (!in_array($field, $this->ignoredFields))
					{
						$groupElements[] = $this->buildRow(
							$this->buildInput(
								array_merge(
									array('Field_Info' => $tableDescription[$field]),
									$this->getFieldSetup($field))));
					}
				}

				$groupClass = 'Form_Element_Group';

				if (isset($this->highlighted[$groupName]))
				{
					$groupClass .= ' Highlighted';
				}
	    
				$this->addElement(
					array('div',
					      array('class' => $groupClass,
					            'id' => $groupName),
					      $groupElements));
			}
			elseif (!in_array($fieldDescription, $this->ignoredFields))
			{
				$fieldInfo = $tableDescription[$fieldDescription];
	    
				$this->addElement(
					$this->buildRow(
						$this->buildInput(
							array_merge(array('Field_Info' => $fieldInfo),
							            $this->getFieldSetup($fieldDescription))),
						isset($this->highlighted[$fieldDescription])));
			}
		}
	}

	/// Build any label and input elements.
	protected function buildInput($settings)
	{
		return array(
			$this->app->get(
				'Element_DB_Input',
				array_merge($settings,
				            array('Translator' => $this->translator))));
	}
      
	/// Build the row including any highlighting.
	protected function buildRow(Array $rowElems, $highlighted=false)
	{
		if (!$this->encasing)
		{
			return $rowElems;
		}
	 
		$encasingAttribs = $this->encasingAttribs;
      
		if ($highlighted)
		{
			if (isset($encasingAttribs['class']))
			{
				$encasingAttribs['class'] .= ' Highlighted';
			}
			else
			{
				$encasingAttribs['class'] = 'Highlighted';
			}
		}
      
		return array($this->encasingTag, $encasingAttribs, $rowElems);
	}

	protected function getFieldSetup($field)
	{
		$fieldSetup = array();

		$fieldMap = array(
			'Field_Attribs'    => 'Field_Attribs',
			'Field_Values'     => 'Field_Value',
			'Hidden'           => 'Hidden',
			'Highlighted'      => 'Highlighted',
			'Labels'           => 'Label',
			'Translate_Labels' => 'Translate_Label');

		// Transform the setup from the array grouped to the specific field used
		// in the element for the DB input.
		foreach ($fieldMap as $grouped => $specific)
		{
			if (isset($this->setup[$grouped][$field]))
			{
				$fieldSetup[$specific] = $this->setup[$grouped][$field];
			}
		}

		// Deal with special settings.
		// Encasing
		if (isset($this->fieldEncasing[$field]))
		{
			$fieldSetup['Encasing'] = $this->fieldEncasing[$field];
		}
		elseif (isset($this->fieldEncasing))
		{
			$fieldSetup['Encasing'] = $this->fieldEncasing;
		}

		// Field_Prefix
		$fieldSetup['Field_Prefix'] = $this->fieldPrefix;
	 
		if (is_array($this->fieldPrefix) &&
		    array_key_exists($field, $this->fieldPrefix))
		{
			$fieldSetup['Field_Prefix'] = $this->fieldPrefix[$field];
		}

		// Required_Indication
		if (isset($this->requiredIndication) &&
		    isset($this->requiredIndication[$field]))
		{
			$fieldSetup['Required_Indication'] =
				$this->requiredIndication[$field];
		}
		elseif (isset($this->requiredIndication) &&
		        ($this->requiredIndication === false))
		{
			$fieldSetup['Required_Indication'] = false;
		}

		// Translate_Prefix
		if (isset($this->translatePrefix))
		{
			$fieldSetup['Translate_Prefix'] = $this->translatePrefix;
		}

		return $fieldSetup;
	}
}
// EOF