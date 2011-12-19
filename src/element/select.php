<?php


class Element_Select extends Element
{
   public function __construct(Array $setup)
   {
      $setup += array('Append_Data'    => array(),
		      'Attribs'        => array(),
		      'Data'           => array(),
		      'Option_Attribs' => array(),
		      'Prepend_Data'   => array(),
		      'Selected_Value' => NULL,
		      'Text_Field'     => NULL,
		      'Value_Field'    => 'ID');

      $optionElements = array();

      $fullData = array_merge($setup['Prepend_Data'],
			      $setup['Data'],
			      $setup['Append_Data']);
	

      foreach ($fullData as $record)
      {
	 $value = $record[$setup['Value_Field']];
	 
	 $optionAttribs = array_merge($setup['Option_Attribs'],
				      array('value' => $value));

	 if ($value == $setup['Selected_Value'])
	 {
	    $optionAttribs['selected'] = 'selected';
	 }
	 
	 $optionElements[] =
	    array('option',
		  $optionAttribs,
		  array('Text' => $record[$setup['Text_Field']]));
      }

      parent::__construct(array('select',
				$setup['Attribs'],
				array('Children' => $optionElements)));
   }
}

// EOF