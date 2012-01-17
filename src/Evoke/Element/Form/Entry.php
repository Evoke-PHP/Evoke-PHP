<?php
namespace Evoke;
/** Entry form for a databse record.
 *  Provide a form to show and allow modification to database tables.
 */
class Element_Form_Entry extends Element_Form
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
      $setup['App']->needs(
	 array('Instance' => array('Table_Info'    => $setup['Table_Info'],
				   'Translator'    => $setup['Translator']),
	       'Set'      => array('Table_Name' => $setup['Table_Name'])));
      
      if (!isset($setup['Translate_Prefix']))
      {
	 $setup['Translate_Prefix'] = $setup['Table_Name'] . '_Field_';
      }

      if ($setup['Field_Prefix_Table'] === true)
      {
	 $setup['Field_Prefix'] =
	    $setup['Table_Name'] . $setup['Table_Separator'];
      }

      parent::__construct($setup);
   }

   /*********************/
   /* Protected Methods */
   /*********************/

   /// Build the elements for the entry form.
   protected function buildFormElements()
   {
      $description = $this->setup['Table_Info']->getDescription();

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
      if (!empty($this->setup['Field_Order']))
      {
	 $fieldOrder = $this->setup['Field_Order'];
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

	    if (isset($this->setup['Group_Headings'][$groupName]))
	    {
	       $groupElements = array(
		  array(
		     'h2',
		     array('class' => $this->setup['Group_Heading_Class']),
		     array('Text' => $this->setup['Group_Headings'][$groupName])
		     ));
	    }
	    
	    foreach($fieldDescription as $field)
	    {
	       if (!in_array($field, $this->setup['Ignored_Fields']))
	       {
		  $groupElements[] = $this->buildRow(
		     $this->buildInput(
			array_merge(
			   array('Field_Info' => $tableDescription[$field]),
			   $this->getFieldSetup($field))));
	       }
	    }

	    $groupClass = 'Form_Element_Group';

	    if (isset($this->setup['Highlighted'][$groupName]))
	    {
	       $groupClass .= ' Highlighted';
	    }
	    
	    $this->addElement(
	       array(
		  'div',
		  array('class' => $groupClass,
			'id' => $groupName),
		  array('Children' => $groupElements)));
	 }
	 elseif (!in_array($fieldDescription, $this->setup['Ignored_Fields']))
	 {
	    $fieldInfo = $tableDescription[$fieldDescription];
	    
	    $this->addElement(
	       $this->buildRow(
		  $this->buildInput(
		     array_merge(array('Field_Info' => $fieldInfo),
				 $this->getFieldSetup($fieldDescription))),
		  isset($this->setup['Highlighted'][$fieldDescription])));
	 }
      }
   }

   /// Build any label and input elements.
   protected function buildInput($settings)
   {
      return array(
	 $this->setup['App']->get(
	    'Element_DB_Input',
	    array_merge($settings,
			array('Translator' => $this->setup['Translator']))));
   }
      
   /// Build the row including any highlighting.
   protected function buildRow(Array $rowElems, $highlighted=false)
   {
      if (!$this->setup['Encasing'])
      {
	 return $rowElems;
      }
	 
      $encasingAttribs = $this->setup['Encasing_Attribs'];
      
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
      
      return array($this->setup['Encasing_Tag'],
			 $encasingAttribs,
			 array('Children' => $rowElems));
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
      if (isset($this->setup['Field_Encasing'][$field]))
      {
	 $fieldSetup['Encasing'] = $this->setup['Field_Encasing'][$field];
      }
      elseif (isset($this->setup['Field_Encasing']))
      {
	 $fieldSetup['Encasing'] = $this->setup['Field_Encasing'];
      }

      // Field_Prefix
      $fieldSetup['Field_Prefix'] = $this->setup['Field_Prefix'];
	 
      if (is_array($this->setup['Field_Prefix']) &&
	  array_key_exists($field, $this->setup['Field_Prefix']))
      {
	 $fieldSetup['Field_Prefix'] = $this->setup['Field_Prefix'][$field];
      }

      // Required_Indication
      if (isset($this->setup['Required_Indication']) &&
	  isset($this->setup['Required_Indication'][$field]))
      {
	 $fieldSetup['Required_Indication'] =
	    $this->setup['Required_Indication'][$field];
      }
      elseif (isset($this->setup['Required_Indication']) &&
	      ($this->setup['Required_Indication'] === false))
      {
	 $fieldSetup['Required_Indication'] = false;
      }

      // Translate_Prefix
      if (isset($this->setup['Translate_Prefix']))
      {
	 $fieldSetup['Translate_Prefix'] = $this->setup['Translate_Prefix'];
      }

      return $fieldSetup;
   }
}
// EOF