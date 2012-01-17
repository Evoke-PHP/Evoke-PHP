<?php
namespace Evoke;
/** Entry form for a databse record using foreign table inputs.
 *  Provide a form to show and allow modification to database tables.
 */
class Element_Form_Entry_Foreign extends Element_Form_Entry
{
   /// Construct the entry form with the table information.
   public function __construct($setup=array())
   {
      $setup = array_merge(
	 array('App'                   => NULL,
	       'Foreign_Selector'      => array(),
	       'Ignore_As_Foreign_Key' => array(),
	       'SQL'                   => NULL),
	 $setup);

      /// \todo Change this. SQL in an element is a bad idea.
      throw new Exception(__METHOD__ . ' SQL in an element is a bad idea.');
      
      if (!($setup['SQL'] instanceof \Evoke\DB\SQL))
      {
	 $setup['SQL'] = new SQL();
      }

      parent::__construct($setup);
   }

   /*********************/
   /* Protected Methods */
   /*********************/

   /** Build a row from the database for the form.
    *  Determine whether the row should be processed as a foreign input or as
    *  a standard database element.
    */
   protected function buildInput($description, $fieldSetup)
   {
      $field = $description['Field'];
      $foreignKeys = $this->setup['Table_Info']->getForeignKeys();
	    
      if (strtoupper($description['Key'] !== 'MUL') ||
	  !isset($foreignKeys[$field]) ||
	  isset($this->setup['Ignore_As_Foreign_Key'][$field]))
      {
	 return parent::buildInput($description, $fieldSetup);
      }
      
      return $this->buildForeignInput($description, $fieldSetup, $foreignKeys);
   }

   /** Build the foreign input to the form.
    *  Get the foreign field information and return the foreign element.
    */
   private function buildForeignInput($description, $fieldSetup, $foreignKeys)
   {	 
      $field = $description['Field'];
      $elems = array();
      
      if (!isset($this->setup['Foreign_Selector'][$field]))
      {
	 throw new \Exception(
	    __METHOD__ . 'No Foreign Selector for field: ' . $field);
      }

      $foreignSelector =
	 array_merge(
	    array('Append_Data'  => array(),
		  'Conditions'   => array(),
		  'Field'        => 'UNDEFINED_FOREIGN_SELECTOR_FIELD',
		  'Order_By'     => array(),
		  'Prepend_Data' => array(),
		  'Required'     => false),
	    $this->setup['Foreign_Selector'][$field]);
      
      $selectedFields = array(
	 'Field'          => $foreignKeys[$field]['Foreign_Field'],
	 'Selector_Field' => $foreignSelector['Field']);

      $foreignKeyData = $this->setup['SQL']->select(
	 $foreignKeys[$field]['Foreign_Table'],
	 $selectedFields,
	 $foreignSelector['Conditions'],
	 $foreignSelector['Order_By']);

      return array(
	 new Element_DB_Input_Foreign(
	    $foreignKeyData,
	    array_merge(
	       $fieldSetup,
	       array('Field'               => $field,
		     'Foreign_Selector'    => $foreignSelector,
		     'Selected_Fields'     => $selectedFields))));
   }

}

// EOF