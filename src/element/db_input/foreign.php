<?php


class Element_DB_Input_Foreign extends Element_DB_Input
{
   public function __construct(Array $setup)
   {
      /// \todo Fix this due to Monumental
      throw new Exception('Fix this after monumental changes.');
      
      $setup = array_merge(
	 array('Data'             => NULL,
	       'Field'            => 'UNSET_FIELD',
	       'Field_Info'       => NULL,
	       'Field_Values'     => array(),
	       'Foreign_Selector' => array('Append_Data'  => array(),
					   'Prepend_Data' => array()),
	       'Selected_Fields'  => array(
		  'Field'          => 'SF_UNSET_FIELD',
		  'Selector_Field' => 'SF_UNSET_SELECTOR_FIELD')),
	 $setup);
      
      parent::__construct($setup);
   }
   
   /*********************/
   /* Protected Methods */
   /*********************/

   protected function getElements()
   {
      $fieldInfo = $this->setup['Field_Info'];
      
      if (empty($data))
      {
	 $elems = array($this->buildLabel($field));

	 if ($this->setup['Required_Indication'])
	 {
	    $elems[] = $this->buildRequiredIndication(
	       $this->setup['Foreign_Selector']['Required']);
	 }

	 $elems[] = array(
	    'span',
	    array('class' => 'Empty_Foreign_Data'),
	    array('Text' => $this->setup['Translator']->get(
		     'No_Foreign_Table_Data')));

	 return $elems;
      }

      $optionElements = array();
      
      $data = array_merge(
	 $this->setup['Foreign_Selector']['Prepend_Data'],
	 $data,
	 $this->setup['Foreign_Selector']['Append_Data']);
      
      foreach ($data as $forKeyData)
      {
	 $attribs = array(
	    'value' => $forKeyData[
	       $this->setup['Selected_Fields']['Field']]);
	 $options = array(
	    'Text' => $forKeyData[
	       $this->setup['Selected_Fields']['Selector_Field']]);
	 
	 if (isset($this->setup['Field_Values'][$field]) &&
	     ($this->setup['Field_Values'][$field] ===
	      $forKeyData[
		 $this->setup['Selected_Fields']['Field']]))
	 {
	    $attribs = array_merge($attribs, array('selected' => 'selected'));
	 }
	 
	 $optionElements[] = array('option', $attribs, $options);
      }
      
      if (isset($this->setup['Field_Attribs'][$field]))
      {
	 $attribArr = $this->setup['Field_Attribs'][$field];
      }
      else
      {
	 $attribArr = array();
      }
      
      if (isset($this->setup['Highlighted_Fields'][$field]))
      {
	 if (isset($attribArr['class']))
	 {
	    $attribArr['class'] .= ' Highlighted';
	 }
	 else
	 {
	    $attribArr['class'] = 'Highlighted';
	 }
      }
      
      return array(
	 $this->buildLabel($field),
	 $this->buildRequiredIndication(
	    $this->setup['Foreign_Selector']['Required']),
	 array(
	    'select',
	    array_merge($attribArr, array('name' => $field)),
	    array('Children' => $optionElements)));
   }
}

// EOF