<?php
namespace Evoke;

class Element_Form_Hidden_Input extends Element_Form
{
   protected $record;
   protected $primaryKeys;
   
   public function __construct(Array $setup)
   {
      $setup += array('App'            => NULL,
		      'Data'           => NULL,
		      'Encasing'       => false,
		      'Ignored_Fields' => array(),
		      'Primary_Keys'   => NULL,
		      'Submit_Buttons' => NULL,
		      'Translator'     => NULL);
      
      $setup['App']->needs(
	 array('Instance' => array('Translator' => $setup['Translator']),
	       'Set'      => array(
		  'Data'           => $setup['Data'],
		  'Primary_Keys'   => $setup['Primary_Keys'],
		  'Submit_Buttons' => $setup['Submit_Buttons'])));
      
      $this->record = $setup['Data'];
      $this->primaryKeys = $setup['Primary_Keys'];
      parent::__construct($setup);
   }

   /*********************/
   /* Protected Methods */
   /*********************/

   /// Build the non button elements of the form (the hidden inputs).
   protected function buildFormElements()
   {
      foreach ($this->primaryKeys as $key)
      {
	 if (!in_array($key, $this->setup['Ignored_Fields']))
	 {
	    $this->addElement(
	       array('input',
		     array('type'  => 'hidden',
			   'name'  => $key,
			   'value' => $this->record[$key])));
	 }
      }
   }
}
// EOF