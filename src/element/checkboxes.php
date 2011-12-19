<?php


/// Element_Checkboxes
class Element_Checkboxes extends Element
{ 
   public function __construct(Array $setup)
   {
      $setup += array(
	 'App'              => NULL,
	 'Checkboxes'       => NULL,
	 'Display_Field'    => NULL,
	 'ID_Field'         => 'ID',
	 'Fieldset_Attribs' => array('class' => 'Checkbox_Group'),
	 'Prefix'           => '',
	 'Selected'         => NULL,
	 'Translator'       => NULL);

      $setup['App']->needs(
	 array('Instance' => array('Translator' => $setup['Translator']),
	       'Set'      => array('Checkboxes' => $setup['Checkboxes'],
				   'Selected'   => $setup['Selected'])));

      if (!isset($setup['Empty_Text']))
      {
	 $setup['Empty_Text'] = $setup['Translator']->get('No_Checkboxes');
      }
      
      if (empty($setup['Checkboxes']))
      {
	 // Build an element to show that there are no checkboxes defined.
	 parent::__construct(
	    array('div',
		  array('class' => 'Group_Container'),
		  array('Children' => array(
			   array('div',
				 array('class' => 'No_Elements'),
				 array('Text' => $setup['Empty_Text']))))));
      }
      else
      {
	 $checkboxElems = array();
	 
	 foreach ($setup['Checkboxes'] as $checkbox)
	 {
	    $id = $setup['Prefix'] . $checkbox[$setup['ID_Field']];
	    $isSelected = array();
	    
	    if (in_array($checkbox[$setup['ID_Field']], $setup['Selected']))
	    {
	       $isSelected = array('checked' => 'checked');
	    }
	    	    
	    $checkboxElems[] = array(
	       'div',
	       array('class' => 'Encasing'),
	       array('Children' => array(
			array(
			   'label',
			   array('for' => $id),
			   array('Text' => $checkbox[$setup['Display_Field']])),
			array(
			   'input',
			   array_merge(array('type' => 'checkbox',
					     'id'   => $id,
					     'name' => $id),
				       $isSelected)))));
	 }
	 
	 // Create the form to make the category selections.
	 parent::__construct(array('fieldset',
				   $setup['Fieldset_Attribs'],
				   array('Children' => $checkboxElems)));
      }
   }
}
      
// EOF