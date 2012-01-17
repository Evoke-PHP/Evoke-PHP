<?php
namespace Evoke;
/** \abstract form class providing a generic interface to write a form.
 *  Elements are added to the Form using the \ref setElements method which
 *  must be defined by derived classes.  The form is written using the
 *  \ref write method.
 */
abstract class Element_Form extends Element
{
   /** The elements that should be written to make the form. These must be
    *  added to using the \ref addElement method.
    */
   private $elements = array();

   /// The form setup options.
   protected $setup;
	    
   /// Construct the Form object.
   public function __construct(Array $setup)
   {
      $this->setup = array_merge(
	 array('Append_Elements'       => array(),
	       'Default_Attribs'       => array('action' => '',
						'method' => 'post'),
	       'Encasing'              => true,
	       'Encasing_Tag'          => 'div',
	       'Encasing_Attribs'      => array('class' => 'Form_Row'),
	       'Prepend_Elements'      => array(),
	       'Submit_Button_Attribs' => array('class' => 'Form_Buttons'),
	       'Submit_Buttons'        => array()),
	 $setup);

      $this->buildFormChildren();

      parent::__set(array('form',
			  array(),
			  array('Children' => $this->elements)));
   }

   /*********************/
   /* Protected Methods */
   /*********************/

   /** Add an element to the Form.
    *  @param elem \mixed The element or elements to add to the form.
    */
   protected function addElement($elem)
   {
      $this->elements[] = $elem;
   }

   /// Build elements at the end of the form (but before the buttons).
   protected function buildAppendElements()
   {
      foreach ($this->setup['Append_Elements'] as $appendElement)
      {
	 $this->addElement($appendElement);
      }
   }

   /// Build the buttons that are contained in the form.
   protected function buildFormButtons()
   {
      $this->addElement(
	 array('div',
	       $this->setup['Submit_Button_Attribs'],
	       array('Children' => $this->setup['Submit_Buttons'])));
   }

   /// Build all of the elements that make up the form.
   protected function buildFormChildren()
   {
      $this->buildPrependElements();
      $this->buildFormElements();
      $this->buildAppendElements();
      $this->buildFormButtons();
   }

   /// \abstract function to build the non button elements of the form.
   abstract protected function buildFormElements();

   /// Build elements at the start of the form.
   protected function buildPrependElements()
   {
      foreach ($this->setup['Prepend_Elements'] as $prependElement)
      {
	 $this->addElement($prependElement);
      }
   }
   
   /** Build a row for the form.
    *  @param rowElems \mixed An element or array of elements for the row.
    */
   protected function buildRow(Array $rowElems)
   {
      if (!$this->setup['Encasing'])
      {
	 return $rowElems;
      }

      return array($this->setup['Encasing_Tag'],
		   $this->setup['Encasing_Attribs'],
		   array('Children' => $rowElems));
   }
}
// EOF